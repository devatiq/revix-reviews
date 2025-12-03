<?php
/**
 * Quick Cache Clear for Trustpilot Reviews
 * 
 * Add this to your theme's functions.php temporarily:
 * 
 * Visit: yoursite.com/?clear_trustpilot_cache=1
 */

add_action('init', function() {
    if (isset($_GET['clear_trustpilot_cache']) && current_user_can('manage_options')) {
        global $wpdb;
        $deleted = $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_revix_trustpilot_%'");
        $deleted += $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_revix_trustpilot_%'");
        
        wp_die(
            '<div style="font-family:Arial;padding:50px;text-align:center;">
                <h1 style="color:#0a0;">✓ Cache Cleared!</h1>
                <p>Deleted ' . $deleted . ' cache entries.</p>
                <p>The next page load will fetch fresh Trustpilot data.</p>
                <p><a href="' . home_url() . '" style="background:#0073aa;color:white;padding:10px 20px;text-decoration:none;border-radius:3px;display:inline-block;margin-top:20px;">Go to Homepage</a></p>
            </div>'
        );
    }
});
