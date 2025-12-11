<?php
namespace RevixReviews\Public\Elementor\Widgets\GoogleSummary;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use RevixReviews\Public\Inc\Integrations\Google\GoogleReviewFetcher;
use RevixReviews\Public\Assets\Library\Icons\SVG;

class Main extends Widget_Base {

    public function get_name() {
        return 'revixreviews_google_summary';
    }

    public function get_title() {
        return esc_html__('Google Reviews Summary', 'revix-reviews');
    }

    public function get_icon() {
        return 'eicon-google-maps';
    }

    public function get_categories() {
        return ['revix-reviews'];
    }

    public function get_keywords() {
        return ['google', 'reviews', 'summary', 'rating', 'revix'];
    }

    protected function register_controls() {

        // Settings Section
        $this->start_controls_section(
            'section_settings',
            [
                'label' => esc_html__('Settings', 'revix-reviews'),
            ]
        );

        $this->add_control(
            'show_name',
            [
                'label' => esc_html__('Show Business Name', 'revix-reviews'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'revix-reviews'),
                'label_off' => esc_html__('No', 'revix-reviews'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'custom_name',
            [
                'label' => esc_html__('Custom Name Prefix', 'revix-reviews'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => esc_html__('e.g., Customer reviews for', 'revix-reviews'),
                'description' => esc_html__('Optional: Add custom text before the business name', 'revix-reviews'),
                'condition' => [
                    'show_name' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_average',
            [
                'label' => esc_html__('Show Average Rating', 'revix-reviews'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'revix-reviews'),
                'label_off' => esc_html__('No', 'revix-reviews'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'reviews_label',
            [
                'label' => esc_html__('Reviews Label', 'revix-reviews'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('reviews', 'revix-reviews'),
                'description' => esc_html__('Label text for review count', 'revix-reviews'),
            ]
        );

        $this->end_controls_section();

        // Container Style Section
        $this->start_controls_section(
            'section_container_style',
            [
                'label' => esc_html__('Container', 'revix-reviews'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'container_background',
            [
                'label' => esc_html__('Background Color', 'revix-reviews'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .revix-google-summary' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'container_border',
                'selector' => '{{WRAPPER}} .revix-google-summary',
            ]
        );

        $this->add_responsive_control(
            'container_border_radius',
            [
                'label' => esc_html__('Border Radius', 'revix-reviews'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .revix-google-summary' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'container_padding',
            [
                'label' => esc_html__('Padding', 'revix-reviews'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .revix-google-summary' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'container_gap',
            [
                'label' => esc_html__('Gap Between Items', 'revix-reviews'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .revix-google-summary' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'container_max_width',
            [
                'label' => esc_html__('Max Width', 'revix-reviews'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 200,
                        'max' => 1000,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .revix-google-summary' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'container_box_shadow',
                'selector' => '{{WRAPPER}} .revix-google-summary',
            ]
        );

        $this->end_controls_section();

        // Business Name Style Section
        $this->start_controls_section(
            'section_name_style',
            [
                'label' => esc_html__('Business Name', 'revix-reviews'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_name' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'name_color',
            [
                'label' => esc_html__('Color', 'revix-reviews'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .revix-google-summary strong' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'name_typography',
                'selector' => '{{WRAPPER}} .revix-google-summary strong',
            ]
        );

        $this->end_controls_section();

        // Rating Style Section
        $this->start_controls_section(
            'section_rating_style',
            [
                'label' => esc_html__('Rating', 'revix-reviews'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_average' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'rating_text_color',
            [
                'label' => esc_html__('Text Color', 'revix-reviews'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .revix-summary-rating' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'rating_star_color',
            [
                'label' => esc_html__('Star Color', 'revix-reviews'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .revix-summary-rating svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'rating_typography',
                'selector' => '{{WRAPPER}} .revix-summary-rating',
            ]
        );

        $this->add_responsive_control(
            'rating_star_size',
            [
                'label' => esc_html__('Star Size', 'revix-reviews'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .revix-summary-rating svg' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'rating_gap',
            [
                'label' => esc_html__('Gap Between Star and Text', 'revix-reviews'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 30,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .revix-summary-rating' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Count Style Section
        $this->start_controls_section(
            'section_count_style',
            [
                'label' => esc_html__('Review Count', 'revix-reviews'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'count_color',
            [
                'label' => esc_html__('Color', 'revix-reviews'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .revix-summary-count' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'count_typography',
                'selector' => '{{WRAPPER}} .revix-summary-count',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        // Check if API credentials are configured
        $api_key = get_option('revix_google_api_key');
        $place_id = get_option('revix_google_place_id');
        
        if (empty($api_key) || empty($place_id)) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                echo '<div style="padding:15px;background:#fee;border:1px solid #c33;color:#c33;">';
                echo esc_html__('Google API Key or Place ID not configured. Please set them in WordPress dashboard under Revix Reviews > Google tab.', 'revix-reviews');
                echo '</div>';
            }
            return;
        }

        $summary = GoogleReviewFetcher::get_summary();

        if (empty($summary['name']) || $summary['total_count'] === 0) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                echo '<div style="padding:15px;background:#fee;border:1px solid #c33;color:#c33;">';
                echo esc_html__('No summary data available. Please verify your API Key and Place ID are correct.', 'revix-reviews');
                echo '</div>';
            }
            return;
        }

        ?>
        <div class="revix-google-summary">
            <?php if ($settings['show_name'] === 'yes') : ?>
                <strong>
                    <?php 
                    if (!empty($settings['custom_name'])) {
                        echo esc_html($settings['custom_name']) . ' ';
                    }
                    echo esc_html($summary['name']); 
                    ?>
                </strong>
            <?php endif; ?>

            <?php if ($settings['show_average'] === 'yes') : ?>
                <div class="revix-summary-rating">
                    <?php SVG::star_icon(['class' => 'summary-star']); ?>
                    <?php echo esc_html($summary['rating']) . ' / 5'; ?>
                </div>
            <?php endif; ?>

            <div class="revix-summary-count">
                <?php 
                echo esc_html(number_format($summary['total_count'])) . ' ' . esc_html($settings['reviews_label']); 
                ?>
            </div>
        </div>
        <?php
    }
}
