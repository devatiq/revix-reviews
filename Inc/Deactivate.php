<?php
namespace RevixReviews;

/**
 * Don't call the file directly.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Deactivate {

	/**
	 * This method is responsible for deactivating the plugin.
	 */
	public static function deactivate() {
		flush_rewrite_rules();
	}
}
