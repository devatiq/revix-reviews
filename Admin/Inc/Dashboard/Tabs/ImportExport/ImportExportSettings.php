<?php
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
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

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
            wp_die(esc_html__('Security check failed', 'revix-reviews'));
        }

        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have sufficient permissions', 'revix-reviews'));
        }

        // Check if file was uploaded
        if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
            wp_die(esc_html__('No file uploaded or upload error occurred', 'revix-reviews'));
        }

        // Validate file type
        $file_type = wp_check_filetype($_FILES['import_file']['name']);
        if ($file_type['ext'] !== 'json') {
            wp_die(esc_html__('Invalid file type. Please upload a JSON file.', 'revix-reviews'));
        }

        // Read file content
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
        $json_content = file_get_contents($_FILES['import_file']['tmp_name']);
        $import_data = json_decode($json_content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_die(esc_html__('Invalid JSON file format', 'revix-reviews'));
        }

        $imported = 0;
        $skipped = 0;

        foreach ($import_data as $review_data) {
            // Prepare post data
            $post_data = [
                'post_type' => 'revixreviews',
                'post_title' => sanitize_text_field($review_data['title']),
                'post_content' => wp_kses_post($review_data['content']),
                'post_status' => sanitize_text_field($review_data['status']),
                'post_date' => sanitize_text_field($review_data['date']),
                'post_author' => isset($review_data['author']) ? absint($review_data['author']) : get_current_user_id()
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
                    update_post_meta($post_id, sanitize_key($meta_key), $meta_value);
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
}
