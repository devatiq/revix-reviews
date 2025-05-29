<?php

namespace RevixReviews\Public\Inc\Integrations\Google;

class GoogleReviewFetcher {

    const API_URL = 'https://maps.googleapis.com/maps/api/place/details/json';

    /**
     * Fetch the Google Place Details API response.
     *
     * @return array
     */
    private static function fetch_data() {
        $api_key   = get_option('revix_google_api_key');
        $place_id  = get_option('revix_google_place_id');

        if (empty($api_key) || empty($place_id)) {
            return [];
        }

        $url = add_query_arg([
            'place_id' => $place_id,
            'fields'   => 'name,rating,user_ratings_total,reviews',
            'key'      => $api_key,
        ], self::API_URL);

        $response = wp_remote_get($url, ['timeout' => 15]);

        if (is_wp_error($response)) {
            return [];
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!isset($data['result']) || !is_array($data['result'])) {
            return [];
        }

        return $data['result'];
    }

    /**
     * Get an array of Google reviews.
     *
     * @return array
     */
    public static function get_reviews() {
        $result = self::fetch_data();

        return isset($result['reviews']) && is_array($result['reviews'])
            ? $result['reviews']
            : [];
    }

    /**
     * Get summary info: average rating, total reviews, business name.
     *
     * @return array
     */
    public static function get_summary() {
        $result = self::fetch_data();

        return [
            'name'        => isset($result['name']) ? sanitize_text_field($result['name']) : '',
            'rating'      => isset($result['rating']) ? floatval($result['rating']) : 0,
            'total_count' => isset($result['user_ratings_total']) ? intval($result['user_ratings_total']) : 0,
        ];
    }
}