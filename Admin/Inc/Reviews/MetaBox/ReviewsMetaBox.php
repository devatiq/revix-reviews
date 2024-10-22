<?php
namespace RevixReviews\Admin\Inc\Reviews\MetaBox;

/**
 * don't call the file directly.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ReviewsMetaBox {

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_review_meta_data' ), 10, 2 );
	}

	/**
	 * Add a meta box to the "revix_reviews" post type, which holds review details.
	 *
	 * @return void
	 */
	public function register_meta_boxes() {
		add_meta_box(
			'revix_review_details',
			__( 'Review Details', 'revix-reviews' ),
			array( $this, 'register_review_metabox_fields' ),
			'revix_reviews',
			'normal',
			'high'
		);
	}

	/**
	 * Renders the meta box containing review details, such as name, email, and rating.
	 *
	 * @param WP_Post $post The post object for which the meta box is being rendered.
	 *
	 * @return void
	 */
	public function register_review_metabox_fields( $post ) {
		// Security field for validating request.
		wp_nonce_field( 'revix_fields_nonce', 'revix_review_meta_nonce' );

		// Retrieve current values based on post ID.
		$name   = get_post_meta( $post->ID, 'revix_review_name', true );
		$email  = get_post_meta( $post->ID, 'revix_review_email', true );
		$rating = get_post_meta( $post->ID, 'revix_review_rating', true );

		// HTML for the form fields.
		echo '<p><label for="revix_review_name">' . esc_html__( 'Name:', 'revix-reviews' ) . '</label>';
		echo '<input type="text" id="revix_review_name" name="revix_review_name" value="' . esc_attr( $name ) . '" class="widefat" /></p>';

		echo '<p><label for="revix_review_email">' . esc_html__( 'Email:', 'revix-reviews' ) . '</label>';
		echo '<input type="email" id="revix_review_email" name="revix_review_email" value="' . esc_attr( $email ) . '" class="widefat" /></p>';

		// Dropdown for the rating.
		echo '<p><label for="revix_review_rating">' . esc_html__( 'Rating:', 'revix-reviews' ) . '</label>';
		echo '<select id="revix_review_rating" name="revix_review_rating" class="widefat">';
		for ( $i = 1; $i <= 5; $i++ ) {
			// Check if rating is set or not. If not set, default to 5.
			$selected = selected( $rating ? $rating : 5, $i, false );
			echo '<option value="' . esc_attr( $i ) . '"' . esc_html( $selected ) . '>' . esc_html( $i ) . '</option>';
		}
		echo '</select></p>';
	}

	/**
	 * Handles saving the custom meta boxes for the "revix_reviews" post type.
	 *
	 * @param int     $post_id The ID of the post being saved.
	 * @param WP_Post $post    The post object being saved.
	 *
	 * @return int The post ID.
	 */
	public function save_review_meta_data( $post_id, $post ) {
		// Verify the nonce before proceeding.
		if ( ! isset( $_POST['revix_review_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['revix_review_meta_nonce'] ) ), 'revix_fields_nonce' ) ) {
			return $post_id;
		}

		// Skip autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Check the user's permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}	

		// update custom fields.
		if ( isset( $_POST['revix_review_name'] ) ) {
			update_post_meta( $post_id, 'revix_review_name', sanitize_text_field( wp_unslash($_POST['revix_review_name'] )) );
		}
		//
		if ( isset( $_POST['revix_review_email'] ) ) {
			update_post_meta( $post_id, 'revix_review_email', sanitize_email( wp_unslash($_POST['revix_review_email']) ) );
		}

		if ( isset( $_POST['revix_review_rating'] ) ) {
			update_post_meta( $post_id, 'revix_review_rating', intval( $_POST['revix_review_rating'] ) );
		}	
	}
}
