<?php
namespace RevixReviews\Public\Shortcodes\General;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ReviewsSubmitForm
{

	public function __construct()
	{
		add_shortcode('revixreviews_form', array($this, 'display_feedback_form')); // Display feedback form.
		add_action('admin_post_nopriv_submit_revixreviews_feedback', array($this, 'handle_submission')); // For non-logged-in users.
		add_action('admin_post_submit_revixreviews_feedback', array($this, 'handle_submission')); // For logged-in users.
	}

	public function display_feedback_form($atts = array())
	{
		// Define default values for the attributes.
		$defaults = array(
			'btn_text' => esc_html__('Submit Feedback', 'revix-reviews'),
		);

		// Override defaults with user-provided attributes.
		$atts = shortcode_atts($defaults, $atts, 'revixreviews_form');

		ob_start(); // Start buffering
		// Form HTML
		?>
		<form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
			<?php wp_nonce_field('revixreviews_feedback_nonce_action', 'revixreviews_feedback_nonce'); ?>
			<input type="hidden" name="action" value="submit_revixreviews_feedback">

			<p><label for="revixreviews_name">
					<?php echo esc_html__('Name:', 'revix-reviews'); ?>
				</label>
				<input type="text" id="revixreviews_name" name="revixreviews_name" required>
			</p>

			<p><label for="revixreviews_email">
					<?php echo esc_html__('Email:', 'revix-reviews'); ?>
				</label>
				<input type="email" id="revixreviews_email" name="revixreviews_email" required>
			</p>

			<p><label for="revixreviews_subject">
					<?php echo esc_html__('Subject:', 'revix-reviews'); ?>
				</label>
				<input type="text" id="revixreviews_subject" name="revixreviews_subject" required>
			</p>

			<p><label for="revixreviews_comments">
					<?php echo esc_html__('Comments:', 'revix-reviews'); ?>
				</label>
				<textarea id="revixreviews_comments" name="revixreviews_comments" required></textarea>
			</p>

			<p><label for="revixreviews_rating">
					<?php echo esc_html__('Rating:', 'revix-reviews'); ?>
				</label>
				<select id="revixreviews_rating" name="revixreviews_rating" required>
					<option value="">
						<?php echo esc_html__('Select a rating', 'revix-reviews'); ?>
					</option>
					<option value="1"><?php echo esc_html__('1', 'revix-reviews'); ?></option>
					<option value="2"><?php echo esc_html__('2', 'revix-reviews'); ?></option>
					<option value="3"><?php echo esc_html__('3', 'revix-reviews'); ?></option>
					<option value="4"><?php echo esc_html__('4', 'revix-reviews'); ?></option>
					<option value="5" selected><?php echo esc_html__('5', 'revix-reviews'); ?></option>
				</select>
			</p>

			<input type="submit" value="<?php echo esc_attr($atts['btn_text']); ?>">
		</form>
		<?php

		return ob_get_clean(); // Return the buffer contents
	}

	public function handle_submission()
	{
		// Check nonce for security.
		if (!isset($_POST['revixreviews_feedback_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['revixreviews_feedback_nonce'])), 'revixreviews_feedback_nonce_action')) {
			wp_die('Security check failed');
		}

		// Server-side validation for required fields.
		$required_fields = array('revixreviews_name', 'revixreviews_email', 'revixreviews_subject', 'revixreviews_comments', 'revixreviews_rating');
		foreach ($required_fields as $field) {
			if (empty($_POST[$field])) {
				wp_die('Please fill all required fields.');
			}
		}

		// Sanitize inputs after ensuring they exist and are non-empty.
		$name = isset($_POST['revixreviews_name']) ? sanitize_text_field(wp_unslash($_POST['revixreviews_name'])) : '';
		$email = isset($_POST['revixreviews_email']) ? sanitize_email(wp_unslash($_POST['revixreviews_email'])) : '';
		$subject = isset($_POST['revixreviews_subject']) ? sanitize_text_field(wp_unslash($_POST['revixreviews_subject'])) : '';
		$comments = isset($_POST['revixreviews_comments']) ? sanitize_textarea_field(wp_unslash($_POST['revixreviews_comments'])) : '';
		$rating = isset($_POST['revixreviews_rating']) ? intval(wp_unslash($_POST['revixreviews_rating'])) : '';

		// Enhanced email validation.
		if (!is_email($email)) {
			wp_die(esc_html__('Please enter a valid email address.', 'revix-reviews'));
		}

		$post_status = get_option('revixreviews_status', 'pending'); // Default to 'pending' if not set.
		// Prepare post data.
		$post_data = array(
			'post_title' => $subject,
			'post_content' => $comments,
			'post_status' => $post_status,
			'post_type' => 'revixreviews',
			'meta_input' => array(
				'revixreviews_name' => $name,
				'revixreviews_email' => $email,
				'revixreviews_rating' => $rating,
			),
		);

		// Insert the post.
		$post_id = wp_insert_post($post_data);

		if ($post_id) {

			$redirect_url = get_option('revixreviews_redirect_url');
			// If a redirect URL is set and is a valid URL, redirect to it. Otherwise, redirect to the home page.
			if (!empty($redirect_url) && filter_var($redirect_url, FILTER_VALIDATE_URL)) {
				wp_safe_redirect($redirect_url);
			} else {
				wp_safe_redirect(home_url('/'));
			}

			exit;
		} else {
			wp_die(esc_html__('An error occurred while submitting your feedback.', 'revix-reviews'));
		}
	}
}
