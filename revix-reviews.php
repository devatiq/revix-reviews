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
define( 'REVIXREVIEWS_VERSION', '1.0.0' );
// Define Plugin Path.
define( 'REVIXREVIEWS_PATH', plugin_dir_path( __FILE__ ) );
// Define Plugin URL.
define( 'REVIXREVIEWS_URL', plugin_dir_url( __FILE__ ) );

// Include the autoloader.
if ( file_exists( REVIXREVIEWS_PATH . 'vendor/autoload.php' ) ) {
	require_once REVIXREVIEWS_PATH . 'vendor/autoload.php';
}

/**
 * Initializes the Revix Reviews plugin by registering all classes and services.
 */
function revixreviews_initialize() {

	if ( class_exists( 'RevixReviews\Manager' ) ) {
		new \RevixReviews\Manager();
	}
}

add_action( 'plugins_loaded', 'revixreviews_initialize' );
