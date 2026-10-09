<?php
namespace RevixReviews\Public\Inc\Integrations\Trustpilot;

/**
 * Fetches Trustpilot reviews via the official Public API.
 *
 * HTML scraping is no longer viable — Trustpilot serves an AWS WAF
 * challenge page to non-browser requests.
 */
class TrustpilotFetcher
{
	const API_BASE = 'https://api.trustpilot.com/v1';
	const ENABLE_CACHE = true;
	const CACHE_EXPIRATION = 12; // Hours
	const CACHE_VERSION = '3.0';

	/**
	 * Clear all Trustpilot review caches.
	 */
	public static function clear_cache()
	{
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
				$wpdb->esc_like('_transient_revix_trustpilot_') . '%'
			)
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
				$wpdb->esc_like('_transient_timeout_revix_trustpilot_') . '%'
			)
		);
	}

	/**
	 * Whether API credentials and a business identifier are configured.
	 *
	 * @return bool
	 */
	public static function is_configured()
	{
		$api_key = get_option('revix_trustpilot_api_key', '');
		if (empty($api_key)) {
			return false;
		}

		$buid = get_option('revix_trustpilot_business_unit_id', '');
		$url  = get_option('revix_trustpilot_url', '');

		return !empty($buid) || !empty($url);
	}

	/**
	 * Get reviews mapped to the plugin's display format.
	 *
	 * @param int   $count     Number of reviews to return.
	 * @param float $minRating Minimum star rating filter.
	 * @return array
	 */
	public function get_reviews($count = 5, $minRating = 0)
	{
		$count     = max(1, (int) $count);
		$minRating = (float) $minRating;

		if (!self::is_configured()) {
			return [];
		}

		$buid = $this->resolve_business_unit_id();
		if (empty($buid)) {
			return [];
		}

		$cache_key = 'revix_trustpilot_reviews_cache_v' . self::CACHE_VERSION . '_' . md5($buid . '|' . $count . '|' . $minRating);

		if (self::ENABLE_CACHE) {
			$cached = get_transient($cache_key);
			if (is_array($cached) && !empty($cached)) {
				return $cached;
			}
		}

		// Fetch a slightly larger page, then apply min_rating locally.
		// Trustpilot's `stars` query is exact-match, not a minimum.
		$per_page = min(100, max($count * 2, 20));
		$data     = $this->api_request(
			'business-units/' . rawurlencode($buid) . '/reviews',
			[
				'perPage' => $per_page,
				'orderBy' => 'createdat.desc',
			]
		);

		if (empty($data['reviews']) || !is_array($data['reviews'])) {
			return [];
		}

		$reviews = [];
		foreach ($data['reviews'] as $item) {
			$stars = isset($item['stars']) ? (int) $item['stars'] : 0;
			if ($stars < $minRating) {
				continue;
			}

			$consumer = isset($item['consumer']) && is_array($item['consumer']) ? $item['consumer'] : [];
			$author   = !empty($consumer['displayName']) ? $consumer['displayName'] : __('Anonymous', 'revix-reviews');
			$location = !empty($consumer['displayLocation']) ? $consumer['displayLocation'] : '';

			$reviews[] = [
				'author'  => $author,
				'country' => $location,
				'avatar'  => '',
				'title'   => isset($item['title']) ? (string) $item['title'] : '',
				'text'    => isset($item['text']) ? (string) $item['text'] : '',
				'rating'  => sprintf('Rated %d out of 5', $stars),
				'date'    => isset($item['createdAt']) ? (string) $item['createdAt'] : '',
			];

			if (count($reviews) >= $count) {
				break;
			}
		}

		if (self::ENABLE_CACHE && !empty($reviews)) {
			set_transient($cache_key, $reviews, self::CACHE_EXPIRATION * HOUR_IN_SECONDS);
		}

		return $reviews;
	}

	/**
	 * Get aggregate TrustScore and review count.
	 *
	 * @return array{average: string, count: string|int}
	 */
	public function get_summary()
	{
		$empty = [
			'average' => '',
			'count'   => '',
		];

		if (!self::is_configured()) {
			return $empty;
		}

		$buid = $this->resolve_business_unit_id();
		if (empty($buid)) {
			return $empty;
		}

		$cache_key = 'revix_trustpilot_summary_cache_v' . self::CACHE_VERSION . '_' . md5($buid);

		if (self::ENABLE_CACHE) {
			$cached = get_transient($cache_key);
			if (is_array($cached) && (isset($cached['average']) || isset($cached['count']))) {
				return $cached;
			}
		}

		$data = $this->api_request('business-units/' . rawurlencode($buid));
		if (empty($data) || !is_array($data)) {
			return $empty;
		}

		$average = '';
		if (isset($data['score']['trustScore'])) {
			$average = (string) $data['score']['trustScore'];
		} elseif (isset($data['score']['stars'])) {
			$average = (string) $data['score']['stars'];
		}

		$count = '';
		if (isset($data['numberOfReviews']['total'])) {
			$count = $data['numberOfReviews']['total'];
		}

		$summary = [
			'average' => $average,
			'count'   => $count,
		];

		if (self::ENABLE_CACHE && ($average !== '' || $count !== '')) {
			set_transient($cache_key, $summary, self::CACHE_EXPIRATION * HOUR_IN_SECONDS);
		}

		return $summary;
	}

	/**
	 * Resolve Business Unit ID from settings or Trustpilot find API.
	 *
	 * @return string
	 */
	private function resolve_business_unit_id()
	{
		$stored = trim((string) get_option('revix_trustpilot_business_unit_id', ''));
		if ($stored !== '') {
			return $stored;
		}

		$domain = self::extract_domain_from_url(get_option('revix_trustpilot_url', ''));
		if ($domain === '') {
			return '';
		}

		$cache_key = 'revix_trustpilot_buid_v' . self::CACHE_VERSION . '_' . md5($domain);
		$cached    = get_transient($cache_key);
		if (is_string($cached) && $cached !== '') {
			return $cached;
		}

		$candidates = array_unique(array_filter([
			$domain,
			preg_replace('/^www\./i', '', $domain),
		]));

		foreach ($candidates as $name) {
			$data = $this->api_request('business-units/find', ['name' => $name]);
			if (!empty($data['id'])) {
				$buid = (string) $data['id'];
				set_transient($cache_key, $buid, WEEK_IN_SECONDS);
				return $buid;
			}
		}

		return '';
	}

	/**
	 * Extract domain slug from a Trustpilot review page URL.
	 *
	 * @param string $url Trustpilot review URL.
	 * @return string
	 */
	public static function extract_domain_from_url($url)
	{
		$url = trim((string) $url);
		if ($url === '') {
			return '';
		}

		$path = (string) wp_parse_url($url, PHP_URL_PATH);
		if ($path === '') {
			return '';
		}

		// Expected: /review/example.com
		if (preg_match('#/review/([^/]+)/?#i', $path, $matches)) {
			return rawurldecode($matches[1]);
		}

		$parts = array_values(array_filter(explode('/', trim($path, '/'))));
		return !empty($parts) ? rawurldecode(end($parts)) : '';
	}

	/**
	 * Perform a Trustpilot Public API GET request.
	 *
	 * @param string $path  Path under /v1/.
	 * @param array  $query Query arguments.
	 * @return array
	 */
	private function api_request($path, $query = [])
	{
		$api_key = trim((string) get_option('revix_trustpilot_api_key', ''));
		if ($api_key === '') {
			return [];
		}

		$url = trailingslashit(self::API_BASE) . ltrim($path, '/');
		if (!empty($query)) {
			$url = add_query_arg($query, $url);
		}

		$response = wp_remote_get($url, [
			'timeout' => 15,
			'headers' => [
				'apikey'     => $api_key,
				'Accept'     => 'application/json',
				'User-Agent' => 'RevixReviews/' . (defined('REVIXREVIEWS_VERSION') ? REVIXREVIEWS_VERSION : '1.0'),
			],
		]);

		if (is_wp_error($response)) {
			error_log('Revix Trustpilot Error: ' . $response->get_error_message());
			return [];
		}

		$code = wp_remote_retrieve_response_code($response);
		$body = wp_remote_retrieve_body($response);
		$data = json_decode($body, true);

		if ($code < 200 || $code >= 300) {
			$message = is_array($data) && isset($data['message']) ? $data['message'] : $body;
			error_log('Revix Trustpilot API HTTP ' . $code . ': ' . $message);
			return [];
		}

		return is_array($data) ? $data : [];
	}
}
