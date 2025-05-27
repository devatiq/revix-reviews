<?php
namespace RevixReviews\Public\Shortcodes\Trustpilot;

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
            'max_rating' => 5
        ], $atts);

        $fetcher = new TrustpilotFetcher();
        $reviews = $fetcher->get_reviews($atts['count'], floatval($atts['min_rating']));

        ob_start();

        echo '<div class="revix-loader-wrapper"><span class="revix-loader"></span></div>';
        echo '<div class="revix-trustpilot-reviews" style="display:none;">';

        foreach ($reviews as $review) {
            $ratingText = $review['rating'];
            preg_match('/([0-9]+(?:\\.[0-9])?)/', $ratingText, $matches);
            $ratingValue = isset($matches[1]) ? $matches[1] : '0';
            $ratingImg = REVIXREVIEWS_URL . 'public/assets/img/stars-' . $ratingValue . '.svg';

            if ($ratingValue < floatval($atts['min_rating']) || $ratingValue > floatval($atts['max_rating'])) {
                continue;
            }

            if (!empty($review['text'])) {
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
              
                echo '<div class="revix-trustpilot-text">' . esc_html($review['text']) . '</div>';
                echo '</div>';
            }
        }

        echo '</div>';

        return ob_get_clean();
    }
}