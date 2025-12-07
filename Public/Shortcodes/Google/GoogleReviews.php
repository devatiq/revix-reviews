<?php
namespace RevixReviews\Public\Shortcodes\Google;

use RevixReviews\Public\Inc\Integrations\Google\GoogleReviewFetcher;
use RevixReviews\Public\Assets\Library\Icons\SVG;

class GoogleReviews
{

    public function __construct()
    {
        self::register();
    }

    public static function register()
    {
        add_shortcode('revix_google_reviews', [__CLASS__, 'render_reviews']);
        add_shortcode('revix_google_summary', [__CLASS__, 'render_summary']);
    }

    /**
     * Shortcode to display individual reviews.
     */
    public static function render_reviews($atts = [])
    {
        $atts = shortcode_atts([
            'masonry' => 'false',
            'words' => '55',
            'debug' => 'false'
        ], $atts, 'revix_google_reviews');

        $debug = ($atts['debug'] === 'true');
        
        // Check if API credentials are configured
        $api_key = get_option('revix_google_api_key');
        $place_id = get_option('revix_google_place_id');
        
        if (empty($api_key) || empty($place_id)) {
            $message = __('Google API Key or Place ID not configured. Please set them in the WordPress dashboard under Revix Reviews > Google tab.', 'revix-reviews');
            if ($debug) {
                $details = 'API Key: ' . (empty($api_key) ? 'Not Set' : 'Set') . ', Place ID: ' . (empty($place_id) ? 'Not Set' : 'Set');
                return '<div class="revix-google-error" style="padding:15px;background:#fee;border:1px solid #c33;color:#c33;">' . esc_html($message) . '<br><small>' . esc_html($details) . '</small></div>';
            }
            return '<!-- ' . esc_html($message) . ' -->';
        }

        $reviews = GoogleReviewFetcher::get_reviews();
        
        if ($debug) {
            error_log('Revix Google Debug: API Key = ' . (empty($api_key) ? 'Not Set' : 'Set'));
            error_log('Revix Google Debug: Place ID = ' . $place_id);
            error_log('Revix Google Debug: Reviews fetched = ' . count($reviews));
        }

        if (empty($reviews)) {
            $message = __('No Google reviews found. Please verify your API Key and Place ID are correct.', 'revix-reviews');
            if ($debug) {
                return '<div class="revix-google-error" style="padding:15px;background:#fee;border:1px solid #c33;color:#c33;">' . esc_html($message) . '</div>';
            }
            return '<!-- ' . esc_html($message) . ' -->';
        }

        ob_start();
        $classes = ['revix-google-reviews'];
        if ($atts['masonry'] === 'true') {
            $classes[] = 'revix-google-masonry';
        }
        echo '<div class="' . esc_attr(implode(' ', $classes)) . '">';
        foreach ($reviews as $review) {
            echo '<div class="revix-google-review-item">';
                echo '<div class="revix-google-rating">';
                    // Display star icons based on rating
                    $rating = intval($review['rating']);
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $rating) {
                            SVG::star_icon(['class' => 'star filled']);
                        } else {
                            SVG::empty_star_icon(['class' => 'star empty']);
                        }
                    }
                    echo ' <span class="revix-google-rating-text">' . $rating . '/5</span>';
                echo '</div>';
                if (!empty($review['text'])) {
                    echo '<p class="revix-google-review-text">' . esc_html(wp_trim_words($review['text'], intval($atts['words']), '...')) . '</p>';
                }
                echo '<div class="revix-google-review-meta">';
                    echo '<div class="revix-google-review-author">';
                        if (!empty($review['profile_photo_url'])) {
                            echo '<img class="revix-google-review-avatar" src="' . esc_url($review['profile_photo_url']) . '" alt="' . esc_attr($review['author_name']) . '">';
                        }
                    echo '<div class="revix-google-review-author-info">';
                    echo '<strong>' . esc_html($review['author_name']) . '</strong>';     
                    echo '<small>' . esc_html($review['relative_time_description']) . '</small>';
                    echo '</div>'; // Close.revix-google-review-author-info
                    echo '</div>'; // Close.revix-google-review-author
                    echo '<div class="revix-google-review-logo">';
                        SVG::google_logo(['class' => 'revix-google-review-logo-svg']);           
                    echo '</div>';
                echo '</div>'; // Close.revix-google-review-meta
            echo '</div>'; // Close .revix-google-review-item

        }
        echo '</div>';
        
        if ($debug) {
            echo '<!-- Revix Google: Rendered ' . count($reviews) . ' reviews -->';
        }

        return ob_get_clean();
    }

    /**
     * Shortcode to display summary: rating, total count, and business name.
     */
    public static function render_summary($atts = [])
    {
        $atts = shortcode_atts([
            'name' => 'true', // true / false / custom string
            'average' => 'true',
            'label' => esc_html__('reviews', 'revix-reviews'),
        ], $atts, 'revix_google_summary');

        $summary = GoogleReviewFetcher::get_summary();

        if (empty($summary['name']) || $summary['total_count'] === 0) {
            return '<p>' . esc_html__('No summary data available.', 'revix-reviews') . '</p>';
        }

        ob_start();
        echo '<div class="revix-google-summary">';

        // Name rendering
        if ($atts['name'] === 'true') {
            echo '<strong>' . esc_html($summary['name']) . '</strong>';
        } elseif (!empty($atts['name']) && $atts['name'] !== 'false') {
            echo '<strong>' . esc_html($atts['name']) . ' ' . esc_html($summary['name']) . '</strong>';
        }

        // Average rating
        if ($atts['average'] === 'true') {
            echo '<div class="revix-summary-rating">';
            SVG::star_icon(['class' => 'summary-star']);
            echo ' ' . esc_html($summary['rating']) . ' / 5</div>';
        }

        // Review count
        echo '<div class="revix-summary-count">' . esc_html(number_format($summary['total_count'])) . ' ' . esc_html($atts['label']) . '</div>';

        echo '</div>';
        return ob_get_clean();
    }


}