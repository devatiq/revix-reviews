<?php
namespace RevixReviews\Public;


// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}


use RevixReviews\Public\Assets\Assets;
use RevixReviews\Public\Shortcodes\Shortcodes;
use RevixReviews\Elementor\Configuration as ElementorConfiguration;

class Frontend {
    protected $assets;
	protected $Shortcodes;
    protected $elementorConfiguration;

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

        // Initialize Elementor widgets if enabled in settings
        $elementor_active = get_option('revixreviews_elementor_active', 0);
        if ($elementor_active) {
            $this->elementorConfiguration = ElementorConfiguration::instance();
        }
    }

}