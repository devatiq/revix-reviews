<?php
/**
 * Assets class for Revix Reviews Elementor Addons.
 *
 * Handles loading of CSS and JS assets for Elementor widgets.
 *
 * @package RevixReviews\Elementor\Assets
 * @since 1.3.0
 */

namespace RevixReviews\Elementor\Assets;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Class Assets
 *
 * Manages asset loading for Elementor widgets.
 *
 * @package RevixReviews\Elementor\Assets
 * @since 1.3.0
 */
class Assets
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        add_action('elementor/frontend/after_enqueue_styles', [$this, 'enqueue_styles']);
        add_action('elementor/frontend/after_register_scripts', [$this, 'enqueue_scripts']);
    }

    /**
     * Enqueue styles.
     */
    public function enqueue_styles()
    {
        // Enqueue main Elementor widgets stylesheet
        wp_enqueue_style(
            'revixreviews-elementor-widgets',
            REVIXREVIEWS_ELEMENTOR_ASSETS . '/css/widgets.css',
            [],
            '1.2.6'
        );
    }

    /**
     * Enqueue scripts.
     */
    public function enqueue_scripts()
    {
        // Enqueue main Elementor widgets script
        wp_enqueue_script(
            'revixreviews-elementor-widgets',
            REVIXREVIEWS_ELEMENTOR_ASSETS . '/js/widgets.js',
            ['jquery'],
            '1.2.6',
            true
        );
    }
}
