<?php
namespace RevixReviews\Public\Inc\Integrations\Trustpilot;
class TrustpilotFetcher
{
    const ENABLE_CACHE = true;
    const DEBUG = false;
    const MAX_PAGES = 10;

    public function get_reviews($count = 5, $minRating = 0)
    {
        $base_url = get_option('revix_trustpilot_url');
        if (!$base_url)
            return [];

        $cache_key = 'revix_trustpilot_reviews_cache_' . md5($base_url . $count . $minRating);
        if (self::ENABLE_CACHE) {
            $cached = get_transient($cache_key);
            if ($cached)
                return $cached;
        }

        $reviews = [];
        $found = 0;

        for ($page = 1; $page <= self::MAX_PAGES && $found < $count; $page++) {
            $url = add_query_arg([
                'page' => $page,
                'stars' => $minRating >= 1 ? (int) $minRating : null,
            ], $base_url);

            $response = wp_remote_get($url);
            if (is_wp_error($response)) {
                if (self::DEBUG) echo "<!-- Trustpilot error: " . esc_html($response->get_error_message()) . " -->";
                break;
            }

            $html = wp_remote_retrieve_body($response);
			if (empty($html)) {
				if (self::DEBUG) {
					echo "<!-- Trustpilot: empty response body on page " . esc_html($page) . " -->";
				}
				break;
			}

            $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
            libxml_use_internal_errors(true);
            $doc = new \DOMDocument();
            @$doc->loadHTML($html);
            $xpath = new \DOMXPath($doc);

            $review_elements = $xpath->query("//article[contains(@class,'styles_reviewCard')]");
			if ($review_elements->length === 0) {
				if (self::DEBUG) {
					echo "<!-- No reviews found on page " . esc_html($page) . " -->";
				}
				break;
			}

            foreach ($review_elements as $element) {
                if ($found >= $count) break;

                $author = $xpath->query(".//span[@data-consumer-name-typography='true']", $element);
                $country = $xpath->query(".//span[@data-consumer-country-typography='true']", $element);
                $avatar = $xpath->query(".//img[@data-consumer-avatar-image='true']", $element);
                $title = $xpath->query(".//h2[@data-service-review-title-typography='true']", $element);
                $content = $xpath->query(".//p[@data-service-review-text-typography='true']", $element);
                $rating = $xpath->query(".//img[contains(@alt,'Rated')]", $element);
                $date = $xpath->query(".//time", $element);

                preg_match('/([0-9]+(?:\\.[0-9])?)/', $rating->length ? $rating->item(0)->getAttribute('alt') : '', $matches);
                $ratingValue = isset($matches[1]) ? floatval($matches[1]) : 0;

                if ($ratingValue < $minRating) {
                    continue;
                }

                $text = $content->length ? trim($content->item(0)->nodeValue) : '';
                if (empty($text)) {
                    $altContent = $xpath->query(".//div[@data-review-content='true']//p", $element);
                    $text = $altContent->length ? trim($altContent->item(0)->nodeValue) : '';
                }

                $reviews[] = [
                    'author' => $author->length ? trim($author->item(0)->nodeValue) : esc_html__('Anonymous', 'revix-reviews'),
                    'country' => $country->length ? trim($country->item(0)->nodeValue) : '',
                    'avatar' => $avatar->length ? $avatar->item(0)->getAttribute('src') : '',
                    'title' => $title->length ? trim($title->item(0)->nodeValue) : '',
                    'text' => $text,
                    'rating' => $rating->length ? $rating->item(0)->getAttribute('alt') : '',
                    'date' => $date->length ? $date->item(0)->getAttribute('datetime') : '',
                ];
                $found++;
            }
        }

        if (self::ENABLE_CACHE && !empty($reviews)) {
            set_transient($cache_key, $reviews, 12 * HOUR_IN_SECONDS);
        }

        return $reviews;
    }
}