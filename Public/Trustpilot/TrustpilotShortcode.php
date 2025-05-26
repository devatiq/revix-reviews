<?php
namespace RevixReviews\Public\Trustpilot;

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
            'min_rating' => 0
        ], $atts);
        
        $fetcher = new TrustpilotFetcher();
        $reviews = $fetcher->get_reviews($atts['count'], floatval($atts['min_rating']));

        ob_start();
        echo '<div class="revix-trustpilot-reviews">';
        foreach ($reviews as $review) {
            $ratingText = $review['rating']; // e.g. "Rated 4.5 out of 5 stars"
            preg_match('/([0-9]+(?:\\.[0-9])?)/', $ratingText, $matches);
            $ratingValue = isset($matches[1]) ? $matches[1] : '0';
            $ratingImg = REVIXREVIEWS_URL . 'public/assets/img/stars-' . $ratingValue . '.svg';
        
            // Filter by minimum rating
            if ($ratingValue < floatval($atts['min_rating'])) {
                continue;
            }
        
            if (!empty($review['text'])) {
                echo '<div class="revix-review">';
                
                // Avatar block
                if (!empty($review['avatar'])) {
                    echo '<img class="revix-avatar" src="' . esc_url($review['avatar']) . '" alt="' . esc_attr($review['author']) . '" />';
                } else {
                    $initial = strtoupper(substr($review['author'], 0, 1));
                    echo '<div class="revix-avatar-fallback">' . esc_html($initial) . '</div>';
                }
        
                echo '<div class="revix-author"><strong>' . esc_html($review['author']) . '</strong></div>';
                echo '<div class="revix-rating">';
                echo '<img src="' . esc_url($ratingImg) . '" alt="' . esc_attr($ratingText) . '" style="height: 18px; vertical-align: middle; margin-right: 5px;" />';
                echo esc_html($ratingText);
                echo '</div>';
                echo '<div class="revix-date">' . esc_html(date('F j, Y', strtotime($review['date']))) . '</div>';
                echo '<div class="revix-text">' . esc_html($review['text']) . '</div>';
                echo '</div>';
            }
        }
        
        echo '</div>';

        return ob_get_clean();
    }

}