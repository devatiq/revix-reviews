<?php
namespace RevixReviews\Admin\Trustpilot;

class TrustpilotSettings {
    public function __construct() {
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function register_settings() {
        add_settings_section('revix_trustpilot_section', 'Trustpilot Reviews Settings', null, 'revix-reviews');

        add_settings_field('revix_trustpilot_url', 'Trustpilot Review Page URL', function () {
            echo '<input type="text" name="revix_trustpilot_url" value="' . esc_attr(get_option('revix_trustpilot_url')) . '" class="regular-text" placeholder="https://www.trustpilot.com/review/yourdomain.com">';
        }, 'revix-reviews', 'revix_trustpilot_section');

        register_setting('revix-reviews-group', 'revix_trustpilot_url');
    }
}
