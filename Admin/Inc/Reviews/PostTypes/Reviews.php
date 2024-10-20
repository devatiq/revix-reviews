<?php 
namespace RevixReviews\Admin\Inc\Reviews\PostType;
/**
 * don't call the file directly.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Reviews {
    public function register() {
        add_action( 'init', [ $this, 'create_review_post_type' ] );
    }

    public function create_review_post_type() {
        $labels = [
            'name'               => __( 'Reviews', 'revix-reviews' ),
            'singular_name'      => __( 'Review', 'revix-reviews' ),
            'menu_name'          => __( 'Reviews', 'revix-reviews' ),
            'name_admin_bar'     => __( 'Review', 'revix-reviews' ),
            'add_new'            => __( 'Add New', 'revix-reviews' ),
            'add_new_item'       => __( 'Add New Review', 'revix-reviews' ),
            'new_item'           => __( 'New Review', 'revix-reviews' ),
            'edit_item'          => __( 'Edit Review', 'revix-reviews' ),
            'view_item'          => __( 'View Review', 'revix-reviews' ),
            'all_items'          => __( 'All Reviews', 'revix-reviews' ),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'has_archive'        => true,
            'supports'           => [ 'title', 'editor', 'thumbnail', 'comments' ],
            'show_in_rest'       => true,
        ];

        register_post_type( 'revix_reviews', $args );
    }
}
