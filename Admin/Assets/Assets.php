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
        $screen = get_current_screen();
        
        // Enqueue SweetAlert2 and settings JS on settings page
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Safe usage for asset loading only
        if ($screen && isset($_GET['page']) && $_GET['page'] === 'revixreviews_settings') {
            wp_enqueue_style('sweetalert2', REVIXREVIEWS_PUBLIC_ASSETS . 'css/sweetalert2.min.css', [], REVIXREVIEWS_VERSION);
            wp_enqueue_script('sweetalert2', REVIXREVIEWS_PUBLIC_ASSETS . 'js/sweetalert2.all.min.js', [], REVIXREVIEWS_VERSION, true);
        }
    }

    /**
     * Enqueue admin styles
     *
     * @return void
     */
    public function enqueue_admin_styles() {
        wp_enqueue_style('revix-reviews-admin', REVIXREVIEWS_ADMIN_ASSETS . 'css/admin-style.css', [], REVIXREVIEWS_VERSION);
    }
}