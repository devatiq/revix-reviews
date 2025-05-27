<?php
namespace RevixReviews\Public;


// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}


use RevixReviews\Public\Assets\Assets;
use RevixReviews\Public\Shortcodes\Shortcodes;

class Frontend {
    protected $assets;
	protected $Shortcodes;

    /**
     * Initialize the class and set its properties.
     */
    public function __construct() {
        $this->setConstants();
        $this->init_initialize();
    }

    public function setConstants()
    {
        define('REVIXREVIEWS_FRONTEND_ASSETS', plugin_dir_url(__FILE__) . 'Assets');
        define('REVIXREVIEWS_FRONTEND_PATH', plugin_dir_path(__FILE__));

    }

    /**
     * Initialize all shortcode classes
     *
     * @return void
     */
    private function init_initialize() {
        $this->assets = new Assets();
        $this->Shortcodes = new Shortcodes();
    }

}