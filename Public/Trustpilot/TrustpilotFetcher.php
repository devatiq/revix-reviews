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

        // Ensure UTF-8 and better parsing
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        @$doc->loadHTML($html);
        $xpath = new \DOMXPath($doc);

        $review_elements = $xpath->query("//article[contains(@class,'styles_reviewCard')]");
        $reviews = [];

        if (self::DEBUG && $review_elements->length === 0) {
            echo "<!-- Trustpilot: no styles_reviewCard found -->";
        }

        foreach ($review_elements as $index => $element) {
            if ($index >= $count) break;

            $author = $xpath->query(".//span[@data-consumer-name-typography='true']", $element);
            $country = $xpath->query(".//span[@data-consumer-country-typography='true']", $element);
            $avatar = $xpath->query(".//img[@data-consumer-avatar-image='true']", $element);
            $title = $xpath->query(".//h2[@data-service-review-title-typography='true']", $element);
            $content = $xpath->query(".//p[@data-service-review-text-typography='true']", $element);
            $rating = $xpath->query(".//img[contains(@alt,'Rated')]", $element);
            $date = $xpath->query(".//time", $element);

            // Fallback for missing content
            $text = $content->length ? trim($content->item(0)->nodeValue) : '';
            if (empty($text)) {
                $altContent = $xpath->query(".//div[@data-review-content='true']//p", $element);
                $text = $altContent->length ? trim($altContent->item(0)->nodeValue) : '';
            }

            $reviews[] = [
                'author'  => $author->length ? trim($author->item(0)->nodeValue) : 'Anonymous',
                'country' => $country->length ? trim($country->item(0)->nodeValue) : '',
                'avatar'  => $avatar->length ? $avatar->item(0)->getAttribute('src') : '',
                'title'   => $title->length ? trim($title->item(0)->nodeValue) : '',
                'text'    => $text,
                'rating'  => $rating->length ? $rating->item(0)->getAttribute('alt') : '',
                'date'    => $date->length ? $date->item(0)->getAttribute('datetime') : '',
            ];
        }

        // Only cache if there's actual content and not all are empty
        if (self::ENABLE_CACHE && !empty($reviews)) {
            $valid = array_filter($reviews, fn($r) => $r['text'] !== '(No content found)');
            if (!empty($valid)) {
                set_transient($cache_key, $valid, 12 * HOUR_IN_SECONDS);
            }
        }

        return $reviews;
    }
}