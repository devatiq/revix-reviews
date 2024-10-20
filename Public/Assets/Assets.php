<?php 
namespace RevixReviews\Public\Assets;
/**
 * disable direct access
 */
if (!defined('ABSPATH')) {
    die;
}

class Assets {
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
    }

    public function enqueue_styles($hook) {

        wp_enqueue_style('revix-style', REVIX_REVIEWS_URL . 'assets/css/style.css');
        
    }
    
}