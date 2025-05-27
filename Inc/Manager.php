<?php
namespace RevixReviews;

/**
 * don't call the file directly.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



use RevixReviews\Admin\AdminManager;
use RevixReviews\Public\Frontend;


class Manager {

	protected $frontend;
	protected $admin_manager;

	/**
	 * Class constructor
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		$this->init();
	}
	/**
	 * Initialize the manager by setting up hooks and classes.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init() {
		$this->init_classes();
	}

	/**
	 * Initialize other classes
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init_classes() {
		$this->frontend            = new Frontend();
		$this->admin_manager       = new AdminManager();	
	}
}
