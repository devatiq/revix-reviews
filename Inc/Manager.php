<?php
namespace RevixReviews;

/**
 * don't call the file directly.
 */

if (!defined('ABSPATH')) {
    exit;
}

use RevixReviews\Admin\Inc\Reviews\PostTypes\Reviews;
use RevixReviews\Admin\Inc\Reviews\MetaBox\ReviewsMetaBox;

class Manager
{

    protected $reviews;
    protected $reviews_meta_box;

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
        $this->reviews = new Reviews();
        $this->reviews_meta_box = new ReviewsMetaBox();
    }


}
