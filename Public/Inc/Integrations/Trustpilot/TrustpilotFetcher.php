<?php
namespace RevixReviews\Public\Inc\Integrations\Trustpilot;
class TrustpilotFetcher
{
    const ENABLE_CACHE = true; // Cache enabled for performance
    const DEBUG = false; // Debug disabled
    const MAX_PAGES = 10;

    /**
     * Clear all Trustpilot review caches
     */
    public static function clear_cache() {
        global $wpdb;
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_revix_trustpilot_%'");
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_revix_trustpilot_%'");
    }

    public function get_reviews($count = 5, $minRating = 0)
    {
        $base_url = get_option('revix_trustpilot_url');
        if (!$base_url)
            return [];

        // Include version in cache key to bust old cache when code changes
        $cache_version = '2.0'; // Increment this when text extraction logic changes
        $cache_key = 'revix_trustpilot_reviews_cache_v' . $cache_version . '_' . md5($base_url . $count . $minRating);
        
        if (self::ENABLE_CACHE) {
            $cached = get_transient($cache_key);
            if ($cached && !empty($cached))
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
                $rating = $xpath->query(".//img[contains(@alt,'Rated')]", $element);
                $date = $xpath->query(".//time", $element);
                
                // Multiple strategies to extract review text
                $text = '';
                
                // Strategy 1: Standard data attribute
                $content = $xpath->query(".//p[@data-service-review-text-typography='true']", $element);
                if ($content->length > 0) {
                    $text = trim($content->item(0)->nodeValue);
                }
                
                // Strategy 2: Review content div
                if (empty($text)) {
                    $content = $xpath->query(".//div[@data-review-content='true']//p", $element);
                    if ($content->length > 0) {
                        $text = trim($content->item(0)->nodeValue);
                    }
                }
                
                // Strategy 3: Class-based selector
                if (empty($text)) {
                    $content = $xpath->query(".//div[contains(@class,'styles_reviewContent')]//p", $element);
                    if ($content->length > 0) {
                        $text = trim($content->item(0)->nodeValue);
                    }
                }
                
                // Strategy 4: Look for data-service-review-text attribute
                if (empty($text)) {
                    $content = $xpath->query(".//*[@data-service-review-text-typography]", $element);
                    if ($content->length > 0) {
                        $text = trim($content->item(0)->nodeValue);
                    }
                }
                
                // Strategy 5: Find the longest paragraph (likely the review)
                if (empty($text)) {
                    $allParagraphs = $xpath->query(".//p", $element);
                    $longestText = '';
                    foreach ($allParagraphs as $p) {
                        $pText = trim($p->nodeValue);
                        // Skip if it's rating text, date, or very short
                        if (strlen($pText) > strlen($longestText) && 
                            !preg_match('/^(Rated|Date of experience|Reply from|Verified)/i', $pText) &&
                            strlen($pText) > 20) {
                            $longestText = $pText;
                        }
                    }
                    $text = $longestText;
                }
                
                // Strategy 6: Look for any div with review text patterns
                if (empty($text)) {
                    $allDivs = $xpath->query(".//div", $element);
                    foreach ($allDivs as $div) {
                        $divText = trim($div->nodeValue);
                        // Look for text that looks like a review (longer than author name, etc)
                        if (strlen($divText) > 50 && strlen($divText) < 5000) {
                            // Make sure it doesn't contain child elements text we already checked
                            $hasChildren = $div->childNodes->length > 1;
                            if (!$hasChildren || strpos($divText, 'Rated') === false) {
                                $text = $divText;
                                break;
                            }
                        }
                    }
                }

                preg_match('/([0-9]+(?:\\.[0-9])?)/', $rating->length ? $rating->item(0)->getAttribute('alt') : '', $matches);
                $ratingValue = isset($matches[1]) ? floatval($matches[1]) : 0;

                if ($ratingValue < $minRating) {
                    continue;
                }

                // Debug: Log what we found
                if (self::DEBUG) {
                    $authorName = $author->length ? trim($author->item(0)->nodeValue) : 'Unknown';
                    error_log('Revix Trustpilot: Processing review by ' . $authorName);
                    error_log('Revix Trustpilot: Text length = ' . strlen($text) . ' chars');
                    if (empty($text)) {
                        error_log('Revix Trustpilot: WARNING - No text found for this review!');
                    }
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