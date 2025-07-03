<?php
/**
 * Core class for the plugin.
 *
 * This class handles the core functionalities of the plugin in the admin area.
 *
 * @package RevixReviews
 * @subpackage Admin
 * @since 1.0.0
 */

namespace RevixReviews\Admin\Core;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Class Core
 *
 * @package RevixReviews\Admin\Core
 * @since 1.0.0
 */
class Core
{
	/**
	 * Core constructor.
	 *
	 * Initializes the core functionalities.
	 *
	 * @since 1.0.0
	 */
	public function __construct()
	{
		add_filter('admin_footer_text', [$this, 'change_admin_footer_text']);
	}

	/**
	 * Adds a custom footer text to the admin pages of the plugin.
	 *
	 * This function checks if the current screen is a Revix Reviews page and, if so,
	 * replaces the default footer text with a custom one that encourages users to rate the plugin.
	 *
	 * @param string $text The default footer text.
	 * @return string The modified footer text.
	 * @since 1.0.0
	 */
	public function change_admin_footer_text($text)
	{
		// Only show on Revix Reviews pages
		$screen = get_current_screen();
		if (
			$screen && (
				$screen->id === 'revixreviews_page_revixreviews_settings' ||
				$screen->post_type === 'revixreviews'
			)
		) {
			$text = sprintf(
				wp_kses_post('Please rate <strong>%1$s</strong> %2$s on <a href="%3$s" target="_blank">WordPress.org</a> to help us spread the word.', 'revix-reviews'),
				'Revix Reviews',
				str_repeat('<span style="color: #ffb900;">&#9733;</span>', 5),
				'https://wordpress.org/support/plugin/revix-reviews/reviews/#new-post'
			);
		}

		return $text;
	}
}
