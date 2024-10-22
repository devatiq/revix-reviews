<?php
/**
 * Plugin Name: Revix Reviews
 * Version: 1.0.0
 * Plugin URI: https://github.com/devatiq/revix-reviews
 * Description: A WordPress plugin for managing reviews.
 * Author: ABCPlugin
 * Author URI: https://abcplugin.com/
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: revix-reviews
 * Namespace: RevixReviews
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Define Plugin Version.
define( 'REVIX_REVIEWS_VERSION', '1.0.0' );
// Define Plugin Path.
define( 'REVIX_REVIEWS_PATH', plugin_dir_path( __FILE__ ) );
// Define Plugin URL.
define( 'REVIX_REVIEWS_URL', plugin_dir_url( __FILE__ ) );

// Include the autoloader.
if ( file_exists( REVIX_REVIEWS_PATH . 'vendor/autoload.php' ) ) {
	require_once REVIX_REVIEWS_PATH . 'vendor/autoload.php';
}

/**
 * Initializes the Revix Reviews plugin by registering all classes and services.
 */
function revix_reviews_initialize() {

	if ( class_exists( 'RevixReviews\Manager' ) ) {
		new \RevixReviews\Manager();
	}
}

add_action( 'plugins_loaded', 'revix_reviews_initialize' );
