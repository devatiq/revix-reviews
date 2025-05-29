<?php
namespace RevixReviews\Frontend\Inc\Integrations\Google;

class GoogleReviewFetcher {

    const API_URL = 'https://maps.googleapis.com/maps/api/place/details/json';

    public static function get_reviews() {
        $api_key   = get_option('revix_google_api_key');
        $place_id  = get_option('revix_google_place_id');

        if (empty($api_key) || empty($place_id)) {
            return [];
        }

        $url = add_query_arg([
            'place_id' => $place_id,
            'fields'   => 'reviews',
            'key'      => $api_key,
        ], self::API_URL);

        $response = wp_remote_get($url, [
            'timeout' => 15,
        ]);

        if (is_wp_error($response)) {
            return [];
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!isset($data['result']['reviews'])) {
            return [];
        }

        return $data['result']['reviews'];
    }
}