<?php
namespace RevixReviews;

/**
 * don't call the file directly.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use RevixReviews\Admin\Inc\Reviews\PostTypes\Reviews;
use RevixReviews\Admin\Inc\Reviews\MetaBox\ReviewsMetaBox;
use RevixReviews\Public\Assets\Assets;
use RevixReviews\Public\Shortcodes\ReviewsShortcode;
use RevixReviews\Public\Shortcodes\ReviewsSubmitForm;
use RevixReviews\Admin\Inc\Dashboard\Settings\Settings;


use RevixReviews\Public\Trustpilot\TrustpilotShortcode;
use RevixReviews\Public\Trustpilot\TrustpilotSummaryShortcode;
class Manager {
	protected $reviews;
	protected $reviews_meta_box;
	protected $assets;
	protected $reviews_shortcode;
	protected $reviews_submit_form;
	protected $settings;

	protected $trustpilot_settings;
	protected $trustpilot_shortcode;
	protected $trustpilot_summary_shortcode;

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
		$this->reviews             = new Reviews();
		$this->reviews_meta_box    = new ReviewsMetaBox();
		$this->assets              = new Assets();
		$this->reviews_shortcode   = new ReviewsShortcode();
		$this->reviews_submit_form = new ReviewsSubmitForm();
		$this->settings            = new Settings();
		$this->trustpilot_shortcode = new TrustpilotShortcode();
		$this->trustpilot_summary_shortcode = new TrustpilotSummaryShortcode();
	}
}
