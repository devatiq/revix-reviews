<?php
namespace RevixReviews\Public\Trustpilot;

class TrustpilotFetcher {
    const ENABLE_CACHE = false; // Toggle for production
    const DEBUG = true; // Show debug info in HTML comment

    public function get_reviews($count = 5) {
        $url = get_option('revix_trustpilot_url');
        if (!$url) return [];

        $cache_key = 'revix_trustpilot_reviews_cache';

        if (self::ENABLE_CACHE) {
            $cached = get_transient($cache_key);
            if ($cached) return $cached;
        }

        $response = wp_remote_get($url);
        if (is_wp_error($response)) {
            if (self::DEBUG) echo "<!-- Trustpilot error: " . esc_html($response->get_error_message()) . " -->";
            return [];
        }

        $html = wp_remote_retrieve_body($response);
        if (empty($html)) {
            if (self::DEBUG) echo "<!-- Trustpilot: empty response body -->";
            return [];
        }

        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        @$doc->loadHTML($html);
        $xpath = new \DOMXPath($doc);

        $review_elements = $xpath->query("//section[contains(@class,'styles_reviewCard')]");
        $reviews = [];

        if (self::DEBUG && $review_elements->length === 0) {
            echo "<!-- Trustpilot: no .styles_reviewCard found -->";
        }

        foreach ($review_elements as $index => $element) {
            if ($index >= $count) break;

            $author = $xpath->query(".//div[contains(@class,'consumerInformation__name')]", $element);
            $text = $xpath->query(".//p[contains(@class,'review-content__text')]", $element);
            $rating = $xpath->query(".//img[contains(@alt,'stars')]", $element);
            $date = $xpath->query(".//time", $element);

            $reviews[] = [
                'author' => $author->length ? trim($author->item(0)->nodeValue) : 'Anonymous',
                'text'   => $text->length ? trim($text->item(0)->nodeValue) : '(No review content)',
                'rating' => $rating->length ? $rating->item(0)->getAttribute('alt') : 'No rating',
                'date'   => $date->length ? $date->item(0)->getAttribute('datetime') : '',
            ];
        }

        if (self::ENABLE_CACHE && !empty($reviews)) {
            set_transient($cache_key, $reviews, 12 * HOUR_IN_SECONDS);
        }

        return $reviews;
    }
}