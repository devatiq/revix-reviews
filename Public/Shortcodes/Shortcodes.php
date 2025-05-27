<?php
namespace RevixReviews\Public\Shortcodes;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

use RevixReviews\Public\Shortcodes\Assets\Assets;
use RevixReviews\Public\Shortcodes\General\ReviewsShortcode;
use RevixReviews\Public\Shortcodes\General\ReviewsSubmitForm;
use RevixReviews\Public\Shortcodes\Trustpilot\TrustpilotShortcode;
use RevixReviews\Public\Shortcodes\Trustpilot\TrustpilotSummaryShortcode;
class Shortcodes
{
	protected $assets;
	protected $reviews_submit_form;
	protected $trustpilot_shortcode;
	protected $trustpilot_summary_shortcode;

	public function __construct()
	{
		$this->setConstants();
		$this->init_shortcodes();

	}


	public function setConstants()
	{
		define('REVIXREVIEWS_SHORTCODE_ASSETS', plugin_dir_url(__FILE__) . 'Assets');
		define('REVIXREVIEWS_SHORTCODE_PATH', plugin_dir_path(__FILE__));

	}

	private function init_shortcodes()
	{
		$this->assets = new Assets();
		$this->reviews_shortcode = new ReviewsShortcode();
		$this->reviews_submit_form = new ReviewsSubmitForm();
		$this->trustpilot_shortcode = new TrustpilotShortcode();
		$this->trustpilot_summary_shortcode = new TrustpilotSummaryShortcode();
	}
}