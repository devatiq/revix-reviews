<?php
/**
 * Admin Manager
 *
 * This file is responsible for managing all the admin-side functionality of the plugin.
 *
 * @package RevixReviews
 * @subpackage Admin
 * @since 1.0.0
 */

namespace RevixReviews\Admin;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

use RevixReviews\Admin\Assets\Assets;
use RevixReviews\Admin\Core\Core;
use RevixReviews\Admin\Inc\Dashboard\Settings\Settings;
use RevixReviews\Admin\Inc\Reviews\MetaBox\ReviewsMetaBox;
use RevixReviews\Admin\Inc\Reviews\PostTypes\Reviews;

/**
 * Class AdminManager
 *
 * @package RevixReviews\Admin
 * @since 1.0.0
 */
class AdminManager
{
	/**
	 * The instance of the Assets class.
	 *
	 * @var Assets
	 * @since 1.0.0
	 */
	protected $assets;

	/**
	 * The instance of the Reviews class.
	 *
	 * @var Reviews
	 * @since 1.0.0
	 */
	protected $reviews;

	/**
	 * The instance of the ReviewsMetaBox class.
	 *
	 * @var ReviewsMetaBox
	 * @since 1.0.0
	 */
	protected $reviews_meta_box;

	/**
	 * The instance of the Settings class.
	 *
	 * @var Settings
	 * @since 1.0.0
	 */
	protected $settings;

	/**
	 * The instance of the Core class.
	 *
	 * @var Core
	 * @since 1.0.0
	 */
	protected $core;

	/**
	 * AdminManager constructor.
	 *
	 * Initializes the admin-side of the plugin.
	 *
	 * @since 1.0.0
	 */
	public function __construct()
	{
		$this->set_constants();
		$this->init_admin_classes();
	}

	/**
	 * Sets the constants for the admin area.
	 *
	 * @since 1.0.0
	 */
	public function set_constants()
	{
		define('REVIXREVIEWS_ADMIN_ASSETS', plugin_dir_url(__FILE__) . 'Assets/');
		define('REVIXREVIEWS_ADMIN_PATH', plugin_dir_path(__FILE__));
	}

	/**
	 * Initializes all the admin classes.
	 *
	 * @since 1.0.0
	 */
	private function init_admin_classes()
	{
		$this->assets             = new Assets();
		$this->reviews            = new Reviews();
		$this->reviews_meta_box     = new ReviewsMetaBox();
		$this->settings           = new Settings();
		$this->core               = new Core();
	}
}