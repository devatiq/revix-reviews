<?php
namespace RevixReviews\Public\Trustpilot;

class TrustpilotFetcher {
    const ENABLE_CACHE = false;
    const DEBUG = true;

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

        // Broader selector
        $review_elements = $xpath->query("//div[contains(@class,'styles_cardWrapper') or contains(@class,'review-card')]");
        $reviews = [];

        if (self::DEBUG && $review_elements->length === 0) {
            echo "<!-- Trustpilot: no styles_cardWrapper or review-card found -->";
        }

        foreach ($review_elements as $index => $element) {
            if ($index >= $count) break;

            $authorNode = $xpath->query(".//*[contains(@class,'consumerInformation__name') or contains(@class,'typography_heading')]", $element);
            $textNode = $xpath->query(".//*[contains(@class,'review-content__text') or contains(@class,'typography_body')]", $element);
            $ratingNode = $xpath->query(".//img[contains(@alt,'stars')]", $element);
            $dateNode = $xpath->query(".//time", $element);
            $avatarNode = $xpath->query(".//img[contains(@class,'consumerAvatar') or contains(@alt,'Profile') or contains(@alt,'avatar')]", $element);


            $reviews[] = [
                'author' => $authorNode->length ? trim($authorNode->item(0)->nodeValue) : 'Anonymous',
                'text'   => $textNode->length ? trim($textNode->item(0)->nodeValue) : '(No content found)',
                'rating' => $ratingNode->length ? $ratingNode->item(0)->getAttribute('alt') : 'No rating',
                'date'   => $dateNode->length ? $dateNode->item(0)->getAttribute('datetime') : '',
                'avatar' => $avatarNode->length ? $avatarNode->item(0)->getAttribute('src') : '',
            ];
        }

        if (self::ENABLE_CACHE && !empty($reviews)) {
            set_transient($cache_key, $reviews, 12 * HOUR_IN_SECONDS);
        }

        return $reviews;
    }
}