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
		add_action('wp_ajax_submit_revixreviews_feedback_ajax', array($this, 'handle_submission_ajax'));
		add_action('wp_ajax_nopriv_submit_revixreviews_feedback_ajax', array($this, 'handle_submission_ajax'));

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
		<form id="revixreviews-feedback-form" method="post">
			<?php wp_nonce_field('revixreviews_feedback_nonce_action', 'revixreviews_feedback_nonce'); ?>
			<input type="hidden" name="action" value="submit_revixreviews_feedback_ajax">


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

			<p>
				<label><?php echo esc_html__('Rating:', 'revix-reviews'); ?></label>
				<div class="rating-stars">
					<span class="star" data-value="1">&#9733;</span>
					<span class="star" data-value="2">&#9733;</span>
					<span class="star" data-value="3">&#9733;</span>
					<span class="star" data-value="4">&#9733;</span>
					<span class="star" data-value="5">&#9733;</span>
				</div>
			<input type="hidden" id="revixreviews_rating" name="revixreviews_rating" value="0" required>
			</p>

			<input type="submit" value="<?php echo esc_attr($atts['btn_text']); ?>">
		</form>

		<?php		

		return ob_get_clean(); // Return the buffer contents
	}

	public function handle_submission_ajax()
	{
		check_ajax_referer('revixreviews_feedback_nonce_action', 'nonce');

		// Validate required fields
		$required_fields = ['revixreviews_name', 'revixreviews_email', 'revixreviews_subject', 'revixreviews_comments', 'revixreviews_rating'];
		foreach ($required_fields as $field) {
			if (empty($_POST[$field])) {
				wp_send_json_error(['message' => 'Please fill all required fields.']);
				wp_die();
			}
		}

		// Sanitize inputs
		$name = sanitize_text_field(wp_unslash($_POST['revixreviews_name']));
		$email = sanitize_email(wp_unslash($_POST['revixreviews_email']));
		$subject = sanitize_text_field(wp_unslash($_POST['revixreviews_subject']));
		$comments = sanitize_textarea_field(wp_unslash($_POST['revixreviews_comments']));
		$rating = intval(wp_unslash($_POST['revixreviews_rating']));

		if (!is_email($email)) {
			wp_send_json_error(['message' => 'Invalid email address.']);
			wp_die();
		}

		$post_status = get_option('revixreviews_status', 'pending');
		$post_id = wp_insert_post([
			'post_title' => $subject,
			'post_content' => $comments,
			'post_status' => $post_status,
			'post_type' => 'revixreviews',
			'meta_input' => [
				'revixreviews_name' => $name,
				'revixreviews_email' => $email,
				'revixreviews_rating' => $rating,
			],
		]);

		if ($post_id) {
			wp_send_json_success(['message' => 'Your feedback has been submitted successfully.']);
		} else {
			wp_send_json_error(['message' => 'Failed to submit feedback.']);
		}
		
		wp_die();
	}

}
