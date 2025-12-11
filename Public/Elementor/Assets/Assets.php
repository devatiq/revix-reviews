<?php
/**
 * Assets class for Revix Reviews Elementor Addons.
 *
 * Handles loading of CSS and JS assets for Elementor widgets.
 *
 * @package RevixReviews\Elementor\Assets
 * @since 1.3.0
 */

namespace RevixReviews\Public\Elementor\Assets;

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
        // Enqueue main frontend styles (Trustpilot, Google, etc.)
        wp_enqueue_style(
            'revixreviews-frontend-style',
            REVIXREVIEWS_FRONTEND_ASSETS . '/css/style.css',
            [],
            REVIXREVIEWS_VERSION
        );

        // Enqueue Trustpilot styles
        wp_enqueue_style(
            'revixreviews-trustpilot-style',
            REVIXREVIEWS_FRONTEND_ASSETS . '/../Shortcodes/Assets/css/trustpilot.css',
            [],
            REVIXREVIEWS_VERSION
        );

        // Enqueue Google reviews styles
        wp_enqueue_style(
            'revixreviews-google-reviews-style',
            REVIXREVIEWS_FRONTEND_ASSETS . '/../Shortcodes/Assets/css/google-review.css',
            [],
            REVIXREVIEWS_VERSION
        );

        // Enqueue general shortcode styles
        wp_enqueue_style(
            'revixreviews-general-style',
            REVIXREVIEWS_FRONTEND_ASSETS . '/../Shortcodes/Assets/css/general.css',
            [],
            REVIXREVIEWS_VERSION
        );

        // Enqueue Elementor-specific widgets stylesheet
        wp_enqueue_style(
            'revixreviews-elementor-widgets',
            REVIXREVIEWS_ELEMENTOR_ASSETS . '/css/widgets.css',
            [],
            REVIXREVIEWS_VERSION
        );
    }

    /**
     * Enqueue scripts.
     */
    public function enqueue_scripts()
    {
        // Enqueue Masonry library
        wp_enqueue_script(
            'revixreviews-masonry',
            REVIXREVIEWS_FRONTEND_ASSETS . '/../Shortcodes/Assets/js/masonry.pkgd.min.js',
            ['jquery'],
            REVIXREVIEWS_VERSION,
            true
        );

        // Enqueue Trustpilot scripts
        wp_enqueue_script(
            'revixreviews-trustpilot-script',
            REVIXREVIEWS_FRONTEND_ASSETS . '/../Shortcodes/Assets/js/trustpilot.js',
            ['jquery'],
            REVIXREVIEWS_VERSION,
            true
        );

        // Enqueue main Elementor widgets script
        wp_enqueue_script(
            'revixreviews-elementor-widgets',
            REVIXREVIEWS_ELEMENTOR_ASSETS . '/js/widgets.js',
            ['jquery'],
            REVIXREVIEWS_VERSION,
            true
        );
    }
}
