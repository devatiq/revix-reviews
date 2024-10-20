<?php
namespace RevixReviews;

/**
 * don't call the file directly.
 */

if (!defined('ABSPATH')) {
    exit;
}

use RevixReviews\Admin\Inc\Reviews\PostTypes\Reviews;

class Manager
{

    protected $reviews;

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
    }


}
