<?php
/**
 * Debug script for Revix Reviews
 * 
 * Place this file in the plugin root and access it via:
 * yoursite.com/wp-content/plugins/revix-reviews/debug-reviews.php
 * 
 * This will help diagnose issues with Trustpilot and Google Reviews
 */

// Load WordPress
require_once('../../../wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    wp_die('You must be an administrator to access this page.');
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Revix Reviews Debug</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .section { background: white; padding: 20px; margin: 20px 0; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .success { color: #0a0; font-weight: bold; }
        .error { color: #c00; font-weight: bold; }
        .warning { color: #f90; font-weight: bold; }
        pre { background: #f9f9f9; padding: 10px; border-left: 3px solid #0073aa; overflow-x: auto; }
        h2 { color: #0073aa; border-bottom: 2px solid #0073aa; padding-bottom: 10px; }
        .stat { display: inline-block; margin-right: 20px; }
    </style>
</head>
<body>
    <h1>🔍 Revix Reviews Debug Information</h1>
    
    <!-- Registered Shortcodes -->
    <div class="section">
        <h2>Registered Shortcodes</h2>
        <?php
        global $shortcode_tags;
        $revix_shortcodes = array_filter(array_keys($shortcode_tags), function($tag) {
            return strpos($tag, 'revix') !== false;
        });
        
        if (empty($revix_shortcodes)) {
            echo '<p class="error">❌ No Revix shortcodes found! The plugin might not be loading correctly.</p>';
        } else {
            echo '<p class="success">✓ Found ' . count($revix_shortcodes) . ' Revix shortcodes:</p>';
            echo '<ul>';
            foreach ($revix_shortcodes as $tag) {
                echo '<li><code>[' . esc_html($tag) . ']</code></li>';
            }
            echo '</ul>';
        }
        ?>
    </div>

    <!-- Trustpilot Section -->
    <div class="section">
        <h2>Trustpilot Configuration</h2>
        <?php
        $trustpilot_url = get_option('revix_trustpilot_url');
        
        if (empty($trustpilot_url)) {
            echo '<p class="error">❌ Trustpilot URL is NOT configured</p>';
            echo '<p>Please set it in: WordPress Admin → Revix Reviews → Trustpilot tab</p>';
        } else {
            echo '<p class="success">✓ Trustpilot URL is configured</p>';
            echo '<p><strong>URL:</strong> ' . esc_html($trustpilot_url) . '</p>';
            
            // Test fetching reviews
            echo '<h3>Testing Trustpilot Reviews Fetch...</h3>';
            
            use RevixReviews\Public\Inc\Integrations\Trustpilot\TrustpilotFetcher;
            $fetcher = new TrustpilotFetcher();
            $reviews = $fetcher->get_reviews(5, 0);
            
            if (empty($reviews)) {
                echo '<p class="error">❌ No reviews fetched</p>';
                echo '<p>Possible issues:</p>';
                echo '<ul>';
                echo '<li>The URL might be incorrect</li>';
                echo '<li>Trustpilot might have changed their HTML structure</li>';
                echo '<li>Your server might be blocked by Trustpilot</li>';
                echo '<li>Check your error log for details</li>';
                echo '</ul>';
            } else {
                echo '<p class="success">✓ Successfully fetched ' . count($reviews) . ' reviews</p>';
                echo '<h4>Sample Review Data:</h4>';
                echo '<pre>' . print_r($reviews[0], true) . '</pre>';
            }
        }
        ?>
        
        <h3>Test Shortcode Output:</h3>
        <div style="border: 1px solid #ddd; padding: 10px; background: #fafafa;">
            <?php 
            if (!empty($trustpilot_url)) {
                echo do_shortcode('[revix_trustpilot_reviews count="3" debug="true"]');
            } else {
                echo '<p class="error">Configure Trustpilot URL first</p>';
            }
            ?>
        </div>
    </div>

    <!-- Google Reviews Section -->
    <div class="section">
        <h2>Google Reviews Configuration</h2>
        <?php
        $google_api_key = get_option('revix_google_api_key');
        $google_place_id = get_option('revix_google_place_id');
        
        echo '<div class="stat">';
        if (empty($google_api_key)) {
            echo '<p class="error">❌ API Key is NOT configured</p>';
        } else {
            echo '<p class="success">✓ API Key is configured</p>';
            echo '<p><small>Key: ' . substr($google_api_key, 0, 10) . '...</small></p>';
        }
        echo '</div>';
        
        echo '<div class="stat">';
        if (empty($google_place_id)) {
            echo '<p class="error">❌ Place ID is NOT configured</p>';
        } else {
            echo '<p class="success">✓ Place ID is configured</p>';
            echo '<p><strong>Place ID:</strong> ' . esc_html($google_place_id) . '</p>';
        }
        echo '</div>';
        
        if (!empty($google_api_key) && !empty($google_place_id)) {
            echo '<h3>Testing Google API Connection...</h3>';
            
            use RevixReviews\Public\Inc\Integrations\Google\GoogleReviewFetcher;
            $reviews = GoogleReviewFetcher::get_reviews();
            $summary = GoogleReviewFetcher::get_summary();
            
            if (empty($reviews)) {
                echo '<p class="error">❌ No reviews fetched from Google API</p>';
                echo '<p>Possible issues:</p>';
                echo '<ul>';
                echo '<li>API Key might be invalid or expired</li>';
                echo '<li>Places API might not be enabled in Google Cloud Console</li>';
                echo '<li>Place ID might be incorrect</li>';
                echo '<li>API Key restrictions might be blocking requests</li>';
                echo '</ul>';
                
                // Try to get more info
                $url = 'https://maps.googleapis.com/maps/api/place/details/json?' . http_build_query([
                    'place_id' => $google_place_id,
                    'fields' => 'name,rating,user_ratings_total,reviews',
                    'key' => $google_api_key,
                ]);
                
                $response = wp_remote_get($url);
                if (!is_wp_error($response)) {
                    $body = wp_remote_retrieve_body($response);
                    $data = json_decode($body, true);
                    echo '<h4>API Response:</h4>';
                    echo '<pre>' . print_r($data, true) . '</pre>';
                }
            } else {
                echo '<p class="success">✓ Successfully fetched ' . count($reviews) . ' reviews from Google</p>';
                
                if (!empty($summary['name'])) {
                    echo '<p><strong>Business:</strong> ' . esc_html($summary['name']) . '</p>';
                    echo '<p><strong>Average Rating:</strong> ' . esc_html($summary['rating']) . ' / 5</p>';
                    echo '<p><strong>Total Reviews:</strong> ' . esc_html($summary['total_count']) . '</p>';
                }
                
                echo '<h4>Sample Review Data:</h4>';
                echo '<pre>' . print_r($reviews[0], true) . '</pre>';
            }
        } else {
            echo '<p class="warning">⚠️ Please configure both API Key and Place ID in WordPress Admin → Revix Reviews → Google tab</p>';
        }
        ?>
        
        <h3>Test Shortcode Output:</h3>
        <div style="border: 1px solid #ddd; padding: 10px; background: #fafafa;">
            <?php 
            if (!empty($google_api_key) && !empty($google_place_id)) {
                echo do_shortcode('[revix_google_reviews debug="true"]');
            } else {
                echo '<p class="error">Configure Google API settings first</p>';
            }
            ?>
        </div>
    </div>

    <!-- Server Info -->
    <div class="section">
        <h2>Server Information</h2>
        <p><strong>PHP Version:</strong> <?php echo PHP_VERSION; ?></p>
        <p><strong>WordPress Version:</strong> <?php echo get_bloginfo('version'); ?></p>
        <p><strong>Plugin Version:</strong> <?php echo defined('REVIXREVIEWS_VERSION') ? REVIXREVIEWS_VERSION : 'Unknown'; ?></p>
        <p><strong>allow_url_fopen:</strong> <?php echo ini_get('allow_url_fopen') ? 'Enabled' : 'Disabled'; ?></p>
        <p><strong>cURL:</strong> <?php echo function_exists('curl_version') ? 'Enabled' : 'Disabled'; ?></p>
    </div>

    <div class="section">
        <h2>Quick Fix Suggestions</h2>
        <ol>
            <li><strong>For Trustpilot:</strong> Make sure the URL is in the format: <code>https://www.trustpilot.com/review/yourdomain.com</code></li>
            <li><strong>For Google:</strong> Get your API key from <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a> and enable the Places API</li>
            <li><strong>Clear cache:</strong> If using a caching plugin, clear your cache after making changes</li>
            <li><strong>Check error logs:</strong> Look in <code>wp-content/debug.log</code> if WP_DEBUG is enabled</li>
            <li><strong>Test with debug mode:</strong> Use <code>[revix_trustpilot_reviews debug="true"]</code> or <code>[revix_google_reviews debug="true"]</code></li>
        </ol>
    </div>

    <p style="text-align: center; color: #999; margin-top: 40px;">
        <small>Delete this file after debugging for security reasons.</small>
    </p>
</body>
</html>
