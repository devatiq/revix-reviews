<?php 
namespace RevixReviews\Base;

/**
 * Don't call the file directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Activate {
    /**
     * This method is responsible for activating the plugin.
     */
    public static function activate() {
        flush_rewrite_rules();
    }
}
