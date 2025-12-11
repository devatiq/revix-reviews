<?php
/**
 * Trustpilot Reviews Elementor Widget
 *
 * @package RevixReviews\Public\Elementor\Widgets\TrustpilotReviews
 * @since 1.3.0
 */

namespace RevixReviews\Public\Elementor\Widgets\TrustpilotReviews;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use RevixReviews\Public\Inc\Integrations\Trustpilot\TrustpilotFetcher;

/**
 * Trustpilot Reviews Widget
 */
class Main extends Widget_Base
{
    /**
     * Get widget name.
     *
     * @return string Widget name.
     */
    public function get_name()
    {
        return 'revixreviews_trustpilot_reviews';
    }

    /**
     * Get widget title.
     *
     * @return string Widget title.
     */
    public function get_title()
    {
        return esc_html__('Trustpilot Reviews', 'revix-reviews');
    }

    /**
     * Get widget icon.
     *
     * @return string Widget icon.
     */
    public function get_icon()
    {
        return 'eicon-star';
    }

    /**
     * Get widget categories.
     *
     * @return array Widget categories.
     */
    public function get_categories()
    {
        return ['revix-reviews'];
    }

    /**
     * Get widget keywords.
     *
     * @return array Widget keywords.
     */
    public function get_keywords()
    {
        return ['trustpilot', 'reviews', 'testimonials', 'ratings', 'revix'];
    }

    /**
     * Register widget controls.
     */
    protected function register_controls()
    {
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__('Settings', 'revix-reviews'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'count',
            [
                'label' => esc_html__('Number of Reviews', 'revix-reviews'),
                'type' => Controls_Manager::NUMBER,
                'default' => 15,
                'min' => 1,
                'max' => 50,
                'step' => 1,
                'description' => esc_html__('Maximum number of reviews to display', 'revix-reviews'),
            ]
        );

        $this->add_control(
            'min_rating',
            [
                'label' => esc_html__('Minimum Rating', 'revix-reviews'),
                'type' => Controls_Manager::NUMBER,
                'default' => 0,
                'min' => 0,
                'max' => 5,
                'step' => 0.5,
                'description' => esc_html__('Show only reviews with this rating or higher', 'revix-reviews'),
            ]
        );

        $this->add_control(
            'max_rating',
            [
                'label' => esc_html__('Maximum Rating', 'revix-reviews'),
                'type' => Controls_Manager::NUMBER,
                'default' => 5,
                'min' => 0,
                'max' => 5,
                'step' => 0.5,
                'description' => esc_html__('Show only reviews with this rating or lower', 'revix-reviews'),
            ]
        );

        $this->add_control(
            'debug',
            [
                'label' => esc_html__('Debug Mode', 'revix-reviews'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('On', 'revix-reviews'),
                'label_off' => esc_html__('Off', 'revix-reviews'),
                'return_value' => 'true',
                'default' => 'false',
                'description' => esc_html__('Enable debug mode to see error messages', 'revix-reviews'),
            ]
        );

        $this->end_controls_section();

        // Style Section
        $this->start_controls_section(
            'style_section',
            [
                'label' => esc_html__('Style', 'revix-reviews'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label' => esc_html__('Columns', 'revix-reviews'),
                'type' => Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                ],
                'selectors' => [
                    '{{WRAPPER}} .revix-trustpilot-reviews' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
                ],
            ]
        );

        $this->add_responsive_control(
            'gap',
            [
                'label' => esc_html__('Gap', 'revix-reviews'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .revix-trustpilot-reviews' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Render widget output on the frontend.
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();

        $atts = [
            'count' => $settings['count'],
            'min_rating' => $settings['min_rating'],
            'max_rating' => $settings['max_rating'],
            'debug' => $settings['debug']
        ];

        $debug = ($atts['debug'] === 'true');
        
        // Check if URL is configured
        $trustpilot_url = get_option('revix_trustpilot_url');
        if (empty($trustpilot_url)) {
            $message = __('Trustpilot URL not configured. Please set it in the WordPress dashboard under Revix Reviews > Trustpilot tab.', 'revix-reviews');
            if ($debug) {
                echo '<div class="revix-trustpilot-error" style="padding:15px;background:#fee;border:1px solid #c33;color:#c33;">' . esc_html($message) . '</div>';
                return;
            }
            echo '<!-- ' . esc_html($message) . ' -->';
            return;
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
                echo '<div class="revix-trustpilot-error" style="padding:15px;background:#fee;border:1px solid #c33;color:#c33;">' . esc_html($message) . '</div>';
                return;
            }
            echo '<!-- ' . esc_html($message) . ' -->';
            return;
        }

        echo '<div class="revix-loader-wrapper"><span class="revix-loader"></span></div>';
        echo '<div class="revix-trustpilot-reviews revixreviews-elementor-widget" style="display:none;">';

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
                echo '<img class="revix-trustpilot-avatar" loading="lazy" src="' . esc_url($review['avatar']) . '" alt="' . esc_attr($review['author']) . '" />';
            } else {
                $initial = strtoupper(substr($review['author'], 0, 1));
                echo '<div class="revix-trustpilot-avatar-fallback">' . esc_html($initial) . '</div>';
            }
       
            echo '<div class="revix-trustpilot-author"><strong>' . esc_html($review['author']) . '</strong>';
            echo '<div class="revix-trustpilot-date">' . esc_html(gmdate('F j, Y', strtotime($review['date']))) . '</div></div>';
            echo '</div>';
            echo '<div class="revix-trustpilot-rating">';
            echo '<img src="' . esc_url($ratingImg) . '" alt="' . esc_attr($ratingText) . '"  />';
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
    }
}
