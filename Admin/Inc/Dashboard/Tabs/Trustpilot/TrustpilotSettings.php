<?php
namespace RevixReviews\Admin\Inc\Dashboard\Tabs\Trustpilot;

use RevixReviews\Public\Inc\Integrations\Trustpilot\TrustpilotFetcher;

class TrustpilotSettings
{
	public function __construct()
	{
		add_action('admin_init', [$this, 'register_settings']);
		add_action('update_option_revix_trustpilot_url', [$this, 'clear_cache_on_save'], 10, 2);
		add_action('update_option_revix_trustpilot_api_key', [$this, 'clear_cache_on_save'], 10, 2);
		add_action('update_option_revix_trustpilot_business_unit_id', [$this, 'clear_cache_on_save'], 10, 2);
	}

	/**
	 * Clear Trustpilot cache when settings are saved.
	 */
	public function clear_cache_on_save($old_value, $new_value)
	{
		if ($old_value === $new_value) {
			return;
		}

		TrustpilotFetcher::clear_cache();
	}

	public function register_settings()
	{
		register_setting('revixreviews_trustpilot', 'revix_trustpilot_url', ['sanitize_callback' => 'esc_url_raw']);
		register_setting('revixreviews_trustpilot', 'revix_trustpilot_api_key', ['sanitize_callback' => 'sanitize_text_field']);
		register_setting('revixreviews_trustpilot', 'revix_trustpilot_business_unit_id', ['sanitize_callback' => 'sanitize_text_field']);

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Safe usage, just reading `tab` for UI display logic only
		$current_tab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : 'general';
		if ($current_tab !== 'trustpilot') {
			return;
		}

		add_settings_section(
			'revix_trustpilot_section',
			'',
			'__return_false',
			'revixreviews_trustpilot'
		);

		add_settings_field(
			'revix_trustpilot_api_key',
			__('Trustpilot API Key', 'revix-reviews'),
			function () {
				$value = get_option('revix_trustpilot_api_key', '');
				echo '<input type="text" name="revix_trustpilot_api_key" value="' . esc_attr($value) . '" class="revixreviews-input" autocomplete="off" placeholder="' . esc_attr__('Your Trustpilot Public API key', 'revix-reviews') . '">';
				echo '<p class="revixreviews-description">' . esc_html__('Required. Create an application in Trustpilot Business → Developers to get a Public API key. Server-side use only.', 'revix-reviews') . '</p>';
			},
			'revixreviews_trustpilot',
			'revix_trustpilot_section'
		);

		add_settings_field(
			'revix_trustpilot_url',
			__('Trustpilot Review Page URL', 'revix-reviews'),
			function () {
				echo '<input type="text" name="revix_trustpilot_url" value="' . esc_attr(get_option('revix_trustpilot_url')) . '" class="revixreviews-input" placeholder="https://www.trustpilot.com/review/yourdomain.com">';
				echo '<p class="revixreviews-description">' . esc_html__('Example: https://www.trustpilot.com/review/yourdomain.com — used to resolve your Business Unit ID when the ID field is empty.', 'revix-reviews') . '</p>';
			},
			'revixreviews_trustpilot',
			'revix_trustpilot_section'
		);

		add_settings_field(
			'revix_trustpilot_business_unit_id',
			__('Business Unit ID (optional)', 'revix-reviews'),
			function () {
				$value = get_option('revix_trustpilot_business_unit_id', '');
				echo '<input type="text" name="revix_trustpilot_business_unit_id" value="' . esc_attr($value) . '" class="revixreviews-input" placeholder="507f191e810c19729de860ea">';
				echo '<p class="revixreviews-description">' . esc_html__('Recommended. Find it via Trustpilot Find Business Unit API using your domain, or leave empty to auto-resolve from the URL above.', 'revix-reviews') . '</p>';
			},
			'revixreviews_trustpilot',
			'revix_trustpilot_section'
		);
	}
}
