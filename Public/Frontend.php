<?php
namespace RevixReviews\Public;


// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}


use RevixReviews\Public\Assets\Assets;
use RevixReviews\Public\Shortcodes\Shortcodes;
use RevixReviews\Public\Elementor\Configuration as ElementorConfiguration;

class Frontend {
    protected $assets;
	protected $Shortcodes;
    protected $elementorConfiguration;

    /**
     * Initialize the class and set its properties.
     */
    public function __construct() {
        $this->setConstants();
        $this->init_initialize();
    }

    public function setConstants()
    {
        define('REVIXREVIEWS_FRONTEND_ASSETS', plugin_dir_url(__FILE__) . 'Assets');
        define('REVIXREVIEWS_FRONTEND_PATH', plugin_dir_path(__FILE__));

    }

    /**
     * Initialize all shortcode classes
     *
     * @return void
     */
    private function init_initialize() {
        $this->assets = new Assets();
        $this->Shortcodes = new Shortcodes();

        // Initialize Elementor widgets if enabled in settings
        $elementor_active = get_option('revixreviews_elementor_active', 0);
        if ($elementor_active) {
            // Check if Elementor is actually installed and active
            if (did_action('elementor/loaded')) {
                $this->elementorConfiguration = ElementorConfiguration::instance();
            } else {
                // Show admin notice if Elementor is not installed
                add_action('admin_notices', [$this, 'elementor_missing_notice']);
            }
        }
    }

    /**
     * Display admin notice when Elementor widgets are enabled but Elementor is not installed
     *
     * @return void
     */
    public function elementor_missing_notice() {
        ?>
        <div class="notice notice-error is-dismissible">
            <p>
                <strong><?php echo esc_html__('Revix Reviews:', 'revix-reviews'); ?></strong>
                <?php echo esc_html__('Elementor widgets are enabled but Elementor plugin is not installed or activated. Please install and activate Elementor to use the widgets.', 'revix-reviews'); ?>
                <a href="<?php echo esc_url(admin_url('plugin-install.php?s=elementor&tab=search&type=term')); ?>" target="_blank">
                    <?php echo esc_html__('Install Elementor', 'revix-reviews'); ?>
                </a>
            </p>
        </div>
        <?php
    }

}