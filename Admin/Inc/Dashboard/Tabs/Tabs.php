<?php 
namespace RevixReviews\Admin\Inc\Dashboard\Tabs;


use RevixReviews\Admin\Inc\Dashboard\Tabs\Trustpilot\TrustpilotSettings;
use RevixReviews\Admin\Inc\Dashboard\Tabs\Google\GoogleSettings;
use RevixReviews\Admin\Inc\Dashboard\Tabs\ImportExport\ImportExportSettings;
class Tabs {

    protected $trustpilot; // Trustpilot object
    protected $google; // Google object
    protected $importexport; // Import/Export object

    public function __construct() {
        $this->class_initialize();
    }

    public static function render_tabs($active_tab = 'general') {
        $tabs = [
            'general'      => ['label' => __('General', 'revix-reviews'), 'icon' => '⚙️'],
            'trustpilot'   => ['label' => __('TrustPilot', 'revix-reviews'), 'icon' => '⭐'],
            'google'       => ['label' => __('Google Reviews', 'revix-reviews'), 'icon' => '🔍'],
            'importexport' => ['label' => __('Import/Export', 'revix-reviews'), 'icon' => '📦']
        ];

        echo '<div class="revixreviews-tabs-wrapper">';
        foreach ($tabs as $tab => $data) {
            $class = ($active_tab === $tab) ? ' revixreviews-tab-active' : '';
            $url = admin_url('edit.php?post_type=revixreviews&page=revixreviews_settings&tab=' . $tab);
            echo '<a href="' . esc_url($url) . '" class="revixreviews-tab' . esc_attr($class) . '">';
            echo '<span class="revixreviews-tab-icon">' . $data['icon'] . '</span>';
            echo '<span class="revixreviews-tab-label">' . esc_html($data['label']) . '</span>';
            echo '</a>';
        }
        echo '</div>';
    }

    private function class_initialize() {
        
        $this->trustpilot = new TrustpilotSettings();
        $this->google     = new GoogleSettings();
        $this->importexport = new ImportExportSettings();

    }

}