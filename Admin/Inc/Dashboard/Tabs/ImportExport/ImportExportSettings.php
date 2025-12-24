<?php
/**
 * Import/Export Settings Class
 * 
 * Handles secure import and export of review data with comprehensive validation.
 * 
 * Security Measures Implemented:
 * - Nonce verification for all form submissions
 * - Capability checks (manage_options required)
 * - File type validation (extension and MIME type)
 * - File size limit (10MB maximum)
 * - JSON validation and structure verification
 * - Sanitization of all input data (titles, content, meta)
 * - Validation of post status against allowed values
 * - Author ID validation and existence check
 * - Meta key sanitization and filtering
 * - Secure file download headers (X-Content-Type-Options, X-Robots-Tag)
 * - Error handling with user-friendly messages
 * - Safe redirects using wp_safe_redirect()
 * 
 * @package RevixReviews
 * @since 1.2.7
 */
namespace RevixReviews\Admin\Inc\Dashboard\Tabs\ImportExport;

class ImportExportSettings {
    
    public function __construct() {
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_post_revixreviews_export', [$this, 'handle_export']);
        add_action('admin_post_revixreviews_import', [$this, 'handle_import']);
    }

    public function register_settings() {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $current_tab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : 'general';
        
        if ($current_tab !== 'importexport') {
            return;
        }

        add_settings_section(
            'revix_importexport_section',
            __('Import / Export Reviews', 'revix-reviews'),
            [$this, 'section_callback'],
            'revixreviews_importexport'
        );
    }

    public function section_callback() {
        echo '<p>' . esc_html__('Export your reviews to a JSON file or import reviews from a previously exported file.', 'revix-reviews') . '</p>';
    }

