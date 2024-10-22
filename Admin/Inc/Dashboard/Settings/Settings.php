<?php
namespace RevixReviews\Admin\Inc\Dashboard\Settings;

defined('ABSPATH') or die('This is not the place you deserve!');

class Settings
{
    /**
     * Initializes the plugin by adding the menu page and setting up the settings fields
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        add_action('admin_menu', array($this, 'revix_add_admin_menu'));
        add_action('admin_init', array($this, 'revix_settings_init'));
    }

    /**
     * Add a submenu page to the Revix Reviews post type menu to display the settings page
     *
     * @since 1.0.0
     */
    public function revix_add_admin_menu()
    {
        add_submenu_page(
            'edit.php?post_type=revix_reviews',
            __('Revix Reviews Settings', 'revix-reviews'),
            __('Settings', 'revix-reviews'),
            'manage_options',
            'revix_reviews_settings',
            array($this, 'revix_create_settings_page')
        );
    }

    /**
     * Renders the settings page for Revix Reviews.
     * Displays a form with settings fields, sections, and submit button.
     */
    public function revix_create_settings_page()
    {
        ?>
        <div class="wrap revix_admin_wrap">
            <h2><?php echo esc_html(get_admin_page_title()); ?></h2>
            <form action="options.php" method="post">
                <?php
                settings_errors();
                settings_fields('revix_reviews');
                do_settings_sections('revix_reviews');
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
    public function revix_settings_init()
    {
        register_setting('revix_reviews', 'revix_redirect_url', ['sanitize_callback' => 'esc_url_raw']);
        register_setting('revix_reviews', 'revix_review_status', ['sanitize_callback' => 'sanitize_text_field']);


        add_settings_section(
            'revix_reviews_main_section',
            __('Main Settings', 'revix-reviews'),
            array($this, 'revix_reviews_main_section_cb'),
            'revix_reviews'
        );
        //redirect url
        add_settings_field(
            'revix_redirect_url',
            __('Redirect URL', 'revix-reviews'),
            array($this, 'revix_redirect_url_field_cb'),
            'revix_reviews',
            'revix_reviews_main_section'
        );

        // post status
        add_settings_field(
            'revix_review_status', // ID
            __('Default Review Status', 'revix-reviews'), // Title
            array($this, 'revix_review_status_field_cb'), // Callback function
            'revix_reviews', // Page
            'revix_reviews_main_section' // Section           
        );
    }

    /**
     * Renders the main settings section description for the Revix Reviews settings page.
     *
     * Outputs a paragraph with a brief description of the settings page.
     *
     * @since 1.0.0
     */
    public function revix_reviews_main_section_cb()
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
    public function revix_redirect_url_field_cb()
    {
        $redirect_url = get_option('revix_redirect_url');
        echo '<input type="text" id="revix_redirect_url" class="regular-text" name="revix_redirect_url" value="' . esc_attr($redirect_url) . '" />';
    }

    /**
     * Renders the post status field for the Revix Reviews settings page.
     *
     * Outputs a dropdown containing the current post status value from the database.
     * The options are 'publish', 'pending', and 'draft'.
     *
     * @since 1.0.0
     */
    public function revix_review_status_field_cb()
    {
        $post_status = get_option('revix_review_status', 'pending'); // Default to 'pending' if not set
        ?>
        <select id="revix_review_status" name="revix_review_status">
            <option value="publish" <?php selected($post_status, 'publish'); ?>>
                <?php echo esc_html__('Publish', 'revix-reviews'); ?>
            </option>
            <option value="pending" <?php selected($post_status, 'pending'); ?>>
                <?php echo esc_html__('Pending', 'revix-reviews'); ?>
            </option>
            <option value="draft" <?php selected($post_status, 'draft'); ?>><?php echo esc_html__('Draft', 'revix-reviews'); ?>
            </option>
        </select>
        <?php
    }
}
