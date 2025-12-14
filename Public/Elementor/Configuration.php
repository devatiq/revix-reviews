<?php
/**
 * Configuration class for Revix Reviews Elementor Addons.
 *
 * This class handles the initialization and configuration of the Revix Reviews Elementor Addons.
 * It ensures compatibility with the required Elementor version and manages the loading of 
 * required assets and functionalities.
 *
 * @package RevixReviews\Elementor
 * @since 1.3.0
 */

namespace RevixReviews\Public\Elementor;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use RevixReviews\Public\Elementor\Assets\Assets;

/**
 * Class Configuration
 *
 * Handles Elementor Addons initialization for Revix Reviews.
 *
 * @package RevixReviews\Elementor
 * @since 1.3.0
 */
class Configuration
{
    protected $assets;

    /**
     * Plugin version.
     *
     * @var string
     */
    public $version = REVIXREVIEWS_VERSION;

    /**
     * Minimum Elementor Version.
     */
    const MINIMUM_ELEMENTOR_VERSION = '3.19.0';

    /**
     * Minimum PHP Version.
     */
    const MINIMUM_PHP_VERSION = '8.0';

    /**
     * Singleton instance.
     *
     * @var Configuration|null
     */
    private static $_instance = null;

    /**
     * Get singleton instance.
     *
     * @return Configuration
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Set constants.
        $this->set_constants();

        // Run compatibility checks.
        if ($this->is_compatible()) {
            add_action('elementor/init', [$this, 'init']);
        }

        // Initialize classes.
        $this->classes_init();
    }

    /**
     * Define constants.
     */
    public function set_constants()
    {
        define('REVIXREVIEWS_ELEMENTOR_ASSETS', plugin_dir_url(__FILE__) . 'Assets');
        define('REVIXREVIEWS_ELEMENTOR_PATH', plugin_dir_path(__FILE__));
    }

    /**
     * Compatibility Checks.
     */
    public function is_compatible()
    {
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', [$this, 'admin_notice_missing_elementor']);
            return false;
        }

        if (!version_compare(ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=')) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_elementor_version']);
            return false;
        }

        if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_php_version']);
            return false;
        }

        return true;
    }

    /**
     * Admin notice: Elementor not installed.
     */
    public function admin_notice_missing_elementor()
    {
        $message = sprintf(
            // Translators: 1. Plugin name, 2. Elementor plugin name, 3. Required Elementor version.
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'revix-reviews'),
            esc_html__('Revix Reviews', 'revix-reviews'),
            esc_html__('Elementor', 'revix-reviews'),
            esc_html(self::MINIMUM_ELEMENTOR_VERSION)
        );
        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', wp_kses_post($message));
    }

    /**
     * Admin notice: Elementor version too low.
     */
    public function admin_notice_minimum_elementor_version()
    {
        $message = sprintf(
            // Translators: 1. Plugin name, 2. Elementor plugin name, 3. Required Elementor version.
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'revix-reviews'),
            esc_html__('Revix Reviews', 'revix-reviews'),
            esc_html__('Elementor', 'revix-reviews'),
            esc_html(self::MINIMUM_ELEMENTOR_VERSION)
        );
        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', wp_kses_post($message));
    }

    /**
     * Admin notice: PHP version too low.
     */
    public function admin_notice_minimum_php_version()
    {
        $message = sprintf(
            // Translators: 1. Plugin name, 2. PHP, 3. Required PHP version.
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'revix-reviews'),
            '<strong>' . esc_html__('Revix Reviews', 'revix-reviews') . '</strong>',
            '<strong>' . esc_html__('PHP', 'revix-reviews') . '</strong>',
            esc_html(self::MINIMUM_PHP_VERSION)
        );
        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', wp_kses_post($message));
    }

    /**
     * Initialize classes.
     */
    public function classes_init()
    {
        $this->assets = new Assets();
    }

    /**
     * Initialize Elementor functionality.
     */
    public function init()
    {
        add_action('elementor/widgets/register', [$this, 'register_widgets']);
        add_action('elementor/elements/categories_registered', [$this, 'register_widget_categories']);
    }

    /**
     * Register custom widget categories.
     *
     * @param \Elementor\Elements_Manager $elements_manager Elementor elements manager.
     */
    public function register_widget_categories($elements_manager)
    {
        $elements_manager->add_category(
            'revix-reviews',
            [
                'title' => esc_html__('Revix Reviews', 'revix-reviews'),
                'icon' => 'fa fa-star',
            ]
        );
    }

    /**
     * Register Elementor widgets.
     *
     * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
     */
    public function register_widgets($widgets_manager)
    {
        $namespace_base = '\RevixReviews\Public\Elementor\Widgets\\';

        $widgets = [
            'revixreviews_trustpilot_reviews' => 'TrustpilotReviews\Main',
            'revixreviews_trustpilot_summary' => 'TrustpilotSummary\Main',
            'revixreviews_google_summary' => 'GoogleSummary\Main',
            'revixreviews_google_reviews' => 'GoogleReviews\Main',
            'revixreviews_testimonial_reviews' => 'TestimonialReviews\Main',
            'revixreviews_submit_form' => 'ReviewSubmitForm\Main',
        ];

        foreach ($widgets as $option_name => $widget_class) {
            $is_enabled = get_option($option_name, 1); // Enabled by default.

            if ($is_enabled) {
                $full_class = $namespace_base . $widget_class;
                if (class_exists($full_class)) {
                    $widgets_manager->register(new $full_class());
                }
            }
        }
    }
}
