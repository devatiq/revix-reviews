<?php
namespace RevixReviews\Admin\Assets;

/**
 * The Assets class for managing admin-side scripts and styles
 */
class Assets {
    /**
     * Initialize the class
     */
    public function __construct() {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_styles']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    /**
     * Enqueue admin scripts and styles
     *
     * @return void
     */
    public function enqueue_assets() {
        
    }

    /**
     * Enqueue admin styles
     *
     * @return void
     */
    public function enqueue_admin_styles() {
        wp_enqueue_style('revix-reviews-admin', REVIXREVIEWS_ADMIN_ASSETS . '/css/admin-style.css', [], REVIXREVIEWS_VERSION);
    }
}