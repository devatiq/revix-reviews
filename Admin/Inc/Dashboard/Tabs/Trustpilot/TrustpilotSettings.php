<?php
namespace RevixReviews\Admin\Inc\Dashboard\Tabs\Trustpilot;

class TrustpilotSettings {
    public function __construct() {
        add_action('admin_init', [$this, 'register_settings']);
    }

	public function register_settings() {
        register_setting('revixreviews_trustpilot', 'revix_trustpilot_url', ['sanitize_callback' => 'esc_url_raw']);

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Safe usage, just reading `tab` for UI display logic only
		$current_tab = isset($_GET['tab']) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'general';
		// Only register Trustpilot section if we're in the TrustPilot tab
		if ($current_tab !== 'trustpilot') {
			return;
		}

		add_settings_section(
			'revix_trustpilot_section',
			__('Trustpilot Reviews Settings', 'revix-reviews'),
			function () {
				echo '<p>' . esc_html__('Enter your public Trustpilot business review page URL.', 'revix-reviews') . '</p>';
			},
			'revixreviews_trustpilot'
		);

		add_settings_field(
			'revix_trustpilot_url',
			__('Trustpilot Review Page URL', 'revix-reviews'),
			function () {
				echo '<input type="text" name="revix_trustpilot_url" value="' . esc_attr(get_option('revix_trustpilot_url')) . '" class="regular-text" placeholder="https://www.trustpilot.com/review/yourdomain.com">';
                echo '<p class="description">' . esc_html__('Enter the full URL of your business\'s Trustpilot review page. This is used to fetch your latest reviews.', 'revix-reviews') . '</p>';
			},
			'revixreviews_trustpilot',
			'revix_trustpilot_section'
		);
		


	}
}
