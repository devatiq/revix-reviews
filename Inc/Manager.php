<?php
namespace RevixReviews;

/**
 * don't call the file directly.
 */

if (!defined('ABSPATH')) {
    exit;
}

use RevixReviews\Activate;
use RevixReviews\Deactivate;
use RevixReviews\Admin\Inc\Reviews\PostTypes\Reviews;
use RevixReviews\Admin\Inc\Reviews\MetaBox\ReviewsMetaBox;
use RevixReviews\Public\Assets\Assets;
use RevixReviews\Public\Shortcodes\ReviewsShortcode;
use RevixReviews\Public\Shortcodes\ReviewsSubmitForm;
use RevixReviews\Admin\Inc\Dashboard\Settings\Settings;

class Manager
{
    protected $activate;
    protected $deactivate;
    protected $reviews;
    protected $reviews_meta_box;    
    protected $assets;
    protected $reviewsShortcode;
    protected $reviewsSubmitForm;
    protected $settings;



    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * Initialize the manager by setting up hooks and classes.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function init()
    {
        $this->init_classes();
    }

    /**
     * Initialize other classes
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function init_classes()
    {
        
        $this->activate = new Activate();
        $this->deactivate = new Deactivate();
        $this->reviews = new Reviews();
        $this->reviews_meta_box = new ReviewsMetaBox();
        $this->assets = new Assets();
        $this->reviewsShortcode = new ReviewsShortcode();
        $this->reviewsSubmitForm = new ReviewsSubmitForm();
        $this->settings = new Settings();
    }


}
