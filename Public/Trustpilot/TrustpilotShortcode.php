<?php
namespace RevixReviews\Public\Trustpilot;

class TrustpilotShortcode {
    public function __construct() {
        add_shortcode('revix_trustpilot_reviews', [$this, 'render']);
    }

    public function render($atts) {
        $atts = shortcode_atts(['count' => 5], $atts);
        $fetcher = new TrustpilotFetcher();
        $reviews = $fetcher->get_reviews($atts['count']);
    
        ob_start();
        echo '<div class="revix-trustpilot-reviews">';
        foreach ($reviews as $review) {
            echo '<div class="revix-review">';
            
            // Avatar block
            if (!empty($review['avatar'])) {
                echo '<img class="revix-avatar" src="' . esc_url($review['avatar']) . '" alt="' . esc_attr($review['author']) . '" />';
            } else {
                $initial = strtoupper(substr($review['author'], 0, 1));
                echo '<div class="revix-avatar-fallback">' . esc_html($initial) . '</div>';
            }
    
            echo '<div class="revix-author"><strong>' . esc_html($review['author']) . '</strong></div>';
            echo '<div class="revix-rating">' . esc_html($review['rating']) . '</div>';
            echo '<div class="revix-date">' . esc_html(date('F j, Y', strtotime($review['date']))) . '</div>';
            echo '<div class="revix-text">' . esc_html($review['text']) . '</div>';
            echo '</div>';
        }
        echo '</div>';
        return ob_get_clean();
    }
    
}