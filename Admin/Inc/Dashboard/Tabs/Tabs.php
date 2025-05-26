<?php 
namespace RevixReviews\Admin\Inc\Dashboard\Tabs;


use RevixReviews\Admin\Inc\Dashboard\Tabs\Trustpilot\TrustpilotSettings;

class Tabs {

    protected $trustpilot; // Trustpilot object

    public function __construct() {
        $this->class_initialize();
    }

    public static function render_tabs($active_tab = 'general') {
        $tabs = [
            'general'    => __('General', 'revix-reviews'),
            'trustpilot' => __('TrustPilot', 'revix-reviews')
        ];

        echo '<h2 class="nav-tab-wrapper">';
        foreach ($tabs as $tab => $label) {
            $class = ($active_tab === $tab) ? ' nav-tab-active' : '';
            $url = admin_url('edit.php?post_type=revixreviews&page=revixreviews_settings&tab=' . $tab);
            echo '<a href="' . esc_url($url) . '" class="nav-tab' . esc_attr($class) . '">' . esc_html($label) . '</a>';
        }
        echo '</h2>';
    }

    private function class_initialize() {
        
        $this->trustpilot = new TrustpilotSettings();


    }

}