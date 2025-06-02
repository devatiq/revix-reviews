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
        $reviews = GoogleReviewFetcher::get_reviews();

        if (empty($reviews)) {
            return '<p>' . esc_html__('No Google reviews found.', 'revix-reviews') . '</p>';
        }

        ob_start();
        echo '<div class="revix-google-reviews">';
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
            echo ' <span class="rating-text">' . $rating . '/5</span>';
            echo '</div>';
            if (!empty($review['text'])) {
                echo '<p class="revix-google-review-text">' . esc_html($review['text']) . '</p>';
            }
            echo '<div class="revix-google-review-meta">';
            echo '<div class="revix-google-review-author">';
            if (!empty($review['profile_photo_url'])) {
                echo '<img class="revix-google-review-avatar" src="' . esc_url($review['profile_photo_url']) . '" alt="' . esc_attr($review['author_name']) . '">';
            }
            echo '<strong>' . esc_html($review['author_name']) . '</strong>';     
            echo '<small>' . esc_html($review['relative_time_description']) . '</small>';
            echo '<div class="revix-google-review-logo">';
                SVG::google_logo(['class' => 'revix-google-review-logo-img']);
            echo '</div>';
            echo '</div>';
            echo '</div>'; // Close .revix-google-review-item

        }
        echo '</div>';

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
            echo '<div class="revix-summary-rating">⭐ ' . esc_html($summary['rating']) . ' / 5</div>';
        }

        // Review count
        echo '<div class="revix-summary-count">' . esc_html(number_format($summary['total_count'])) . ' ' . esc_html($atts['label']) . '</div>';

        echo '</div>';
        return ob_get_clean();
    }


}