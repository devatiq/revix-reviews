<?php 
namespace RevixReviews\Admin\Inc\Reviews\MetaBox;
/**
 * don't call the file directly.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ReviewsMetaBox
{
    public function __construct()
    {
        add_action('add_meta_boxes', array($this, 'add_custom_meta_boxes'));
        add_action('save_post', array($this, 'save_custom_meta_boxes'), 10, 2);
    }

    public function add_custom_meta_boxes()
    {
        add_meta_box(
            'revix_review_details',
            __('Review Details', 'revix-reviews'),
            array($this, 'render_meta_boxes'),
            'revix_reviews',
            'normal',
            'high'
        );
    }

    public function render_meta_boxes($post)
    {
        // Security field for validating request
        wp_nonce_field('revix_custom_fields', 'revix_custom_fields_nonce');

        // Retrieve current values based on post ID
        $name = get_post_meta($post->ID, 'revix_review_name', true);
        $email = get_post_meta($post->ID, 'revix_review_email', true);
        $rating = get_post_meta($post->ID, 'revix_review_rating', true);

        // HTML for the form fields
        echo '<p><label for="revix_review_name">' . esc_html__('Name:', 'revix-reviews') . '</label>';
        echo '<input type="text" id="revix_review_name" name="revix_review_name" value="' . esc_attr($name) . '" class="widefat" /></p>';

        echo '<p><label for="revix_review_email">' . esc_html__('Email:', 'revix-reviews') . '</label>';
        echo '<input type="email" id="revix_review_email" name="revix_review_email" value="' . esc_attr($email) . '" class="widefat" /></p>';

        // Dropdown for the rating        
        echo '<p><label for="revix_review_rating">' . esc_html__('Rating:', 'revix-reviews') . '</label>';
        echo '<select id="revix_review_rating" name="revix_review_rating" class="widefat">';
        for ($i = 1; $i <= 5; $i++) {
            // Check if rating is set or not. If not set, default to 5.
            $selected = selected($rating ? $rating : 5, $i, false);
            echo '<option value="' . esc_attr($i) . '"' . esc_html($selected) . '>' . esc_html($i) . '</option>';
        }
        echo '</select></p>';

    }

    public function save_custom_meta_boxes($post_id, $post)
    {
        // Verify the nonce before proceeding.
        if (!isset($_POST['revix_custom_fields_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['revix_custom_fields_nonce'])), 'revix_custom_fields')) {
            return $post_id;
        }

        // Skip autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        // Check the user's permissions.
        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }

        // Update the meta field in the database.
        update_post_meta($post_id, 'revix_review_name', sanitize_text_field($_POST['revix_review_name']));
        update_post_meta($post_id, 'revix_review_email', sanitize_email($_POST['revix_review_email']));
        update_post_meta($post_id, 'revix_review_rating', intval($_POST['revix_review_rating']));
    }
}
