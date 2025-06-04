<?php
namespace RevixReviews\Admin\Inc\Dashboard\Tabs\Google;

class GoogleSettings
{
    public function __construct()
    {
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function register_settings()
    {
        register_setting('revixreviews_google', 'revix_google_api_key', ['sanitize_callback' => 'sanitize_text_field']);
        register_setting('revixreviews_google', 'revix_google_place_id', ['sanitize_callback' => 'sanitize_text_field']);

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $current_tab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : 'general';
        if ($current_tab !== 'google') {
            return;
        }

        add_settings_section(
            'revix_google_section',
            __('Google Maps Reviews Settings', 'revix-reviews'),
            function () {
                echo '<p>' . esc_html__('Enter your Google Maps API key and Place ID to fetch reviews.', 'revix-reviews') . '</p>';
            },
            'revixreviews_google'
        );


        // Google API Key Field (as password)
        add_settings_field(
            'revix_google_api_key',
            __('Google API Key', 'revix-reviews'),
            function () {
                $value = esc_attr(get_option('revix_google_api_key'));
                echo '<input type="password" id="revix_google_api_key" name="revix_google_api_key" value="' . $value . '" class="regular-text" placeholder="AIza..." />';
                echo '<label> <input type="checkbox" id="revix_toggle_api_key" onclick="document.getElementById(\'revix_google_api_key\').type = this.checked ? \'text\' : \'password\'"> ' . esc_html__('Show', 'revix-reviews') . '</label>';
                echo '<p class="description">' . esc_html__('Get your API key from the Google Cloud Console. Enable the Places API and restrict it for security.', 'revix-reviews') . '</p>';
            },
            'revixreviews_google',
            'revix_google_section'
        );


        // Place ID Field
        add_settings_field(
            'revix_google_place_id',
            __('Google Place ID', 'revix-reviews'),
            function () {
                echo '<input type="text" name="revix_google_place_id" value="' . esc_attr(get_option('revix_google_place_id')) . '" class="regular-text" placeholder="ChIJN1t_tDeuEmsRUsoyG83frY4">';
                echo '<p class="description">' . esc_html__('Find your Place ID using the official Google Place ID Finder:', 'revix-reviews') . ' <a href="https://developers.google.com/maps/documentation/places/web-service/place-id" target="_blank" rel="noopener noreferrer">' . esc_html__('Google Place ID Lookup', 'revix-reviews') . '</a></p>';
            },
            'revixreviews_google',
            'revix_google_section'
        );
    }
}