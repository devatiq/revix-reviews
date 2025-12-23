<?php
namespace RevixReviews\Public\Elementor\Widgets\TestimonialReviews;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Main extends Widget_Base {

    public function get_name() {
        return 'revixreviews_testimonial_reviews';
    }

    public function get_title() {
        return esc_html__('Testimonial Reviews', 'revix-reviews');
    }

    public function get_icon() {
        return 'eicon-testimonial';
    }

    public function get_categories() {
        return ['revix-reviews'];
    }

    public function get_keywords() {
        return ['testimonial', 'reviews', 'ratings', 'revix', 'customer'];
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
            'count',
            [
                'label' => esc_html__('Number of Reviews', 'revix-reviews'),
                'type' => Controls_Manager::NUMBER,
                'default' => -1,
                'min' => -1,
                'step' => 1,
                'description' => esc_html__('Set -1 to display all reviews', 'revix-reviews'),
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
                'step' => 1,
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
                'step' => 1,
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

        $this->add_responsive_control(
            'columns',
            [
                'label' => esc_html__('Columns', 'revix-reviews'),
                'type' => Controls_Manager::SELECT,
                'default' => '2',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                ],
                'selectors' => [
                    '{{WRAPPER}} .revix-testimonial-grids:not(.revix-testimonial-masonry)' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
                    '{{WRAPPER}} .revix-testimonial-grids.revix-testimonial-masonry' => 'column-count: {{VALUE}};',
                ],
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
                    'size' => 30,
                ],
                'selectors' => [
                    '{{WRAPPER}} .revix-testimonial-grids:not(.revix-testimonial-masonry)' => 'gap: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .revix-testimonial-grids.revix-testimonial-masonry' => 'column-gap: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .revix-testimonial-masonry .revix-testimonial-single-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Card Style Section
        $this->start_controls_section(
            'section_card_style',
            [
                'label' => esc_html__('Card', 'revix-reviews'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'card_background',
            [
                'label' => esc_html__('Background Color', 'revix-reviews'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .revix-testimonial-single-item' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'card_border',
                'selector' => '{{WRAPPER}} .revix-testimonial-single-item',
            ]
        );

        $this->add_responsive_control(
            'card_border_radius',
            [
                'label' => esc_html__('Border Radius', 'revix-reviews'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .revix-testimonial-single-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .revix-testimonial-single-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_box_shadow',
                'selector' => '{{WRAPPER}} .revix-testimonial-single-item',
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
                    '{{WRAPPER}} .revix-testimonial-rating svg path' => 'fill: {{VALUE}};',
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
                    '{{WRAPPER}} .revix-testimonial-rating svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Content Style Section
        $this->start_controls_section(
            'section_content_style',
            [
                'label' => esc_html__('Content', 'revix-reviews'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'content_color',
            [
                'label' => esc_html__('Color', 'revix-reviews'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .revix-testimonial-content' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'content_typography',
                'selector' => '{{WRAPPER}} .revix-testimonial-content',
            ]
        );

        $this->end_controls_section();

        // Heading Style Section
        $this->start_controls_section(
            'section_heading_style',
            [
                'label' => esc_html__('Heading Name', 'revix-reviews'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'heading_name_color',
            [
                'label' => esc_html__('Color', 'revix-reviews'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .revix-testimonial-client-info h3' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'heading_name_typography',
                'selector' => '{{WRAPPER}} .revix-testimonial-client-info h3',
            ]
        );

        $this->end_controls_section();

        // Quote Icon Style Section
        $this->start_controls_section(
            'section_quote_style',
            [
                'label' => esc_html__('Quote Icon', 'revix-reviews'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'quote_color',
            [
                'label' => esc_html__('Color', 'revix-reviews'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .revix-testimonial-quote svg path' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'quote_size',
            [
                'label' => esc_html__('Size', 'revix-reviews'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 20,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .revix-testimonial-quote svg' => 'width: {{SIZE}}{{UNIT}}; height: auto;',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $args = array(
            'post_type' => 'revixreviews',
            'post_status' => 'publish',
            'posts_per_page' => intval($settings['count']),
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'revixreviews_rating',
                    'value' => floatval($settings['min_rating']),
                    'compare' => '>=',
                    'type' => 'NUMERIC'
                ),
                array(
                    'key' => 'revixreviews_rating',
                    'value' => floatval($settings['max_rating']),
                    'compare' => '<=',
                    'type' => 'NUMERIC'
                )
            ),
        );

        $query = new \WP_Query($args);

        if (!$query->have_posts()) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                echo '<div style="padding:15px;background:#fee;border:1px solid #c33;color:#c33;">';
                echo esc_html__('No testimonial reviews found. Add some reviews to display them here.', 'revix-reviews');
                echo '</div>';
            }
            return;
        }

        // Build classes array
        $classes = ['revix-testimonial-slider', 'revix-testimonial-grids'];
        if ($settings['masonry'] === 'true') {
            $classes[] = 'revix-testimonial-masonry';
        }

        ?>
        <div class="revix-testimonial-wrapper-area">
            <div class="revix-testimonial-wrapper">
                <div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
                    <?php while ($query->have_posts()) : $query->the_post();
                        $testimonial_rating = get_post_meta(get_the_ID(), 'revixreviews_rating', true);
                    ?>
                        <div class="revix-testimonial-single-item">
                            <div class="revix-testimonial-client-info">
                                <h3><?php the_title(); ?></h3>
                            </div>
                            <div class="revix-testimonial-rating" aria-label="Rating: <?php echo intval($testimonial_rating); ?> out of 5 stars" title="<?php echo intval($testimonial_rating); ?> out of 5 stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= $testimonial_rating): ?>
                                        <!-- Filled Star SVG -->
                                        <svg xmlns="http://www.w3.org/2000/svg" height="18" viewBox="0 0 32 32" width="18" fill="#f5a623">
                                            <path d="m29.911 13.75-6.229 6.072 1.471 8.576c.064.375-.09.754-.398.978-.174.127-.381.191-.588.191-.159 0-.319-.038-.465-.115l-7.702-4.049-7.701 4.048c-.336.178-.745.149-1.053-.076-.308-.224-.462-.603-.398-.978l1.471-8.576-6.23-6.071c-.272-.266-.371-.664-.253-1.025s.431-.626.808-.681l8.609-1.25 3.85-7.802c.337-.683 1.457-.683 1.794 0l3.85 7.802 8.609 1.25c.377.055.69.319.808.681s.019.758-.253 1.025z" />
                                        </svg>
                                    <?php else: ?>
                                        <!-- Outlined Star SVG -->
                                        <svg xmlns="http://www.w3.org/2000/svg" height="18" viewBox="0 -10 512 512" width="18" fill="#ccc">
                                            <path d="m114.59375 491.140625c-5.609375 0-11.179688-1.75-15.933594-5.1875-8.855468-6.417969-12.992187-17.449219-10.582031-28.09375l32.9375-145.089844-111.703125-97.960937c-8.210938-7.167969-11.347656-18.519532-7.976562-28.90625 3.371093-10.367188 12.542968-17.707032 23.402343-18.710938l147.796875-13.417968 58.433594-136.746094c4.308594-10.046875 14.121094-16.535156 25.023438-16.535156 10.902343 0 20.714843 6.488281 25.023437 16.511718l58.433594 136.769532 147.773437 13.417968c10.882813.980469 20.054688 8.34375 23.425782 18.710938 3.371093 10.367187.253906 21.738281-7.957032 28.90625l-111.703125 97.941406 32.9375 145.085938c2.414063 10.667968-1.726562 21.699218-10.578125 28.097656-8.832031 6.398437-20.609375 6.890625-29.910156 1.300781l-127.445312-76.160156-127.445313 76.203125c-4.308594 2.558594-9.109375 3.863281-13.953125 3.863281zm141.398438-112.875c4.84375 0 9.640624 1.300781 13.953124 3.859375l120.277344 71.9375-31.085937-136.941406c-2.21875-9.746094 1.089843-19.921875 8.621093-26.515625l105.472657-92.5-139.542969-12.671875c-10.046875-.917969-18.6875-7.234375-22.613281-16.492188l-55.082031-129.046875-55.148438 129.066407c-3.882812 9.195312-12.523438 15.511718-22.546875 16.429687l-139.5625 12.671875 105.46875 92.5c7.554687 6.613281 10.859375 16.769531 8.621094 26.539062l-31.0625 136.9375 120.277343-71.914062c4.308594-2.558594 9.109376-3.859375 13.953126-3.859375z" />
                                        </svg>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>

                            <div class="revix-testimonial-content">
                                <?php echo wp_kses_post(wp_trim_words(get_the_content(), $settings['words'], '...')); ?>
                            </div>
                            <div class="revix-testimonial-quote">
                                <svg xmlns="http://www.w3.org/2000/svg" width="68" height="50" viewBox="0 0 68 50" fill="none">
                                    <mask id="mask0_147_7210" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="0" y="0" width="68" height="50">
                                        <path d="M68 0H0V49.8333H68V0Z" fill="white"></path>
                                    </mask>
                                    <g mask="url(#mask0_147_7210)">
                                        <path d="M18.4355 0.958008C27.4644 0.958008 33.9999 8.62457 33.9999 19.7796C33.9999 36.0712 21.8354 47.6097 4.41985 49.833C4.08581 49.8628 3.75216 49.773 3.47656 49.5792C3.20097 49.3853 3.00094 49.0996 2.91119 48.7717C2.82144 48.4439 2.84762 48.0944 2.9852 47.7841C3.12279 47.4738 3.36294 47.2224 3.66425 47.0731C10.3509 44.0831 13.751 40.2497 14.1666 36.4547C14.4006 35.2745 14.2186 34.0483 13.6524 32.9903C13.0862 31.9324 12.1718 31.1099 11.0688 30.6663C6.11989 29.478 2.83325 23.2297 2.83325 16.7897C2.83325 12.5909 4.47708 8.56403 7.40306 5.59502C10.329 2.62601 14.2975 0.958008 18.4355 0.958008Z" fill="white"></path>
                                        <path d="M52.4351 0.958008C61.464 0.958008 67.9995 8.62457 67.9995 19.7796C67.9995 36.0712 55.835 47.6097 38.4195 49.833C38.0854 49.8628 37.7518 49.773 37.4762 49.5792C37.2006 49.3853 37.0006 49.0996 36.9108 48.7717C36.8211 48.4439 36.8473 48.0944 36.9848 47.7841C37.1224 47.4738 37.3626 47.2224 37.6639 47.0731C44.3506 44.0831 47.7507 40.2497 48.1662 36.4547C48.4002 35.2745 48.2182 34.0483 47.652 32.9903C47.0858 31.9324 46.1714 31.1099 45.0684 30.6663C40.1195 29.478 36.8329 23.2297 36.8329 16.7897C36.8329 12.5909 38.4767 8.56403 41.4027 5.59502C44.3287 2.62601 48.2972 0.958008 52.4351 0.958008Z" fill="white"></path>
                                    </g>
                                </svg>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
        <?php
        wp_reset_postdata();
    }
}
