<?php
namespace RevixReviews\Public\Shortcodes\Google;

use RevixReviews\Public\Inc\Integrations\Google\GoogleReviewFetcher;

class GoogleReviews {

    public function __construct() {
        self::register();
    }

    public static function register() {
        add_shortcode('revix_google_reviews', [__CLASS__, 'render_reviews']);
    }

    public static function render_reviews($atts = []) {
        $reviews = GoogleReviewFetcher::get_reviews();

        if (empty($reviews)) {
            return '<p>' . esc_html__('No Google reviews found.', 'revix-reviews') . '</p>';
        }

        ob_start();

        echo '<div class="revix-google-reviews">';
        foreach ($reviews as $review) {
            echo '<div class="revix-review-item">';
            if (!empty($review['profile_photo_url'])) {
                echo '<img class="revix-avatar" src="' . esc_url($review['profile_photo_url']) . '" alt="' . esc_attr($review['author_name']) . '">';
            }
            echo '<strong>' . esc_html($review['author_name']) . '</strong>';
            echo '<div class="revix-rating">' . intval($review['rating']) . '/5</div>';
            echo '<p class="revix-text">' . esc_html($review['text']) . '</p>';
            echo '<small>' . esc_html($review['relative_time_description']) . '</small>';
            echo '</div>';
        }
        echo '</div>';

        return ob_get_clean();
    }
}