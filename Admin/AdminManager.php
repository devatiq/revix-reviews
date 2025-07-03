<?php
namespace RevixReviews\Admin;


// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

use RevixReviews\Admin\Assets\Assets;
use RevixReviews\Admin\Inc\Reviews\PostTypes\Reviews;
use RevixReviews\Admin\Inc\Reviews\MetaBox\ReviewsMetaBox;

use RevixReviews\Admin\Inc\Dashboard\Settings\Settings;
use RevixReviews\Admin\Core\Core;

class AdminManager
{

    protected $assets;
    protected $reviews;
    protected $reviews_meta_box;
   

    protected $settings;
    protected $trustpilot_settings;

    protected $core;

    /**
     * 
     * Initialize the class and set its properties.
     */
    public function __construct()
    {
        $this->setConstants();
        $this->init_admin_classes();
    }
    public function setConstants()
    {
        define('REVIXREVIEWS_ADMIN_ASSETS', plugin_dir_url(__FILE__) . 'Assets');
        define('REVIXREVIEWS_ADMIN_PATH', plugin_dir_path(__FILE__));

    }
    /**
     * Initialize all admin classes
     *
     * @return void
     */
    private function init_admin_classes()
    {
        $this->assets = new Assets();
        $this->reviews = new Reviews();
        $this->reviews_meta_box = new ReviewsMetaBox();       
        $this->settings = new Settings();
        $this->core = new Core();
    }
}