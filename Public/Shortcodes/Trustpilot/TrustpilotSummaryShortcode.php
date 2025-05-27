<?php
namespace RevixReviews\Public\Shortcodes\Trustpilot;

class TrustpilotSummaryShortcode
{
    public function __construct()
    {
        add_shortcode('revix_trustpilot_summary', [$this, 'render']);
    }

    public function render()
    {
        $url = get_option('revix_trustpilot_url');
        if (!$url)
            return '<!-- Trustpilot URL not set -->';

        $response = wp_remote_get($url);
        if (is_wp_error($response))
            return '<!-- Error fetching Trustpilot page -->';

        $html = wp_remote_retrieve_body($response);
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        @$doc->loadHTML($html);
        $xpath = new \DOMXPath($doc);

        $scripts = $xpath->query('//script[@type="application/ld+json"]');

        $average = '';
        $count = '';

        foreach ($scripts as $script) {
            $json = json_decode($script->nodeValue, true);

            if (isset($json['aggregateRating']['ratingValue'])) {
                $average = $json['aggregateRating']['ratingValue'];
                $count = $json['aggregateRating']['reviewCount'];
                break;
            }

            if (isset($json['@graph']) && is_array($json['@graph'])) {
                foreach ($json['@graph'] as $node) {
                    if (isset($node['aggregateRating']['ratingValue'])) {
                        $average = $node['aggregateRating']['ratingValue'];
                        $count = $node['aggregateRating']['reviewCount'];
                        break 2;
                    }
                }
            }
        }

        ob_start();
        echo '<div class="revix-summary-wrapper">';
        if ($average) {
            $rating = floatval($average);
            if ($rating >= 4.75) {
                $svgFile = 'stars-5.svg';
            } elseif ($rating >= 4.25) {
                $svgFile = 'stars-4.5.svg';
            } elseif ($rating >= 3.75) {
                $svgFile = 'stars-4.svg';
            } elseif ($rating >= 3.25) {
                $svgFile = 'stars-3.5.svg';
            } elseif ($rating >= 2.75) {
                $svgFile = 'stars-3.svg';
            } elseif ($rating >= 2.25) {
                $svgFile = 'stars-2.5.svg';
            } elseif ($rating >= 1.75) {
                $svgFile = 'stars-2.svg';
            } elseif ($rating >= 1.25) {
                $svgFile = 'stars-1.5.svg';
            } else {
                $svgFile = 'stars-1.svg';
            }
            $svg = REVIXREVIEWS_URL . 'public/assets/img/' . $svgFile;

            echo '<div class="revix-summary-average">';
            echo '<img src="' . esc_url($svg) . '" alt="Rated ' . esc_attr($average) . ' out of 5 stars" style="height:18px;vertical-align:middle;margin-right:6px;" />';
            echo '<strong>' . esc_html($average) . '</strong> out of 5';
            echo '</div>';
        }
        if ($count) {
            echo '<div class="revix-summary-count">';
            echo '<strong>' . number_format((int) $count) . '</strong> Reviews';
            echo '</div>';
        }
        echo '</div>';

        return ob_get_clean();
    }
}
