<?php
namespace RevixReviews\Admin;


// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

use RevixReviews\Admin\Inc\Reviews\PostTypes\Reviews;
use RevixReviews\Admin\Inc\Reviews\MetaBox\ReviewsMetaBox;

use RevixReviews\Admin\Inc\Dashboard\Settings\Settings;

class AdminManager
{

    protected $reviews;
    protected $reviews_meta_box;
   

    protected $settings;
    protected $trustpilot_settings;

    /**
     * 
     * Initialize the class and set its properties.
     */
    public function __construct()
    {
        $this->init_admin_classes();
    }

    /**
     * Initialize all admin classes
     *
     * @return void
     */
    private function init_admin_classes()
    {
        $this->reviews = new Reviews();
        $this->reviews_meta_box = new ReviewsMetaBox();       
        $this->settings = new Settings();
    }
}