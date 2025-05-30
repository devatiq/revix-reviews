<?php
namespace RevixReviews\Public\Shortcodes\Assets;

/**
 * disable direct access
 */
if ( ! defined( 'ABSPATH' ) ) {
    die;
}

class Assets {

    /**
     * Construct method.
     * 
     * Registers the `enqueue_styles` method to run when the `wp_enqueue_scripts` action is fired.
     * 
     * @since 1.0.0
     */
    public function __construct() {
       
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    }


    /**
     * Enqueue shortcode styles.
     * 
     * Enqueues the styles specific to shortcodes for the plugin.
     * 
     * @since 1.0.0
     * 
     * @param string $hook The current WordPress admin page.
     */
    public function enqueue_styles( $hook ) {
        wp_enqueue_style('revix-trustpilot', REVIXREVIEWS_SHORTCODE_ASSETS. '/css/trustpilot.css', array(), REVIXREVIEWS_VERSION );
       
    }

    /**
     * Enqueue shortcode scripts.
     * 
     * Enqueues the scripts specific to shortcodes for the plugin.
     * 
     * @since 1.0.0
     * 
     * @param string $hook The current WordPress admin page.
     */
    public function enqueue_scripts( $hook ) {
        wp_enqueue_script( 'revix-trustpilot', REVIXREVIEWS_SHORTCODE_ASSETS . '/js/trustpilot.js', array('jquery'), REVIXREVIEWS_VERSION, true );
    }
}