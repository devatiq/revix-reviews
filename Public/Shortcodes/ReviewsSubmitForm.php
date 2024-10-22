<?php
namespace RevixReviews\Public\Shortcodes;

defined('ABSPATH') or die;

class ReviewsSubmitForm
{
    public function __construct()
    {    
        add_shortcode('revix_reviews_form', [$this, 'display_feedback_form']); // Display feedback form
        add_action('admin_post_nopriv_submit_revix_feedback', [$this, 'handle_submission']); // For non-logged-in users
        add_action('admin_post_submit_revix_feedback', [$this, 'handle_submission']); // For logged-in users
    }
     
    public function display_feedback_form($atts = [])
    {
        // Define default values for the attributes
        $defaults = [           
            'btn_text' => 'Submit Feedback', 
        ];

        // Override defaults with user-provided attributes
        $atts = shortcode_atts($defaults, $atts, 'revix_feedback_form');

        // Form HTML
        ?>
        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
            <?php wp_nonce_field('revix_feedback_nonce_action', 'revix_feedback_nonce'); ?>
            <input type="hidden" name="action" value="submit_revix_feedback">

            <p><label for="revix_name">
                    <?php esc_html_e('Name:', 'revix-reviews'); ?>
                </label>
                <input type="text" id="revix_name" name="revix_name" required>
            </p>

            <p><label for="revix_email">
                    <?php esc_html_e('Email:', 'revix-reviews'); ?>
                </label>
                <input type="email" id="revix_email" name="revix_email" required>
            </p>

            <p><label for="revix_subject">
                    <?php esc_html_e('Subject:', 'revix-reviews'); ?>
                </label>
                <input type="text" id="revix_subject" name="revix_subject" required>
            </p>

            <p><label for="revix_comments">
                    <?php esc_html_e('Comments:', 'revix-reviews'); ?>
                </label>
                <textarea id="revix_comments" name="revix_comments" required></textarea>
            </p>

            <p><label for="revix_rating">
                    <?php esc_html_e('Rating:', 'revix-reviews'); ?>
                </label>
                <select id="revix_rating" name="revix_rating" required>
                    <option value="">
                        <?php esc_html_e('Select a rating', 'revix-reviews'); ?>
                    </option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5" selected>5</option>
                </select>
            </p>

            <input type="submit" value="<?php echo esc_attr($atts['btn_text']); ?>">
        </form>
        <?php
    }

    public function handle_submission()
    {
        // Check nonce for security
        if (!isset($_POST['revix_feedback_nonce']) || !wp_verify_nonce(sanitize_text_field( wp_unslash ($_POST['revix_feedback_nonce'])), 'revix_feedback_nonce_action')) {
            wp_die('Security check failed');
        }

        // Server-side validation for required fields
        $required_fields = ['revix_name', 'revix_email', 'revix_subject', 'revix_comments', 'revix_rating'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                wp_die('Please fill all required fields.');
            }
        } 

        // Enhanced email validation
        if (!is_email($_POST['revix_email'])) {
            wp_die(__('Please enter a valid email address.', 'revix-reviews'));
        }

        $post_status = get_option('revix_review_status', 'pending'); // Default to 'pending' if not set
        // Sanitize and prepare post data
        $post_data = [
            'post_title' => sanitize_text_field($_POST['revix_subject']),
            'post_content' => sanitize_textarea_field($_POST['revix_comments']),
            'post_status' => $post_status,
            'post_type' => 'revix_reviews',
            'meta_input' => [
                'revix_review_name' => sanitize_text_field($_POST['revix_name']),
                'revix_review_email' => sanitize_email($_POST['revix_email']),
                'revix_review_rating' => intval($_POST['revix_rating']),
            ],
        ];

        // Insert the post
        $post_id = wp_insert_post($post_data);

        if ($post_id) {

            $redirect_url = get_option('revix_redirect_url');        
            // If a redirect URL is set and is a valid URL, redirect to it. Otherwise, redirect to the home page.
            if (!empty($redirect_url) && filter_var($redirect_url, FILTER_VALIDATE_URL)) {
                wp_redirect($redirect_url);
            } else {
                wp_redirect(home_url('/'));
            }
            
            exit;
        } else {
            wp_die('An error occurred while submitting your feedback.');
        }
    }
}
