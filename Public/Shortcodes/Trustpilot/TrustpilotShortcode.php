<?php
namespace RevixReviews\Public\Shortcodes\Trustpilot;

use RevixReviews\Public\Inc\Integrations\Trustpilot\TrustpilotFetcher;
class TrustpilotShortcode
{
    public function __construct()
    {
        add_shortcode('revix_trustpilot_reviews', [$this, 'render']);
    }

    public function render($atts)
    {
        $atts = shortcode_atts([
            'count' => 15,
            'min_rating' => 0,
            'max_rating' => 5,
            'debug' => 'false'
        ], $atts);

        $debug = ($atts['debug'] === 'true');
        
        // Check if URL is configured
        $trustpilot_url = get_option('revix_trustpilot_url');
        if (empty($trustpilot_url)) {
            $message = __('Trustpilot URL not configured. Please set it in the WordPress dashboard under Revix Reviews > Trustpilot tab.', 'revix-reviews');
            if ($debug) {
                return '<div class="revix-trustpilot-error" style="padding:15px;background:#fee;border:1px solid #c33;color:#c33;">' . esc_html($message) . '</div>';
            }
            return '<!-- ' . esc_html($message) . ' -->';
        }

        $fetcher = new TrustpilotFetcher();
        $reviews = $fetcher->get_reviews($atts['count'] + 4, floatval($atts['min_rating']));

        if ($debug) {
            error_log('Revix Trustpilot Debug: URL = ' . $trustpilot_url);
            error_log('Revix Trustpilot Debug: Reviews fetched = ' . count($reviews));
        }

        if (empty($reviews)) {
            $message = sprintf(
                __('No Trustpilot reviews found. URL configured: %s', 'revix-reviews'),
                $trustpilot_url
            );
            if ($debug) {
                return '<div class="revix-trustpilot-error" style="padding:15px;background:#fee;border:1px solid #c33;color:#c33;">' . esc_html($message) . '</div>';
            }
            return '<!-- ' . esc_html($message) . ' -->';
        }

        ob_start();

        echo '<div class="revix-loader-wrapper"><span class="revix-loader"></span></div>';
        echo '<div class="revix-trustpilot-reviews" style="display:none;">';

        $rendered_count = 0;
        foreach ($reviews as $review) {
            $ratingText = $review['rating'];
            preg_match('/([0-9]+(?:\\.[0-9])?)/', $ratingText, $matches);
            $ratingValue = isset($matches[1]) ? $matches[1] : '0';
            $ratingImg = REVIXREVIEWS_FRONTEND_ASSETS . '/img/stars-' . $ratingValue . '.svg';

            if ($ratingValue < floatval($atts['min_rating']) || $ratingValue > floatval($atts['max_rating'])) {
                continue;
            }

            // Display review even without text (rating-only reviews are valid)
            $rendered_count++;
            echo '<div class="revix-trustpilot-single-review"> <div class="revix-trustpilot-author-info">';

            if (!empty($review['avatar'])) {
                // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NonEnqueuedImage -- Using external avatar image from Trustpilot, not a WordPress attachment.
                echo '<img class="revix-trustpilot-avatar" loading="lazy" src="' . esc_url($review['avatar']) . '" alt="' . esc_attr($review['author']) . '" />';
            } else {
                $initial = strtoupper(substr($review['author'], 0, 1));
                echo '<div class="revix-trustpilot-avatar-fallback">' . esc_html($initial) . '</div>';
            }
       
            echo '<div class="revix-trustpilot-author"><strong>' . esc_html($review['author']) . '</strong>';
            echo '<div class="revix-trustpilot-date">' . esc_html(gmdate('F j, Y', strtotime($review['date']))) . '</div></div>';
            echo '</div>';
            echo '<div class="revix-trustpilot-rating">';
            echo '<img src="' . esc_url($ratingImg) . '" alt="' . esc_attr($ratingText) . '"  />'; // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NonEnqueuedImage -- Using external avatar image from Trustpilot, not a WordPress attachment.
            echo '<p>' . esc_html($ratingText) . '</p>';
            echo '</div>';
          
            // Only show text if it exists
            if (!empty($review['text'])) {
                echo '<div class="revix-trustpilot-text">' . esc_html($review['text']) . '</div>';
            }
            
            echo '</div>';
        }

        echo '</div>';
        
        if ($debug) {
            echo '<!-- Revix Trustpilot: Fetched ' . count($reviews) . ' reviews, rendered ' . $rendered_count . ' reviews (filtered by rating) -->';
        }
        
        // If no reviews were rendered, show a message
        if ($rendered_count === 0) {
            echo '<div class="revix-trustpilot-no-reviews">';
            if ($debug) {
                echo '<p style="padding:15px;background:#fffbcc;border:1px solid #e6db55;">';
                echo esc_html__('No reviews to display. Fetched ' . count($reviews) . ' reviews but none matched the rating criteria (min_rating: ' . $atts['min_rating'] . ', max_rating: ' . $atts['max_rating'] . ').', 'revix-reviews');
                echo '</p>';
            }
            echo '</div>';
        }

        return ob_get_clean();
    }
}