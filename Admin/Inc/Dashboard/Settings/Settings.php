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
		add_action('admin_enqueue_scripts', array($this, 'enqueue_settings_assets'));
		add_action('wp_ajax_revixreviews_toggle_setting', array($this, 'handle_ajax_toggle'));
	}

	private function class_initialize() { 

		$this->tabs = new Tabs();
	}

	/**
	 * Enqueue CSS and JavaScript for settings page
	 *
	 * @since 1.3.0
	 */
	public function enqueue_settings_assets($hook)
	{
		// Only load on our settings page
		if ('revixreviews_page_revixreviews_settings' !== $hook) {
			return;
		}

		// Enqueue CSS
		wp_enqueue_style(
			'revixreviews-settings',
			REVIXREVIEWS_ADMIN_ASSETS . 'css/settings.css',
			array(),
			REVIXREVIEWS_VERSION
		);

		// Enqueue JavaScript
		wp_enqueue_script(
			'revixreviews-settings',
			REVIXREVIEWS_ADMIN_ASSETS . 'js/settings.js',
			array('jquery'),
			REVIXREVIEWS_VERSION,
			true
		);

		// Localize script with AJAX URL and nonce
		wp_localize_script(
			'revixreviews-settings',
			'revixReviewsSettings',
			array(
				'ajaxUrl'    => admin_url('admin-ajax.php'),
				'nonce'      => wp_create_nonce('revixreviews_settings_nonce'),
				'savingText' => __('Saving...', 'revix-reviews'),
			)
		);
	}

	/**
	 * Handle AJAX toggle switch changes
	 *
	 * @since 1.3.0
	 */
	public function handle_ajax_toggle()
	{
		// Verify nonce
		check_ajax_referer('revixreviews_settings_nonce', 'nonce');

		// Check user capabilities
		if (!current_user_can('manage_options')) {
			wp_send_json_error(array('message' => __('You do not have permission to perform this action.', 'revix-reviews')));
			wp_die(); // Ensure script terminates
		}

		// Get and sanitize input
		$option_name = isset($_POST['option']) ? sanitize_key($_POST['option']) : '';
		$value       = isset($_POST['value']) ? absint($_POST['value']) : 0;

		// Whitelist allowed options for extra security
		$allowed_options = array(
			'revixreviews_elementor_active',
			'revixreviews_google_summary',
			'revixreviews_trustpilot_summary',
			'revixreviews_trustpilot_reviews',
			'revixreviews_google_reviews',
		);

		if (empty($option_name) || !in_array($option_name, $allowed_options, true)) {
			wp_send_json_error(array('message' => __('Invalid option name.', 'revix-reviews')));
			wp_die();
		}

		// Validate value is 0 or 1
		if ($value !== 0 && $value !== 1) {
			wp_send_json_error(array('message' => __('Invalid value.', 'revix-reviews')));
			wp_die();
		}

		// Update the option
		$updated = update_option($option_name, $value);

		if ($updated || get_option($option_name) == $value) {
			wp_send_json_success(array(
				'message' => __('Setting saved successfully.', 'revix-reviews'),
				'option'  => sanitize_key($option_name),
				'value'   => absint($value),
			));
		} else {
			wp_send_json_error(array('message' => __('Failed to save setting.', 'revix-reviews')));
		}
		
		wp_die(); // Always terminate AJAX properly
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
		// Check user capabilities
		if (!current_user_can('manage_options')) {
			wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'revix-reviews'));
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- No action taken, just reading for UI display
		$active_tab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : 'general';
		
		// Validate tab name
		$allowed_tabs = array('general', 'trustpilot', 'google');
		if (!in_array($active_tab, $allowed_tabs, true)) {
			$active_tab = 'general';
		}
		?>
		<div class="wrap revixreviews-admin-wrap">
			<h1 class="revixreviews-page-title">
				<span class="revixreviews-title-icon">⚙️</span>
				<?php echo esc_html(get_admin_page_title()); ?>
			</h1>
			
			<?php Tabs::render_tabs($active_tab); ?>
	
			<div class="revixreviews-settings-layout">
				<!-- Main Settings Area -->
				<div class="revixreviews-settings-main">
					<?php settings_errors(); ?>
					
					<form action="options.php" method="post" id="revixreviews-settings-form">
						<?php
						if ($active_tab === 'trustpilot') {
							settings_fields('revixreviews_trustpilot');
							$this->render_trustpilot_settings();
						} elseif ($active_tab === 'google') {
							settings_fields('revixreviews_google');
							$this->render_google_settings();
						} else {
							settings_fields('revixreviews');
							$this->render_general_settings();
						}
	
						submit_button(__('Save All Settings', 'revix-reviews'), 'primary large', 'submit', true, array('id' => 'revixreviews-submit-btn'));
						?>
					</form>
				</div>
	
				<!-- Sidebar Area -->
				<div class="revixreviews-settings-sidebar">
					<div class="revixreviews-settings-card">
						<div class="revixreviews-card-header">
							<h3><?php esc_html_e('📺 Video Tutorial', 'revix-reviews'); ?></h3>
						</div>
						<div class="revixreviews-card-body">
							<div class="revixreviews-video-wrapper">
								<iframe width="100%" height="100%" src="https://www.youtube.com/embed/yB0dJ70jS2Y" 
									title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen>
								</iframe>
							</div>
						</div>
					</div>

					<div class="revixreviews-settings-card">
						<div class="revixreviews-card-header">
							<h3><?php esc_html_e('💡 Quick Tips', 'revix-reviews'); ?></h3>
						</div>
						<div class="revixreviews-card-body">
							<ul class="revixreviews-tips-list">
								<li><?php esc_html_e('Toggle switches save automatically', 'revix-reviews'); ?></li>
								<li><?php esc_html_e('Use "Save All Settings" for other changes', 'revix-reviews'); ?></li>
								<li><?php esc_html_e('Enable Elementor widgets to use in page builder', 'revix-reviews'); ?></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render general settings tab content
	 *
	 * @since 1.3.0
	 */
	private function render_general_settings()
	{
		?>
		<div class="revixreviews-settings-grid">
			<div class="revixreviews-settings-card">
				<div class="revixreviews-card-header">
					<h3><?php esc_html_e('General Settings', 'revix-reviews'); ?></h3>
					<p class="revixreviews-card-description"><?php esc_html_e('Configure basic plugin settings and default behaviors', 'revix-reviews'); ?></p>
				</div>
				<div class="revixreviews-card-body">
					<!-- Redirect URL -->
					<div class="revixreviews-field">
						<label for="revixreviews_redirect_url" class="revixreviews-label">
							<?php esc_html_e('Redirect URL', 'revix-reviews'); ?>
						</label>
						<input type="text" id="revixreviews_redirect_url" class="revixreviews-input" name="revixreviews_redirect_url" 
							value="<?php echo esc_attr(get_option('revixreviews_redirect_url')); ?>" 
							placeholder="<?php echo esc_url(home_url('/thank-you')); ?>" />
						<p class="revixreviews-description">
							<?php esc_html_e('Enter the URL to redirect users to after they submit a review.', 'revix-reviews'); ?>
						</p>
					</div>

					<!-- Default Review Status -->
					<div class="revixreviews-field">
						<label for="revixreviews_status" class="revixreviews-label">
							<?php esc_html_e('Default Review Status', 'revix-reviews'); ?>
						</label>
						<select id="revixreviews_status" name="revixreviews_status" class="revixreviews-select">
							<option value="publish" <?php selected(get_option('revixreviews_status', 'pending'), 'publish'); ?>>
								<?php esc_html_e('Publish', 'revix-reviews'); ?>
							</option>
							<option value="pending" <?php selected(get_option('revixreviews_status', 'pending'), 'pending'); ?>>
								<?php esc_html_e('Pending', 'revix-reviews'); ?>
							</option>
							<option value="draft" <?php selected(get_option('revixreviews_status', 'pending'), 'draft'); ?>>
								<?php esc_html_e('Draft', 'revix-reviews'); ?>
							</option>
						</select>
						<p class="revixreviews-description">
							<?php esc_html_e('Select the default status for new reviews. "Publish" makes them public immediately, "Pending" requires admin approval, and "Draft" saves them as drafts.', 'revix-reviews'); ?>
						</p>
					</div>
				</div>
			</div>

			<div class="revixreviews-settings-card">
				<div class="revixreviews-card-header">
					<h3><?php esc_html_e('Elementor Integration', 'revix-reviews'); ?></h3>
					<p class="revixreviews-card-description"><?php esc_html_e('Enable or disable Elementor widgets for the page builder', 'revix-reviews'); ?></p>
				</div>
				<div class="revixreviews-card-body">
					<div class="revixreviews-toggles-grid">
						<!-- Enable Elementor Widgets -->
						<?php $this->render_toggle_field(
							'revixreviews_elementor_active',
							__('Enable Elementor Widgets', 'revix-reviews'),
							__('When enabled, Revix Reviews widgets will be available in the Elementor editor. Requires Elementor plugin to be installed and activated.', 'revix-reviews'),
							get_option('revixreviews_elementor_active', 0)
						); ?>

						<!-- Google Summary Widget -->
						<?php $this->render_toggle_field(
							'revixreviews_google_summary',
							__('Google Summary Widget', 'revix-reviews'),
							__('Display Google review ratings summary in Elementor.', 'revix-reviews'),
							get_option('revixreviews_google_summary', 1)
						); ?>

						<!-- Trustpilot Summary Widget -->
						<?php $this->render_toggle_field(
							'revixreviews_trustpilot_summary',
							__('Trustpilot Summary Widget', 'revix-reviews'),
							__('Display Trustpilot review ratings summary in Elementor.', 'revix-reviews'),
							get_option('revixreviews_trustpilot_summary', 1)
						); ?>
					<!-- Trustpilot Reviews Widget -->
					<?php $this->render_toggle_field(
						'revixreviews_trustpilot_reviews',
						__('Trustpilot Reviews Widget', 'revix-reviews'),
						__('Display full Trustpilot reviews list in Elementor.', 'revix-reviews'),
						get_option('revixreviews_trustpilot_reviews', 1)
					); ?>
						<!-- Google Reviews Widget -->
						<?php $this->render_toggle_field(
							'revixreviews_google_reviews',
							__('Google Reviews Widget', 'revix-reviews'),
							__('Display full Google reviews list in Elementor.', 'revix-reviews'),
							get_option('revixreviews_google_reviews', 1)
						); ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render trustpilot settings tab content
	 *
	 * @since 1.3.0
	 */
	private function render_trustpilot_settings()
	{
		?>
		<div class="revixreviews-settings-card">
			<div class="revixreviews-card-header">
				<h3><?php esc_html_e('Trustpilot Reviews Settings', 'revix-reviews'); ?></h3>
				<p class="revixreviews-card-description"><?php esc_html_e('Enter your public Trustpilot business review page URL', 'revix-reviews'); ?></p>
			</div>
			<div class="revixreviews-card-body">
				<?php do_settings_sections('revixreviews_trustpilot'); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render google settings tab content
	 *
	 * @since 1.3.0
	 */
	private function render_google_settings()
	{
		?>
		<div class="revixreviews-settings-card">
			<div class="revixreviews-card-header">
				<h3><?php esc_html_e('Google Maps Reviews Settings', 'revix-reviews'); ?></h3>
				<p class="revixreviews-card-description"><?php esc_html_e('Enter your Google Maps API key and Place ID to fetch reviews', 'revix-reviews'); ?></p>
			</div>
			<div class="revixreviews-card-body">
				<?php do_settings_sections('revixreviews_google'); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render a toggle switch field with AJAX functionality
	 *
	 * @since 1.3.0
	 * @param string $option_name The option name
	 * @param string $label The field label
	 * @param string $description The field description
	 * @param int $current_value The current value (0 or 1)
	 */
	private function render_toggle_field($option_name, $label, $description, $current_value)
	{
		?>
		<div class="revixreviews-toggle-field">
			<div class="revixreviews-toggle-header">
				<label for="<?php echo esc_attr($option_name); ?>" class="revixreviews-toggle-label">
					<?php echo esc_html($label); ?>
				</label>
				<label class="revixreviews-toggle-switch">
					<input type="checkbox" 
						id="<?php echo esc_attr($option_name); ?>" 
						name="<?php echo esc_attr($option_name); ?>" 
						value="1"
						class="revixreviews-ajax-toggle"
						data-option="<?php echo esc_attr($option_name); ?>"
						<?php checked($current_value, 1); ?> />
					<span class="revixreviews-toggle-slider"></span>
				</label>
			</div>
			<?php if (!empty($description)) : ?>
				<p class="revixreviews-description" style="margin-top: 12px;">
					<?php echo esc_html($description); ?>
				</p>
			<?php endif; ?>
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
		register_setting('revixreviews', 'revixreviews_elementor_active', array('sanitize_callback' => 'absint'));
		register_setting('revixreviews', 'revixreviews_google_summary', array('sanitize_callback' => 'absint'));
		register_setting('revixreviews', 'revixreviews_trustpilot_summary', array('sanitize_callback' => 'absint'));
		register_setting('revixreviews', 'revixreviews_trustpilot_reviews', array('sanitize_callback' => 'absint'));
		register_setting('revixreviews', 'revixreviews_google_reviews', array('sanitize_callback' => 'absint'));

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

		// Elementor widgets.
		add_settings_field(
			'revixreviews_elementor_active',
			__('Enable Elementor Widgets', 'revix-reviews'),
			array($this, 'revixreviews_elementor_active_field_cb'),
			'revixreviews',
			'revixreviews_main_section'
		);

		// Google Summary widget.
		add_settings_field(
			'revixreviews_google_summary',
			__('Enable Google Summary Widget', 'revix-reviews'),
			array($this, 'revixreviews_google_summary_field_cb'),
			'revixreviews',
			'revixreviews_main_section'
		);

		// Trustpilot Summary widget.
		add_settings_field(
			'revixreviews_trustpilot_summary',
			__('Enable Trustpilot Summary Widget', 'revix-reviews'),
			array($this, 'revixreviews_trustpilot_summary_field_cb'),
			'revixreviews',
			'revixreviews_main_section'
		);

		// Google Reviews widget.
		add_settings_field(
			'revixreviews_google_reviews',
			__('Enable Google Reviews Widget', 'revix-reviews'),
			array($this, 'revixreviews_google_reviews_field_cb'),
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
		$placeholder  = esc_url(home_url('/thank-you'));
		echo '<input type="text" id="revixreviews_redirect_url" class="regular-text" name="revixreviews_redirect_url" value="' . esc_attr($redirect_url) . '" placeholder="' . $placeholder . '" />' . '<p class="description">' . esc_html__('Enter the URL to redirect users to after they submit a review.', 'revix-reviews') . '</p>';
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
<p class="description">
            <?php echo esc_html__('Select the default status for new reviews. \'Publish\' makes them public immediately, \'Pending\' requires admin approval, and \'Draft\' saves them as drafts.', 'revix-reviews'); ?>
        </p>
        <?php
	}

	/**
	 * Renders the Elementor widgets checkbox field for the Revix Reviews settings page.
	 *
	 * Outputs a checkbox to enable/disable Elementor widgets integration.
	 *
	 * @since 1.3.0
	 */
	public function revixreviews_elementor_active_field_cb()
	{
		$is_active = get_option('revixreviews_elementor_active', 0);
		?>
		<label for="revixreviews_elementor_active">
			<input type="checkbox" id="revixreviews_elementor_active" name="revixreviews_elementor_active" value="1" <?php checked($is_active, 1); ?> />
			<?php echo esc_html__('Enable Elementor widgets for Revix Reviews', 'revix-reviews'); ?>
		</label>
		<p class="description">
			<?php echo esc_html__('When enabled, Revix Reviews widgets will be available in the Elementor editor. Requires Elementor plugin to be installed and activated.', 'revix-reviews'); ?>
		</p>
		<?php
	}

	/**
	 * Renders the Google Summary widget checkbox field for the Revix Reviews settings page.
	 *
	 * Outputs a checkbox to enable/disable Google Summary Elementor widget.
	 *
	 * @since 1.3.0
	 */
	public function revixreviews_google_summary_field_cb()
	{
		$is_active = get_option('revixreviews_google_summary', 1);
		?>
		<label for="revixreviews_google_summary">
			<input type="checkbox" id="revixreviews_google_summary" name="revixreviews_google_summary" value="1" <?php checked($is_active, 1); ?> />
			<?php echo esc_html__('Enable Google Summary widget in Elementor', 'revix-reviews'); ?>
		</label>
		<p class="description">
			<?php echo esc_html__('When enabled, the Google Reviews Summary widget will be available in the Elementor editor.', 'revix-reviews'); ?>
		</p>
		<?php
	}

	/**
	 * Renders the Trustpilot Summary widget checkbox field for the Revix Reviews settings page.
	 *
	 * Outputs a checkbox to enable/disable Trustpilot Summary Elementor widget.
	 *
	 * @since 1.3.0
	 */
	public function revixreviews_trustpilot_summary_field_cb()
	{
		$is_active = get_option('revixreviews_trustpilot_summary', 1);
		?>
		<label for="revixreviews_trustpilot_summary">
			<input type="checkbox" id="revixreviews_trustpilot_summary" name="revixreviews_trustpilot_summary" value="1" <?php checked($is_active, 1); ?> />
			<?php echo esc_html__('Enable Trustpilot Summary widget in Elementor', 'revix-reviews'); ?>
		</label>
		<p class="description">
			<?php echo esc_html__('When enabled, the Trustpilot Summary widget will be available in the Elementor editor.', 'revix-reviews'); ?>
		</p>
		<?php
	}

	/**
	 * Renders the Google Reviews widget checkbox field for the Revix Reviews settings page.
	 *
	 * Outputs a checkbox to enable/disable Google Reviews Elementor widget.
	 *
	 * @since 1.3.0
	 */
	public function revixreviews_google_reviews_field_cb()
	{
		$is_active = get_option('revixreviews_google_reviews', 1);
		?>
		<label for="revixreviews_google_reviews">
			<input type="checkbox" id="revixreviews_google_reviews" name="revixreviews_google_reviews" value="1" <?php checked($is_active, 1); ?> />
			<?php echo esc_html__('Enable Google Reviews widget in Elementor', 'revix-reviews'); ?>
		</label>
		<p class="description">
			<?php echo esc_html__('When enabled, the Google Reviews widget will be available in the Elementor editor.', 'revix-reviews'); ?>
		</p>
		<?php
	}
}
