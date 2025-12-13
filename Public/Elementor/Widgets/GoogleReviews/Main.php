<?php
namespace RevixReviews\Public\Elementor\Widgets\GoogleReviews;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use RevixReviews\Public\Inc\Integrations\Google\GoogleReviewFetcher;
use RevixReviews\Public\Assets\Library\Icons\SVG;

class Main extends Widget_Base {

    public function get_name() {
        return 'revixreviews_google_reviews';
    }

    public function get_title() {
        return esc_html__('Google Reviews', 'revix-reviews');
    }

    public function get_icon() {
        return 'eicon-google-maps';
    }

    public function get_categories() {
        return ['revix-reviews'];
    }

    public function get_keywords() {
        return ['google', 'reviews', 'testimonials', 'ratings', 'revix'];
    }

    protected function register_controls() {

        // Settings Section
        $this->start_controls_section(
            'section_settings',
            [
                'label' => esc_html__('Settings', 'revix-reviews'),
            ]
        );


        $this->add_responsive_control(
            'columns',
            [
                'label' => esc_html__('Columns', 'revix-reviews'),
                'type' => Controls_Manager::SELECT,
                'default' => '3',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                ],
                'selectors' => [
                    '{{WRAPPER}} .revix-google-reviews' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
                ],
            ]
        );

        $this->add_control(
            'words',
            [
                'label' => esc_html__('Words Limit', 'revix-reviews'),
                'type' => Controls_Manager::NUMBER,
                'default' => 100,
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
            'section_layout_style',
            [
                'label' => esc_html__('Layout', 'revix-reviews'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'gap',
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
                'default' => [
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .revix-google-reviews' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Review Card Style Section
        $this->start_controls_section(
            'section_card_style',
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
                    '{{WRAPPER}} .revix-google-review-item' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'card_border',
                'selector' => '{{WRAPPER}} .revix-google-review-item',
            ]
        );

        $this->add_responsive_control(
            'card_border_radius',
            [
                'label' => esc_html__('Border Radius', 'revix-reviews'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .revix-google-review-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .revix-google-review-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_box_shadow',
                'selector' => '{{WRAPPER}} .revix-google-review-item',
            ]
        );

        $this->end_controls_section();

        // Rating Style Section
        $this->start_controls_section(
            'section_rating_style',
            [
                'label' => esc_html__('Rating', 'revix-reviews'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'rating_star_color',
            [
                'label' => esc_html__('Star Color', 'revix-reviews'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .revix-google-rating svg.filled' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'rating_empty_star_color',
            [
                'label' => esc_html__('Empty Star Color', 'revix-reviews'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .revix-google-rating svg.empty' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'rating_text_color',
            [
                'label' => esc_html__('Rating Text Color', 'revix-reviews'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .revix-google-rating-text' => 'color: {{VALUE}};',
                ],
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
                        'min' => 10,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .revix-google-rating svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Review Text Style Section
        $this->start_controls_section(
            'section_text_style',
            [
                'label' => esc_html__('Review Text', 'revix-reviews'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => esc_html__('Color', 'revix-reviews'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .revix-google-review-text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'text_typography',
                'selector' => '{{WRAPPER}} .revix-google-review-text',
            ]
        );

        $this->add_responsive_control(
            'text_margin',
            [
                'label' => esc_html__('Margin', 'revix-reviews'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .revix-google-review-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Author Style Section
        $this->start_controls_section(
            'section_author_style',
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
                    '{{WRAPPER}} .revix-google-review-author strong' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'author_name_typography',
                'label' => esc_html__('Name Typography', 'revix-reviews'),
                'selector' => '{{WRAPPER}} .revix-google-review-author strong',
            ]
        );

        $this->add_control(
            'author_date_color',
            [
                'label' => esc_html__('Date Color', 'revix-reviews'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .revix-google-review-author small' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'author_date_typography',
                'label' => esc_html__('Date Typography', 'revix-reviews'),
                'selector' => '{{WRAPPER}} .revix-google-review-author small',
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
                        'min' => 20,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .revix-google-review-avatar' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
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
                    '{{WRAPPER}} .revix-google-review-avatar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $atts = [
            'masonry' => $settings['masonry'],
            'words' => isset($settings['words']) && $settings['words'] !== '' ? intval($settings['words']) : 100,
        ];

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

        $reviews = GoogleReviewFetcher::get_reviews();

        if (empty($reviews)) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                echo '<div style="padding:15px;background:#fee;border:1px solid #c33;color:#c33;">';
                echo esc_html__('No Google reviews found. Please verify your API Key and Place ID are correct.', 'revix-reviews');
                echo '</div>';
            }
            return;
        }

        // Build classes array
        $classes = ['revix-google-reviews'];
        if ($atts['masonry'] === 'true') {
            $classes[] = 'revix-google-masonry';
        }

        ?>
        <div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
            <?php foreach ($reviews as $review) : ?>
                <div class="revix-google-review-item">
                    <div class="revix-google-rating">
                        <?php
                        // Display star icons based on rating
                        $rating = intval($review['rating']);
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $rating) {
                                SVG::star_icon(['class' => 'star filled']);
                            } else {
                                SVG::empty_star_icon(['class' => 'star empty']);
                            }
                        }
                        ?>
                        <span class="revix-google-rating-text"><?php echo esc_html($rating); ?>/5</span>
                    </div>
                    <?php if (!empty($review['text'])) : ?>
                        <p class="revix-google-review-text">
                            <?php echo esc_html(wp_trim_words($review['text'], $atts['words'], '...')); ?>
                        </p>
                    <?php endif; ?>
                    <div class="revix-google-review-meta">
                        <div class="revix-google-review-author">
                            <?php if (!empty($review['profile_photo_url'])) : ?>
                                <img class="revix-google-review-avatar" src="<?php echo esc_url($review['profile_photo_url']); ?>" alt="<?php echo esc_attr($review['author_name']); ?>">
                            <?php endif; ?>
                            <div class="revix-google-review-author-info">
                                <strong><?php echo esc_html($review['author_name']); ?></strong>
                                <small><?php echo esc_html($review['relative_time_description']); ?></small>
                            </div>
                        </div>
                        <div class="revix-google-review-logo">
                            <?php SVG::google_logo(['class' => 'revix-google-review-logo-svg']); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }
}
