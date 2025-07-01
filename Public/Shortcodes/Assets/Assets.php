<?php
namespace RevixReviews\Public\Shortcodes\Assets;

/**
 * disable direct access
 */
if (!defined('ABSPATH')) {
    die;
}

class Assets
{

    /**
     * Construct method.
     * 
     * Registers the `enqueue_styles` method to run when the `wp_enqueue_scripts` action is fired.
     * 
     * @since 1.0.0
     */
    public function __construct()
    {

        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles_conditionally'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts_conditionally'));
    }


    /**
     * Enqueue shortcode styles.
     * 
     * Enqueues the styles specific to shortcodes for the plugin.
     * 
     * @since 1.0.0
     * 
     * @param string $hook The current WordPress admin page.
     */
    public function enqueue_styles_conditionally($hook)
    {
        if (!is_singular() && !is_front_page()) { // this all is to avoid enqueueing styles on pages that don't need them
            return;
        }
    
        global $post;
    
        if (has_shortcode($post->post_content, 'revix_trustpilot_reviews') || has_shortcode($post->post_content, 'revix_trustpilot_summary')) {
            wp_enqueue_style('revix-trustpilot', REVIXREVIEWS_SHORTCODE_ASSETS . '/css/trustpilot.css', [], REVIXREVIEWS_VERSION);
        }
    
        if (has_shortcode($post->post_content, 'revix_google_reviews') || has_shortcode($post->post_content, 'revix_google_summary')) {
            wp_enqueue_style('revix-google-review', REVIXREVIEWS_SHORTCODE_ASSETS . '/css/google-review.css', [], REVIXREVIEWS_VERSION);
        }

        
    }
    

    /**
     * Enqueue shortcode scripts.
     * 
     * Enqueues the scripts specific to shortcodes for the plugin.
     * 
     * @since 1.0.0
     * 
     * @param string $hook The current WordPress admin page.
     */
    public function enqueue_scripts_conditionally($hook)
    {
        if (!is_singular() && !is_front_page()) { // this all is to avoid enqueueing scripts on pages that don't need them
            return;
        }
    
        global $post;
    
        if (has_shortcode($post->post_content, 'revix_trustpilot_reviews')) {
            wp_enqueue_script('revix-trustpilot', REVIXREVIEWS_SHORTCODE_ASSETS . '/js/trustpilot.js', ['jquery'], REVIXREVIEWS_VERSION, true);
        }
    
        if (has_shortcode($post->post_content, 'revix_google_reviews')) {
            wp_enqueue_script('masonry-js', REVIXREVIEWS_SHORTCODE_ASSETS . '/js/masonry.pkgd.min.js', [], null, true);
            wp_add_inline_script('masonry-js', "
                document.addEventListener('DOMContentLoaded', function () {
                    var container = document.querySelector('.revix-google-masonry');
                    if(container){
                        new Masonry(container, {
                            itemSelector: '.revix-google-review-item',
                            columnWidth: '.revix-google-review-item',
                            percentPosition: true,
                            gutter: 25
                        });
                    }
                });
            ");
        }

        wp_enqueue_script(
            'revixreviews-form-ajax', REVIXREVIEWS_SHORTCODE_ASSETS . '/js/revixreviews-ajax.js', ['jquery'], REVIXREVIEWS_VERSION, true
        );
        
        wp_localize_script('revixreviews-form-ajax', 'revixreviews_ajax_obj', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('revixreviews_feedback_nonce_action'),
            'redirect_url'  => get_option('revixreviews_redirect_url') ?: home_url('/'),
        ]);
    }
    
}