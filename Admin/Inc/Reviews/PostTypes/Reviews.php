<?php
namespace RevixReviews\Admin\Inc\Reviews\PostTypes;

/**
 * don't call the file directly.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Reviews {

	/**
	 * Constructor for the Reviews class.
	 *
	 * Registers a custom post type for reviews by hooking into the 'init' action.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_review_post_type' ) );
	}

	/**
	 * Creates a custom post type for reviews.
	 *
	 * Registers a custom post type with the key 'revix_reviews' and assigns it
	 * labels, public visibility, and support for various features like title,
	 * editor, thumbnails, and comments. The post type is also registered to be
	 * shown in the WordPress REST API.
	 */
	public function register_review_post_type() {
		$labels = array(
			'name'           => __( 'Reviews', 'revix-reviews' ),
			'singular_name'  => __( 'Review', 'revix-reviews' ),
			'menu_name'      => __( 'Reviews', 'revix-reviews' ),
			'name_admin_bar' => __( 'Review', 'revix-reviews' ),
			'add_new'        => __( 'Add New', 'revix-reviews' ),
			'add_new_item'   => __( 'Add New Review', 'revix-reviews' ),
			'new_item'       => __( 'New Review', 'revix-reviews' ),
			'edit_item'      => __( 'Edit Review', 'revix-reviews' ),
			'view_item'      => __( 'View Review', 'revix-reviews' ),
			'all_items'      => __( 'All Reviews', 'revix-reviews' ),
		);

		$args = array(
			'labels'       => $labels,
			'public'       => true,
			'has_archive'  => true,
			'supports'     => array( 'title', 'editor', 'thumbnail', 'comments' ),
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => 'revix-reviews' ),
			'publicly_queryable' => false
		);

		register_post_type( 'revix_reviews', $args );
	}
}
