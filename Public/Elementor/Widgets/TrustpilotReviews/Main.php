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

        $this->add_control(
            'words',
            [
                'label' => esc_html__('Words Limit', 'revix-reviews'),
                'type' => Controls_Manager::NUMBER,
                'default' => 55,
                'min' => 10,
                'max' => 500,
                'step' => 5,
                'description' => esc_html__('Limit review text length by word count', 'revix-reviews'),
            ]
        );

        $this->add_control(
            'masonry',
            [
                'label' => esc_html__('Masonry Layout', 'revix-reviews'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('On', 'revix-reviews'),
                'label_off' => esc_html__('Off', 'revix-reviews'),
                'return_value' => 'true',
                'default' => 'false',
                'description' => esc_html__('Enable Pinterest-style masonry layout for varying review heights', 'revix-reviews'),
            ]
        );

        $this->end_controls_section();

        // Layout Style Section
        $this->start_controls_section(
            'layout_style_section',
            [
                'label' => esc_html__('Layout', 'revix-reviews'),
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

        // Review Card Style Section
        $this->start_controls_section(
            'card_style_section',
            [
                'label' => esc_html__('Review Card', 'revix-reviews'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'card_background',
            [
                'label' => esc_html__('Background Color', 'revix-reviews'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .revix-trustpilot-single-review' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'card_border',
                'selector' => '{{WRAPPER}} .revix-trustpilot-single-review',
            ]
        );

        $this->add_responsive_control(
            'card_border_radius',
            [
                'label' => esc_html__('Border Radius', 'revix-reviews'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .revix-trustpilot-single-review' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'card_padding',
            [
                'label' => esc_html__('Padding', 'revix-reviews'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .revix-trustpilot-single-review' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_box_shadow',
                'selector' => '{{WRAPPER}} .revix-trustpilot-single-review',
            ]
        );

        $this->end_controls_section();

        // Author Style Section
        $this->start_controls_section(
            'author_style_section',
            [
                'label' => esc_html__('Author', 'revix-reviews'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'author_name_color',
            [
                'label' => esc_html__('Name Color', 'revix-reviews'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .revix-trustpilot-author strong' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'author_name_typography',
                'label' => esc_html__('Name Typography', 'revix-reviews'),
                'selector' => '{{WRAPPER}} .revix-trustpilot-author strong',
            ]
        );

        $this->add_control(
            'author_date_color',
            [
                'label' => esc_html__('Date Color', 'revix-reviews'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .revix-trustpilot-date' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'author_date_typography',
                'label' => esc_html__('Date Typography', 'revix-reviews'),
                'selector' => '{{WRAPPER}} .revix-trustpilot-date',
            ]
        );

        $this->add_responsive_control(
            'avatar_size',
            [
                'label' => esc_html__('Avatar Size', 'revix-reviews'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 30,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .revix-trustpilot-avatar' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .revix-trustpilot-avatar-fallback' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'avatar_border_radius',
            [
                'label' => esc_html__('Avatar Border Radius', 'revix-reviews'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .revix-trustpilot-avatar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .revix-trustpilot-avatar-fallback' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Review Text Style Section
        $this->start_controls_section(
            'text_style_section',
            [
                'label' => esc_html__('Review Text', 'revix-reviews'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => esc_html__('Text Color', 'revix-reviews'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .revix-trustpilot-text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'text_typography',
                'label' => esc_html__('Typography', 'revix-reviews'),
                'selector' => '{{WRAPPER}} .revix-trustpilot-text',
            ]
        );

        $this->add_responsive_control(
            'text_margin',
            [
                'label' => esc_html__('Margin', 'revix-reviews'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .revix-trustpilot-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Rating Style Section
        $this->start_controls_section(
            'rating_style_section',
            [
                'label' => esc_html__('Rating', 'revix-reviews'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'rating_stars_size',
            [
                'label' => esc_html__('Stars Size', 'revix-reviews'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .revix-trustpilot-rating img' => 'width: {{SIZE}}{{UNIT}}; height: auto;',
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
            'debug' => $settings['debug'],
            'words' => isset($settings['words']) && $settings['words'] !== '' ? intval($settings['words']) : 55,
            'masonry' => $settings['masonry']
        ];

        $debug = ($atts['debug'] === 'true');
        
        // Debug: Log the words value
        if ($debug) {
            error_log('Revix Trustpilot Debug: Words setting = ' . $atts['words']);
            error_log('Revix Trustpilot Debug: Settings words raw = ' . print_r($settings['words'], true));
        }
        
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

        // Check if we're in Elementor editor mode
        $is_editor = \Elementor\Plugin::$instance->editor->is_edit_mode();
        
        if (!$is_editor) {
            echo '<div class="revix-loader-wrapper"><span class="revix-loader"></span></div>';
        }
        
        // Build classes array
        $classes = ['revix-trustpilot-reviews', 'revixreviews-elementor-widget'];
        if ($atts['masonry'] === 'true') {
            $classes[] = 'revix-trustpilot-masonry';
        }
        
        // In editor mode, show directly; otherwise hide until JS loads
        $display_style = $is_editor ? 'display:grid;' : 'display:none;';
        echo '<div class="' . esc_attr(implode(' ', $classes)) . '" style="' . esc_attr($display_style) . '">';

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
                $review_text = wp_trim_words($review['text'], $atts['words'], '...');
                echo '<div class="revix-trustpilot-text">' . esc_html($review_text) . '</div>';
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