    /**
     * Handle export of reviews
     */
    public function handle_export() {
        // Security check
        if (!isset($_POST['revixreviews_export_nonce']) || 
            !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['revixreviews_export_nonce'])), 'revixreviews_export_action')) {
            wp_die(esc_html__('Security check failed', 'revix-reviews'));
        }

        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have sufficient permissions', 'revix-reviews'));
        }

        // Get all reviews
        $args = [
            'post_type' => 'revixreviews',
            'posts_per_page' => -1,
            'post_status' => ['publish', 'pending', 'draft', 'private']
        ];

        $reviews = get_posts($args);
        $export_data = [];

        foreach ($reviews as $review) {
            $review_data = [
                'title' => $review->post_title,
                'content' => $review->post_content,
                'status' => $review->post_status,
                'date' => $review->post_date,
                'author' => $review->post_author,
                'meta' => []
            ];

            // Get all custom fields
            $meta = get_post_meta($review->ID);
            foreach ($meta as $key => $value) {
                // Skip WordPress internal meta fields
                if (strpos($key, '_') !== 0 || strpos($key, '_revixreviews') === 0) {
                    $review_data['meta'][$key] = maybe_unserialize($value[0]);
                }
            }

            $export_data[] = $review_data;
        }

        // Create JSON file
        $json = wp_json_encode($export_data, JSON_PRETTY_PRINT);
        $filename = 'revixreviews-export-' . gmdate('Y-m-d-H-i-s') . '.json';

        // Send headers for download
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($json));
        header('Pragma: no-cache');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('X-Content-Type-Options: nosniff');
        header('X-Robots-Tag: noindex, nofollow');

        // Output JSON
        echo $json; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        exit;
    }

    /**
     * Handle import of reviews
     */
    public function handle_import() {
        // Security check
        if (!isset($_POST['revixreviews_import_nonce']) || 
            !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['revixreviews_import_nonce'])), 'revixreviews_import_action')) {
            $this->redirect_with_error('Security check failed');
            return;
        }

        if (!current_user_can('manage_options')) {
            $this->redirect_with_error('You do not have sufficient permissions');
            return;
        }

        // Check if file was uploaded
        if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
            $this->redirect_with_error('No file uploaded or upload error occurred');
            return;
        }

        // Validate file size (max 10MB)
        $max_file_size = 10 * 1024 * 1024; // 10MB in bytes
        if ($_FILES['import_file']['size'] > $max_file_size) {
            $this->redirect_with_error('File is too large. Maximum size is 10MB');
            return;
        }

        // Validate file type - check extension directly from the filename
        $filename = isset($_FILES['import_file']['name']) ? sanitize_file_name($_FILES['import_file']['name']) : '';
        $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if ($file_extension !== 'json') {
            $this->redirect_with_error('Invalid file type. Please upload a JSON file.');
            return;
        }

        // Validate MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $_FILES['import_file']['tmp_name']);
        finfo_close($finfo);
        
        $allowed_mime_types = ['application/json', 'text/plain'];
        if (!in_array($mime_type, $allowed_mime_types, true)) {
            $this->redirect_with_error('Invalid file format. Only JSON files are allowed.');
            return;
        }

        // Read file content
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
        $json_content = file_get_contents($_FILES['import_file']['tmp_name']);
        
        if ($json_content === false) {
            $this->redirect_with_error('Failed to read the uploaded file');
            return;
        }

        $import_data = json_decode($json_content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->redirect_with_error('Invalid JSON file format');
            return;
        }

        if (!is_array($import_data) || empty($import_data)) {
            $this->redirect_with_error('The JSON file contains no valid review data');
            return;
        }

        // Validate JSON structure - ensure it's an array of review objects
        foreach ($import_data as $index => $item) {
            if (!is_array($item)) {
                $this->redirect_with_error('Invalid JSON structure. Expected array of review objects.');
                return;
            }
        }

        // Limit number of reviews to prevent memory issues
        $max_reviews = 1000;
        if (count($import_data) > $max_reviews) {
            $this->redirect_with_error(sprintf('Too many reviews. Maximum allowed is %d reviews per import.', $max_reviews));
            return;
        }

        $imported = 0;
        $skipped = 0;

        foreach ($import_data as $review_data) {
            // Validate required fields
            if (!isset($review_data['title']) || !isset($review_data['content'])) {
                $skipped++;
                continue;
            }

            // Validate post status
            $allowed_statuses = ['publish', 'pending', 'draft', 'private'];
            $post_status = isset($review_data['status']) ? sanitize_text_field($review_data['status']) : 'pending';
            if (!in_array($post_status, $allowed_statuses, true)) {
                $post_status = 'pending';
            }

            // Validate author exists
            $author_id = isset($review_data['author']) ? absint($review_data['author']) : get_current_user_id();
            if ($author_id > 0 && !get_user_by('id', $author_id)) {
                $author_id = get_current_user_id();
            }

            // Prepare post data
            $post_data = [
                'post_type' => 'revixreviews',
                'post_title' => sanitize_text_field($review_data['title']),
                'post_content' => wp_kses_post($review_data['content']),
                'post_status' => $post_status,
                'post_date' => isset($review_data['date']) ? sanitize_text_field($review_data['date']) : current_time('mysql'),
                'post_author' => $author_id
            ];

            // Insert post
            $post_id = wp_insert_post($post_data);

            if (is_wp_error($post_id)) {
                $skipped++;
                continue;
            }

            // Import meta fields
            if (isset($review_data['meta']) && is_array($review_data['meta'])) {
                foreach ($review_data['meta'] as $meta_key => $meta_value) {
                    // Sanitize meta key
                    $sanitized_key = sanitize_key($meta_key);
                    
                    // Skip empty keys or protected WordPress meta
                    if (empty($sanitized_key) || (strpos($sanitized_key, '_') === 0 && strpos($sanitized_key, '_revixreviews') !== 0)) {
                        continue;
                    }
                    
                    // Sanitize meta value based on type
                    if (is_string($meta_value)) {
                        $meta_value = sanitize_text_field($meta_value);
                    } elseif (is_array($meta_value)) {
                        $meta_value = array_map('sanitize_text_field', $meta_value);
                    }
                    
                    update_post_meta($post_id, $sanitized_key, $meta_value);
                }
            }

            $imported++;
        }

        // Redirect back with success message
        $redirect_url = add_query_arg([
            'page' => 'revixreviews_settings',
            'tab' => 'importexport',
            'imported' => $imported,
            'skipped' => $skipped
        ], admin_url('edit.php?post_type=revixreviews'));

        wp_safe_redirect($redirect_url);
        exit;
    }

    /**
     * Redirect back to settings page with error message
     *
     * @param string $error_message The error message to display
     */
    private function redirect_with_error($error_message) {
        $redirect_url = add_query_arg([
            'page' => 'revixreviews_settings',
            'tab' => 'importexport',
            'import_error' => urlencode($error_message)
        ], admin_url('edit.php?post_type=revixreviews'));

        wp_safe_redirect($redirect_url);
        exit;
    }
}
