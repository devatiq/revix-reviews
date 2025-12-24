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
        add_action('admin_post_revixreviews_export_csv', [$this, 'handle_export_csv']);
        add_action('wp_ajax_revixreviews_import', [$this, 'handle_import_ajax']);
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
     * Handle CSV export of reviews
     */
    public function handle_export_csv() {
        // Security check
        if (!isset($_POST['revixreviews_export_csv_nonce']) || 
            !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['revixreviews_export_csv_nonce'])), 'revixreviews_export_csv_action')) {
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
        
        if (empty($reviews)) {
            wp_die(esc_html__('No reviews found to export', 'revix-reviews'));
        }

        // Collect all unique meta keys
        $all_meta_keys = [];
        foreach ($reviews as $review) {
            $meta = get_post_meta($review->ID);
            foreach ($meta as $key => $value) {
                if (strpos($key, '_') !== 0 || strpos($key, '_revixreviews') === 0) {
                    if (!in_array($key, $all_meta_keys, true)) {
                        $all_meta_keys[] = $key;
                    }
                }
            }
        }
        sort($all_meta_keys);

        // Prepare CSV headers
        $headers = ['title', 'content', 'status', 'date', 'author'];
        foreach ($all_meta_keys as $meta_key) {
            $headers[] = 'meta_' . $meta_key;
        }

        // Create CSV content
        $filename = 'revixreviews-export-' . gmdate('Y-m-d-H-i-s') . '.csv';
        
        // Send headers for download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('X-Content-Type-Options: nosniff');
        header('X-Robots-Tag: noindex, nofollow');

        // Open output stream
        $output = fopen('php://output', 'w');
        
        // Write BOM for UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Write headers
        fputcsv($output, $headers);

        // Write data rows
        foreach ($reviews as $review) {
            $row = [
                $review->post_title,
                $review->post_content,
                $review->post_status,
                $review->post_date,
                $review->post_author
            ];

            // Add meta fields
            $meta = get_post_meta($review->ID);
            foreach ($all_meta_keys as $meta_key) {
                if (isset($meta[$meta_key])) {
                    $value = maybe_unserialize($meta[$meta_key][0]);
                    // Convert arrays to JSON string for CSV
                    if (is_array($value)) {
                        $value = wp_json_encode($value);
                    }
                    $row[] = $value;
                } else {
                    $row[] = '';
                }
            }

            fputcsv($output, $row);
        }

        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
        fclose($output);
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
        
        if (!in_array($file_extension, ['json', 'csv'], true)) {
            wp_send_json_error(['message' => __('Invalid file type. Please upload a JSON or CSV file.', 'revix-reviews')]);
        }

        // Validate MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $_FILES['import_file']['tmp_name']);
        finfo_close($finfo);
        
        $allowed_mime_types = ['application/json', 'text/plain', 'text/csv', 'application/csv', 'application/vnd.ms-excel'];
        if (!in_array($mime_type, $allowed_mime_types, true)) {
            wp_send_json_error(['message' => __('Invalid file format. Only JSON and CSV files are allowed.', 'revix-reviews')]);
        }

        // Read and parse file based on extension
        if ($file_extension === 'csv') {
            $import_data = $this->parse_csv_file($_FILES['import_file']['tmp_name']);
            if (is_wp_error($import_data)) {
                wp_send_json_error(['message' => $import_data->get_error_message()]);
            }
        } else {
            // Read JSON file content
            // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
            $json_content = file_get_contents($_FILES['import_file']['tmp_name']);
            
            if ($json_content === false) {
                wp_send_json_error(['message' => __('Failed to read the uploaded file', 'revix-reviews')]);
            }

            $import_data = json_decode($json_content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                wp_send_json_error(['message' => __('Invalid JSON file format', 'revix-reviews')]);
            }
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
     * Handle AJAX import of reviews
     */
    public function handle_import_ajax() {
        // Security check
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'revixreviews_import_action')) {
            wp_send_json_error(['message' => __('Security check failed', 'revix-reviews')]);
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('You do not have sufficient permissions', 'revix-reviews')]);
        }

        // Check if file was uploaded
        if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
            wp_send_json_error(['message' => __('No file uploaded or upload error occurred', 'revix-reviews')]);
        }

        // Validate file size (max 10MB)
        $max_file_size = 10 * 1024 * 1024; // 10MB in bytes
        if ($_FILES['import_file']['size'] > $max_file_size) {
            wp_send_json_error(['message' => __('File is too large. Maximum size is 10MB', 'revix-reviews')]);
        }

        // Validate file type
        $filename = isset($_FILES['import_file']['name']) ? sanitize_file_name($_FILES['import_file']['name']) : '';
        $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (!in_array($file_extension, ['json', 'csv'], true)) {
            wp_send_json_error(['message' => __('Invalid file type. Please upload a JSON or CSV file.', 'revix-reviews')]);
        }

        // Validate MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $_FILES['import_file']['tmp_name']);
        finfo_close($finfo);
        
        $allowed_mime_types = ['application/json', 'text/plain', 'text/csv', 'application/csv', 'application/vnd.ms-excel'];
        if (!in_array($mime_type, $allowed_mime_types, true)) {
            wp_send_json_error(['message' => __('Invalid file format. Only JSON and CSV files are allowed.', 'revix-reviews')]);
        }

        // Read and parse file content based on file type
        if ($file_extension === 'csv') {
            // Handle CSV file
            $import_data = $this->parse_csv_file($_FILES['import_file']['tmp_name']);
            
            if (is_wp_error($import_data)) {
                wp_send_json_error(['message' => $import_data->get_error_message()]);
            }
            
            if (empty($import_data)) {
                wp_send_json_error(['message' => __('The CSV file contains no valid review data', 'revix-reviews')]);
            }
        } else {
            // Handle JSON file
            // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
            $json_content = file_get_contents($_FILES['import_file']['tmp_name']);
            
            if ($json_content === false) {
                wp_send_json_error(['message' => __('Failed to read the uploaded file', 'revix-reviews')]);
            }

            $import_data = json_decode($json_content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                wp_send_json_error(['message' => __('Invalid JSON file format', 'revix-reviews')]);
            }

            if (!is_array($import_data) || empty($import_data)) {
                wp_send_json_error(['message' => __('The JSON file contains no valid review data', 'revix-reviews')]);
            }

            // Validate JSON structure
            foreach ($import_data as $index => $item) {
                if (!is_array($item)) {
                    wp_send_json_error(['message' => __('Invalid JSON structure. Expected array of review objects.', 'revix-reviews')]);
                }
            }
        }

        // Limit number of reviews
        $max_reviews = 1000;
        if (count($import_data) > $max_reviews) {
            wp_send_json_error(['message' => sprintf(__('Too many reviews. Maximum allowed is %d reviews per import.', 'revix-reviews'), $max_reviews)]);
        }

        $imported = 0;
        $skipped = 0;
        $skipped_items = [];

        foreach ($import_data as $index => $review_data) {
            // Validate required fields
            if (!isset($review_data['title']) || !isset($review_data['content'])) {
                $skipped++;
                $skipped_items[] = sprintf(__('Item #%d: Missing required fields (title or content)', 'revix-reviews'), $index + 1);
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

            // Validate and format date
            $post_date = current_time('mysql');
            if (isset($review_data['date']) && !empty($review_data['date'])) {
                $date_string = sanitize_text_field($review_data['date']);
                
                // Validate date format (YYYY-MM-DD HH:MM:SS)
                if (!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $date_string)) {
                    $skipped++;
                    $title = isset($review_data['title']) ? substr($review_data['title'], 0, 30) : __('Unknown', 'revix-reviews');
                    $skipped_items[] = sprintf(__('Item "%s...": Invalid date format. Expected YYYY-MM-DD HH:MM:SS, got "%s"', 'revix-reviews'), $title, $date_string);
                    continue;
                }
                
                // Validate that it's a real date
                $date_parts = explode(' ', $date_string);
                if (count($date_parts) === 2) {
                    list($date, $time) = $date_parts;
                    $date_components = explode('-', $date);
                    $time_components = explode(':', $time);
                    
                    if (count($date_components) === 3 && count($time_components) === 3) {
                        $year = (int) $date_components[0];
                        $month = (int) $date_components[1];
                        $day = (int) $date_components[2];
                        
                        if (!checkdate($month, $day, $year)) {
                            $skipped++;
                            $title = isset($review_data['title']) ? substr($review_data['title'], 0, 30) : __('Unknown', 'revix-reviews');
                            $skipped_items[] = sprintf(__('Item "%s...": Invalid date value "%s"', 'revix-reviews'), $title, $date_string);
                            continue;
                        }
                    }
                }
                
                $post_date = $date_string;
            }

            // Prepare post data
            $post_data = [
                'post_type' => 'revixreviews',
                'post_title' => sanitize_text_field($review_data['title']),
                'post_content' => wp_kses_post($review_data['content']),
                'post_status' => $post_status,
                'post_date' => $post_date,
                'post_author' => $author_id
            ];

            // Insert post
            $post_id = wp_insert_post($post_data);

            if (is_wp_error($post_id)) {
                $skipped++;
                $title = isset($review_data['title']) ? substr($review_data['title'], 0, 30) : __('Unknown', 'revix-reviews');
                $skipped_items[] = sprintf(__('Item "%s...": %s', 'revix-reviews'), $title, $post_id->get_error_message());
                continue;
            }

            // Import meta fields
            if (isset($review_data['meta']) && is_array($review_data['meta'])) {
                foreach ($review_data['meta'] as $meta_key => $meta_value) {
                    $sanitized_key = sanitize_key($meta_key);
                    
                    if (empty($sanitized_key) || (strpos($sanitized_key, '_') === 0 && strpos($sanitized_key, '_revixreviews') !== 0)) {
                        continue;
                    }
                    
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

        // Send success response
        wp_send_json_success([
            'imported' => $imported,
            'skipped' => $skipped,
            'skipped_items' => $skipped_items
        ]);
    }

    /**
     * Parse CSV file and convert to array format
     *
     * @param string $file_path Path to the CSV file
     * @return array|WP_Error Array of review data or WP_Error on failure
     */
    private function parse_csv_file($file_path) {
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
        $file = fopen($file_path, 'r');
        
        if ($file === false) {
            return new \WP_Error('file_read_error', __('Failed to open CSV file', 'revix-reviews'));
        }

        $headers = [];
        $data = [];
        $row_number = 0;

        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fgetcsv
        while (($row = fgetcsv($file)) !== false) {
            $row_number++;
            
            if ($row_number === 1) {
                // First row is headers - remove BOM if present
                $headers = array_map(function($header) {
                    $header = trim($header);
                    // Remove UTF-8 BOM from first column if present
                    return str_replace("\xEF\xBB\xBF", '', $header);
                }, $row);
                
                // Validate required columns
                if (!in_array('title', $headers, true) || !in_array('content', $headers, true)) {
                    // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
                    fclose($file);
                    return new \WP_Error('invalid_csv', __('CSV must have "title" and "content" columns', 'revix-reviews'));
                }
                continue;
            }

            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            // Map row to headers
            $review = [];
            foreach ($headers as $index => $header) {
                if (isset($row[$index])) {
                    $value = trim($row[$index]);
                    
                    // Handle meta fields (columns starting with meta_)
                    if (strpos($header, 'meta_') === 0) {
                        if (!isset($review['meta'])) {
                            $review['meta'] = [];
                        }
                        $meta_key = substr($header, 5); // Remove 'meta_' prefix
                        $review['meta'][$meta_key] = $value;
                    } else {
                        $review[$header] = $value;
                    }
                }
            }

            $data[] = $review;
        }

        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
        fclose($file);

        if (empty($data)) {
            return new \WP_Error('empty_csv', __('CSV file contains no data rows', 'revix-reviews'));
        }

        return $data;
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
