<?php
/**
 * Trustpilot HTML Structure Inspector
 * 
 * This script fetches and displays the actual HTML structure from Trustpilot
 * to help identify the correct selectors for extracting review text.
 * 
 * Access: yoursite.com/wp-content/plugins/revix-reviews/inspect-trustpilot.php
 */

// Load WordPress
require_once('../../../wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    wp_die('You must be an administrator to access this page.');
}

$trustpilot_url = get_option('revix_trustpilot_url');

if (empty($trustpilot_url)) {
    wp_die('Please configure your Trustpilot URL in WordPress Admin → Revix Reviews → Trustpilot tab first.');
}

// Clear cache if requested
if (isset($_GET['clear_cache'])) {
    global $wpdb;
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_revix_trustpilot_%'");
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_revix_trustpilot_%'");
    $wpdb->query("DELETE FROM wp_options WHERE option_name LIKE '%revix_trustpilot_reviews_cache%'");
    echo '<div style="background:#0a0;color:white;padding:10px;margin:20px;">Cache cleared! Refresh the page to see fresh data.</div>';
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Trustpilot HTML Inspector</title>
    <style>
        body { font-family: monospace; margin: 20px; background: #f5f5f5; }
        .section { background: white; padding: 20px; margin: 20px 0; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .review-card { border: 2px solid #0073aa; margin: 20px 0; padding: 15px; background: #f9f9f9; }
        pre { background: #282c34; color: #abb2bf; padding: 15px; overflow-x: auto; border-radius: 5px; font-size: 12px; }
        .found { background: #0a0; color: white; padding: 2px 5px; }
        .notfound { background: #c00; color: white; padding: 2px 5px; }
        .attr { color: #e06c75; }
        .value { color: #98c379; }
        h2 { color: #0073aa; }
        .button { display: inline-block; background: #0073aa; color: white; padding: 10px 20px; text-decoration: none; border-radius: 3px; margin: 10px 0; }
        .button:hover { background: #005a87; }
    </style>
</head>
<body>
    <h1>🔍 Trustpilot HTML Structure Inspector</h1>
    
    <div class="section">
        <h2>Current Configuration</h2>
        <p><strong>Trustpilot URL:</strong> <?php echo esc_html($trustpilot_url); ?></p>
        <a href="?clear_cache=1" class="button">Clear Cache & Refresh Data</a>
    </div>

    <?php
    // Fetch the page
    echo '<div class="section"><h2>Fetching Page...</h2>';
    
    $response = wp_remote_get($trustpilot_url);
    
    if (is_wp_error($response)) {
        echo '<p style="color:red;">Error: ' . esc_html($response->get_error_message()) . '</p>';
        echo '</div></body></html>';
        exit;
    }
    
    $html = wp_remote_retrieve_body($response);
    echo '<p style="color:green;">✓ Page fetched successfully (' . number_format(strlen($html)) . ' bytes)</p>';
    echo '</div>';
    
    // Parse HTML
    $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
    libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    @$doc->loadHTML($html);
    $xpath = new DOMXPath($doc);
    
    // Find review cards
    $review_elements = $xpath->query("//article[contains(@class,'styles_reviewCard')]");
    
    echo '<div class="section">';
    echo '<h2>Found ' . $review_elements->length . ' Review Cards</h2>';
    
    if ($review_elements->length === 0) {
        echo '<p style="color:red;">No review cards found! Trustpilot might have changed their HTML structure.</p>';
        echo '<h3>Trying Alternative Selectors:</h3>';
        
        $alternatives = [
            "//article",
            "//div[contains(@class,'review')]",
            "//*[contains(@class,'reviewCard')]",
        ];
        
        foreach ($alternatives as $selector) {
            $alt = $xpath->query($selector);
            echo '<p>Selector: <code>' . esc_html($selector) . '</code> → Found: ' . $alt->length . '</p>';
        }
    } else {
        // Analyze first 3 reviews
        $count = 0;
        foreach ($review_elements as $element) {
            if ($count >= 3) break;
            $count++;
            
            echo '<div class="review-card">';
            echo '<h3>Review #' . $count . '</h3>';
            
            // Test all selectors
            $selectors = [
                'Author' => ".//span[@data-consumer-name-typography='true']",
                'Avatar' => ".//img[@data-consumer-avatar-image='true']",
                'Rating' => ".//img[contains(@alt,'Rated')]",
                'Date' => ".//time",
                'Title' => ".//h2[@data-service-review-title-typography='true']",
                'Text (Primary)' => ".//p[@data-service-review-text-typography='true']",
                'Text (Alt 1)' => ".//div[@data-review-content='true']//p",
                'Text (Alt 2)' => ".//div[contains(@class,'styles_reviewContent')]//p",
                'All Paragraphs' => ".//p",
            ];
            
            echo '<table style="width:100%;border-collapse:collapse;">';
            echo '<tr><th style="text-align:left;border-bottom:1px solid #ccc;">Element</th><th style="text-align:left;border-bottom:1px solid #ccc;">Status</th><th style="text-align:left;border-bottom:1px solid #ccc;">Value</th></tr>';
            
            foreach ($selectors as $name => $selector) {
                $result = $xpath->query($selector, $element);
                $found = $result->length > 0;
                $value = '';
                
                if ($found) {
                    if (strpos($name, 'Avatar') !== false || strpos($name, 'Rating') !== false) {
                        $value = $result->item(0)->getAttribute('src') ?: $result->item(0)->getAttribute('alt');
                    } elseif (strpos($name, 'Date') !== false) {
                        $value = $result->item(0)->getAttribute('datetime');
                    } elseif (strpos($name, 'All Paragraphs') !== false) {
                        $value = $result->length . ' paragraphs found';
                    } else {
                        $value = trim($result->item(0)->nodeValue);
                    }
                }
                
                $status = $found ? '<span class="found">FOUND</span>' : '<span class="notfound">NOT FOUND</span>';
                $displayValue = $found ? esc_html(substr($value, 0, 100)) . (strlen($value) > 100 ? '...' : '') : '-';
                
                echo '<tr>';
                echo '<td style="padding:5px;border-bottom:1px solid #eee;"><strong>' . esc_html($name) . '</strong></td>';
                echo '<td style="padding:5px;border-bottom:1px solid #eee;">' . $status . '</td>';
                echo '<td style="padding:5px;border-bottom:1px solid #eee;">' . $displayValue . '</td>';
                echo '</tr>';
            }
            echo '</table>';
            
            // Show all paragraph content
            $allPs = $xpath->query(".//p", $element);
            if ($allPs->length > 0) {
                echo '<h4>All Paragraph Contents in This Review:</h4>';
                echo '<ol>';
                foreach ($allPs as $idx => $p) {
                    $text = trim($p->nodeValue);
                    if (!empty($text)) {
                        echo '<li>' . esc_html($text) . ' <em>(Length: ' . strlen($text) . ')</em></li>';
                    }
                }
                echo '</ol>';
            }
            
            echo '</div>';
        }
    }
    
    echo '</div>';
    
    // Show raw HTML snippet
    echo '<div class="section">';
    echo '<h2>Raw HTML Sample (First Review Card)</h2>';
    if ($review_elements->length > 0) {
        $firstReview = $review_elements->item(0);
        $rawHtml = $doc->saveHTML($firstReview);
        echo '<pre>' . esc_html(substr($rawHtml, 0, 2000)) . '...</pre>';
    }
    echo '</div>';
    ?>
    
    <div class="section">
        <h2>Next Steps</h2>
        <ol>
            <li>Look at the "All Paragraph Contents" section above to see which paragraph contains the actual review text</li>
            <li>Check which "Text" selector successfully found the review content</li>
            <li>If none of the text selectors work, we need to update the XPath queries in TrustpilotFetcher.php</li>
            <li>After clearing cache, the plugin will fetch fresh data with the new selectors</li>
        </ol>
        <p><strong>⚠️ Delete this file after debugging for security!</strong></p>
    </div>
</body>
</html>
