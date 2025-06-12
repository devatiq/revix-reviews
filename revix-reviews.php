<?php
/**
 * Plugin Name: Revix Reviews – All-in-One Business Review Manager
 * Version: 1.2.4
 * Description: A WordPress plugin for managing reviews.
 * Author: SupreoX Limited
 * Author URI: https://supreox.com/
 * Plugin URI: https://revixreviews.com/
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: revix-reviews
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 8.0
 *
 * @package RevixReviews
 */

namespace RevixReviews;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

final class RevixReviews
{
    /**
     * Singleton instance.
     *
     * @var RevixReviews|null
     */
    private static $instance = null;

    /**
     * Get singleton instance.
     *
     * @return RevixReviews
     */
    public static function get_instance(): RevixReviews
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Constructor is private to enforce singleton.
     */
    private function __construct()
    {
        $this->define_constants();
        $this->include_files();
        $this->init_hooks();
    }

    /**
     * Define plugin constants.
     */
    private function define_constants(): void
    {
        define('REVIXREVIEWS_VERSION', '1.2.4');
        define('REVIXREVIEWS_PATH', plugin_dir_path(__FILE__));
        define('REVIXREVIEWS_URL', plugin_dir_url(__FILE__));
        define('REVIXREVIEWS_FILE', __FILE__);
        define('REVIXREVIEWS_BASENAME', plugin_basename(__FILE__));
        define('REVIXREVIEWS_NAME', 'Revix Reviews');
    }

    /**
     * Include necessary files.
     */
    private function include_files(): void
    {
        if (file_exists(REVIXREVIEWS_PATH . 'vendor/autoload.php')) {
            require_once REVIXREVIEWS_PATH . 'vendor/autoload.php';
        }
    }

    /**
     * Register plugin hooks.
     */
    private function init_hooks(): void
    {
        add_action('plugins_loaded', [$this, 'plugin_loaded']);
        register_activation_hook(REVIXREVIEWS_FILE, [$this, 'activate']);
        register_deactivation_hook(REVIXREVIEWS_FILE, [$this, 'deactivate']);
    }

    /**
     * Actions after plugins_loaded.
     */
    public function plugin_loaded(): void
    {
        if (class_exists('\RevixReviews\Manager')) {
            new \RevixReviews\Manager();
        }
    }

    /**
     * Plugin activation logic.
     */
    public function activate(): void
    {
        if (class_exists('\RevixReviews\Activate')) {
            \RevixReviews\Activate::activate();
        }
    }

    /**
     * Plugin deactivation logic.
     */
    public function deactivate(): void
    {
        if (class_exists('\RevixReviews\Deactivate')) {
            \RevixReviews\Deactivate::deactivate();
        }
    }
}

// Initialize the plugin.
if (!function_exists('revixreviews_initialize')) {
    function revixreviews_initialize()
    {
        return \RevixReviews\RevixReviews::get_instance();
    }

    revixreviews_initialize();
}