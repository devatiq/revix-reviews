<?php
namespace RevixReviews\Public\Assets;

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
	 * Enqueue frontend styles.
	 *
	 * Enqueues the frontend styles for the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook The current WordPress admin page.
	 */
	public function enqueue_styles( $hook ) {

		wp_enqueue_style( 'revix-style', REVIXREVIEWS_FRONTEND_ASSETS . '/css/style.css', array(), REVIXREVIEWS_VERSION );
		
	}

	/**
	 * Enqueue frontend scripts.
	 *
	 * Enqueues the frontend scripts for the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook The current WordPress admin page.
	 */
	public function enqueue_scripts( $hook ) {

	}
}
