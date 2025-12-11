<?php
namespace RevixReviews\Public\Elementor\Widgets\TrustpilotSummary;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Main extends Widget_Base {

    public function get_name() {
        return 'revixreviews_trustpilot_summary';
    }

    public function get_title() {
        return esc_html__('Trustpilot Summary', 'revix-reviews');
    }

    public function get_icon() {
        return 'eicon-star-o';
    }

    public function get_categories() {
        return ['revix-reviews'];
    }

    public function get_keywords() {
        return ['trustpilot', 'summary', 'rating', 'reviews', 'revix'];
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
            'show_count',
            [
                'label' => esc_html__('Show Review Count', 'revix-reviews'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'revix-reviews'),
                'label_off' => esc_html__('No', 'revix-reviews'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'count_label',
            [
                'label' => esc_html__('Count Label', 'revix-reviews'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Reviews', 'revix-reviews'),
                'description' => esc_html__('Label text for review count', 'revix-reviews'),
                'condition' => [
                    'show_count' => 'yes',
                ],
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
                    '{{WRAPPER}} .revix-summary-wrapper' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'container_direction',
            [
                'label' => esc_html__('Direction', 'revix-reviews'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'row' => [
                        'title' => esc_html__('Row', 'revix-reviews'),
                        'icon' => 'eicon-arrow-right',
                    ],
                    'column' => [
                        'title' => esc_html__('Column', 'revix-reviews'),
                        'icon' => 'eicon-arrow-down',
                    ],
                ],
                'default' => 'row',
                'selectors' => [
                    '{{WRAPPER}} .revix-summary-wrapper' => 'flex-direction: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'container_gap',
            [
                'label' => esc_html__('Gap', 'revix-reviews'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .revix-summary-wrapper' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'container_alignment',
            [
                'label' => esc_html__('Alignment', 'revix-reviews'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => esc_html__('Left', 'revix-reviews'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'revix-reviews'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'flex-end' => [
                        'title' => esc_html__('Right', 'revix-reviews'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .revix-summary-wrapper' => 'align-items: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'container_border',
                'selector' => '{{WRAPPER}} .revix-summary-wrapper',
            ]
        );

        $this->add_responsive_control(
            'container_border_radius',
            [
                'label' => esc_html__('Border Radius', 'revix-reviews'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .revix-summary-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .revix-summary-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'container_box_shadow',
                'selector' => '{{WRAPPER}} .revix-summary-wrapper',
            ]
        );

        $this->end_controls_section();

        // Average Rating Style Section
        $this->start_controls_section(
            'section_average_style',
            [
                'label' => esc_html__('Average Rating', 'revix-reviews'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_average' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'average_color',
            [
                'label' => esc_html__('Text Color', 'revix-reviews'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .revix-summary-average strong' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'average_typography',
                'selector' => '{{WRAPPER}} .revix-summary-average strong',
            ]
        );

        $this->add_responsive_control(
            'stars_size',
            [
                'label' => esc_html__('Stars Size', 'revix-reviews'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .revix-summary-average img' => 'height: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_responsive_control(
            'average_gap',
            [
                'label' => esc_html__('Gap Between Rating and Stars', 'revix-reviews'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 30,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .revix-summary-average' => 'gap: {{SIZE}}{{UNIT}};',
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
                'condition' => [
                    'show_count' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'count_number_color',
            [
                'label' => esc_html__('Number Color', 'revix-reviews'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .revix-summary-count strong' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'count_text_color',
            [
                'label' => esc_html__('Label Color', 'revix-reviews'),
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

        $url = get_option('revix_trustpilot_url');
        
        if (!$url) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                echo '<div style="padding:15px;background:#fee;border:1px solid #c33;color:#c33;">';
                echo esc_html__('Trustpilot URL not configured. Please set it in WordPress dashboard under Revix Reviews > Trustpilot tab.', 'revix-reviews');
                echo '</div>';
            }
            return;
        }

        $response = wp_remote_get($url);
        if (is_wp_error($response)) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                echo '<div style="padding:15px;background:#fee;border:1px solid #c33;color:#c33;">';
                echo esc_html__('Error fetching Trustpilot page. Please check your URL.', 'revix-reviews');
                echo '</div>';
            }
            return;
        }

        $html = wp_remote_retrieve_body($response);
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        @$doc->loadHTML($html);
        $xpath = new \DOMXPath($doc);

        $scripts = $xpath->query('//script[@type="application/ld+json"]');

        $average = '';
        $count = '';

        foreach ($scripts as $script) {
            $json = json_decode($script->nodeValue, true);

            if (isset($json['aggregateRating']['ratingValue'])) {
                $average = $json['aggregateRating']['ratingValue'];
                $count = $json['aggregateRating']['reviewCount'];
                break;
            }

            if (isset($json['@graph']) && is_array($json['@graph'])) {
                foreach ($json['@graph'] as $node) {
                    if (isset($node['aggregateRating']['ratingValue'])) {
                        $average = $node['aggregateRating']['ratingValue'];
                        $count = $node['aggregateRating']['reviewCount'];
                        break 2;
                    }
                }
            }
        }

        if (empty($average) && empty($count)) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                echo '<div style="padding:15px;background:#fee;border:1px solid #c33;color:#c33;">';
                echo esc_html__('No summary data available. Please verify your Trustpilot URL.', 'revix-reviews');
                echo '</div>';
            }
            return;
        }

        ?>
        <div class="revix-summary-wrapper">
            <?php if ($settings['show_average'] === 'yes' && $average) : ?>
                <?php
                $rating = floatval($average);
                if ($rating >= 4.75) {
                    $svgFile = 'stars-5.svg';
                } elseif ($rating >= 4.25) {
                    $svgFile = 'stars-4.5.svg';
                } elseif ($rating >= 3.75) {
                    $svgFile = 'stars-4.svg';
                } elseif ($rating >= 3.25) {
                    $svgFile = 'stars-3.5.svg';
                } elseif ($rating >= 2.75) {
                    $svgFile = 'stars-3.svg';
                } elseif ($rating >= 2.25) {
                    $svgFile = 'stars-2.5.svg';
                } elseif ($rating >= 1.75) {
                    $svgFile = 'stars-2.svg';
                } elseif ($rating >= 1.25) {
                    $svgFile = 'stars-1.5.svg';
                } else {
                    $svgFile = 'stars-1.svg';
                }
                $svg = REVIXREVIEWS_FRONTEND_ASSETS . '/img/' . $svgFile;
                ?>
                <div class="revix-summary-average">
                    <strong><?php echo esc_html($average); ?></strong>
                    <img src="<?php echo esc_url($svg); ?>" alt="<?php echo esc_attr('Rated ' . $average . ' out of 5 stars'); ?>" />
                </div>
            <?php endif; ?>

            <?php if ($settings['show_count'] === 'yes' && $count) : ?>
                <div class="revix-summary-count">
                    <strong><?php echo esc_html(number_format((int) $count)); ?></strong> <?php echo esc_html($settings['count_label']); ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
}
