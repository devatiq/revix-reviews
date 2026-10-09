<?php
namespace RevixReviews\Public\Shortcodes\Trustpilot;

use RevixReviews\Public\Inc\Integrations\Trustpilot\TrustpilotFetcher;

class TrustpilotSummaryShortcode
{
	public function __construct()
	{
		add_shortcode('revix_trustpilot_summary', [$this, 'render']);
	}

	public function render()
	{
		if (!TrustpilotFetcher::is_configured()) {
			return '<!-- Trustpilot API key and URL/Business Unit ID not configured -->';
		}

		$fetcher = new TrustpilotFetcher();
		$summary = $fetcher->get_summary();
		$average = isset($summary['average']) ? $summary['average'] : '';
		$count   = isset($summary['count']) ? $summary['count'] : '';

		ob_start();
		echo '<div class="revix-summary-wrapper">';
		if ($average !== '' && $average !== null) {
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
			$svg = REVIXREVIEWS_FRONTEND_ASSETS . '/img/' . $svgFile;

			echo '<div class="revix-summary-average">';
			echo '<strong>' . esc_html($average) . '</strong>';
			// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NonEnqueuedImage -- Plugin star asset.
			echo '<img src="' . esc_url($svg) . '" alt="Rated ' . esc_attr($average) . ' out of 5 stars" />';
			echo '</div>';
		}
		if ($count !== '' && $count !== null) {
			echo '<div class="revix-summary-count">';
			echo '<strong>' . esc_html(number_format((int) $count)) . '</strong> ' . esc_html__('Reviews', 'revix-reviews');
			echo '</div>';
		}
		echo '</div>';

		return ob_get_clean();
	}
}
