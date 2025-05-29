<?php
namespace RevixReviews\Admin\Inc\Dashboard\Settings;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

use RevixReviews\Admin\Inc\Dashboard\Tabs\Tabs;
class Settings
{

	protected $tabs;
	/**
	 * Initializes the plugin by adding the menu page and setting up the settings fields
	 *
	 * @since 1.0.0
	 */
	public function __construct()
	{
		$this->class_initialize();
		add_action('admin_menu', array($this, 'revixreviews_add_admin_menu'));
		add_action('admin_init', array($this, 'revixreviews_settings_init'));
	}

	private function class_initialize() { 

		$this->tabs = new Tabs();
	}

	/**
	 * Add a submenu page to the Revix Reviews post type menu to display the settings page
	 *
	 * @since 1.0.0
	 */
	public function revixreviews_add_admin_menu()
	{
		add_submenu_page(
			'edit.php?post_type=revixreviews',
			__('Revix Reviews Settings', 'revix-reviews'),
			__('Settings', 'revix-reviews'),
			'manage_options',
			'revixreviews_settings',
			array($this, 'revixreviews_create_settings_page')
		);
	}

	/**
	 * Renders the settings page for Revix Reviews.
	 * Displays a form with settings fields, sections, and submit button.
	 */
	public function revixreviews_create_settings_page()
	{
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$active_tab = isset($_GET['tab']) ? sanitize_text_field( wp_unslash($_GET['tab']) ) : 'general';
		?>
		<div class="wrap revixreviews_admin_wrap">
			<h2><?php echo esc_html(get_admin_page_title()); ?></h2>
			<?php Tabs::render_tabs($active_tab); ?>
			<form action="options.php" method="post">
				<?php
				settings_errors();				

				if ($active_tab === 'trustpilot') {
					settings_fields('revixreviews_trustpilot');
					do_settings_sections('revixreviews_trustpilot');
				} elseif ($active_tab === 'google') {
					settings_fields('revixreviews_google');
					do_settings_sections('revixreviews_google');
				} else {
					settings_fields('revixreviews');
					do_settings_sections('revixreviews');
				}
						

				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Initializes the settings for Revix Reviews plugin.
	 * Registers setting options for redirect URL and default review status.
	 * Adds a main settings section for the settings page.
	 */
	public function revixreviews_settings_init()
	{
		register_setting('revixreviews', 'revixreviews_redirect_url', array('sanitize_callback' => 'esc_url_raw'));
		register_setting('revixreviews', 'revixreviews_status', array('sanitize_callback' => 'sanitize_text_field'));

		add_settings_section(
			'revixreviews_main_section',
			__('Main Settings', 'revix-reviews'),
			array($this, 'revixreviews_main_section_cb'),
			'revixreviews'
		);
		// redirect url.
		add_settings_field(
			'revixreviews_redirect_url',
			__('Redirect URL', 'revix-reviews'),
			array($this, 'revixreviews_redirect_url_field_cb'),
			'revixreviews',
			'revixreviews_main_section'
		);

		// post status.
		add_settings_field(
			'revixreviews_status',
			__('Default Review Status', 'revix-reviews'),
			array($this, 'revixreviews_status_field_cb'),
			'revixreviews',
			'revixreviews_main_section'
		);


	}

	/**
	 * Renders the main settings section description for the Revix Reviews settings page.
	 *
	 * Outputs a paragraph with a brief description of the settings page.
	 *
	 * @since 1.0.0
	 */
	public function revixreviews_main_section_cb()
	{
		echo '<p>' . esc_html__('Set your preferences for the Revix Reviews plugin.', 'revix-reviews') . '</p>';
	}

	/**
	 * Renders the redirect URL field for the Revix Reviews settings page.
	 *
	 * Outputs an input field containing the current redirect URL value from the database.
	 *
	 * @since 1.0.0
	 */
	public function revixreviews_redirect_url_field_cb()
	{
		$redirect_url = get_option('revixreviews_redirect_url');
		echo '<input type="text" id="revixreviews_redirect_url" class="regular-text" name="revixreviews_redirect_url" value="' . esc_attr($redirect_url) . '" />';
	}

	/**
	 * Renders the post status field for the Revix Reviews settings page.
	 *
	 * Outputs a dropdown containing the current post status value from the database.
	 * The options are 'publish', 'pending', and 'draft'.
	 *
	 * @since 1.0.0
	 */
	public function revixreviews_status_field_cb()
	{
		$post_status = get_option('revixreviews_status', 'pending');
		?>
		<select id="revixreviews_status" name="revixreviews_status">
			<option value="publish" <?php selected($post_status, 'publish'); ?>>
				<?php echo esc_html__('Publish', 'revix-reviews'); ?>
			</option>
			<option value="pending" <?php selected($post_status, 'pending'); ?>>
				<?php echo esc_html__('Pending', 'revix-reviews'); ?>
			</option>
			<option value="draft" <?php selected($post_status, 'draft'); ?>>
				<?php echo esc_html__('Draft', 'revix-reviews'); ?>
			</option>
		</select>
		<?php
	}
}
