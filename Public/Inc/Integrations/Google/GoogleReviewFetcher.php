<?php

namespace RevixReviews\Public\Inc\Integrations\Google;

class GoogleReviewFetcher {

    const API_URL = 'https://places.googleapis.com/v1/places/';

    /**
     * Fetch the Google Place Details API response using the new Places API.
     *
     * @return array
     */
    private static function fetch_data() {
        $api_key   = get_option('revix_google_api_key');
        $place_id  = get_option('revix_google_place_id');

        if (empty($api_key) || empty($place_id)) {
            error_log('Revix Google Error: API Key or Place ID is empty');
            return [];
        }

        // New Places API endpoint
        $url = self::API_URL . $place_id;

        // Make request with new API format
        $response = wp_remote_get($url, [
            'timeout' => 15,
            'headers' => [
                'Content-Type' => 'application/json',
                'X-Goog-Api-Key' => $api_key,
                'X-Goog-FieldMask' => 'displayName,rating,userRatingCount,reviews'
            ]
        ]);

        if (is_wp_error($response)) {
            error_log('Revix Google Error: ' . $response->get_error_message());
            return [];
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        // Check for errors in new API format
        if (isset($data['error'])) {
            error_log('Revix Google API Error: ' . $data['error']['message']);
            error_log('Revix Google API Status: ' . $data['error']['status']);
            return [];
        }

        if (empty($data)) {
            error_log('Revix Google Error: Empty API response');
            return [];
        }

        return $data;
    }

    /**
     * Get an array of Google reviews.
     * Converts new API format to match old format for compatibility.
     *
     * @return array
     */
    public static function get_reviews() {
        $result = self::fetch_data();

        if (!isset($result['reviews']) || !is_array($result['reviews'])) {
            return [];
        }

        // Convert new API format to old format for compatibility
        $converted_reviews = [];
        foreach ($result['reviews'] as $review) {
            $converted_reviews[] = [
                'author_name' => isset($review['authorAttribution']['displayName']) ? $review['authorAttribution']['displayName'] : 'Anonymous',
                'rating' => isset($review['rating']) ? intval($review['rating']) : 0,
                'text' => isset($review['text']['text']) ? $review['text']['text'] : '',
                'time' => isset($review['publishTime']) ? strtotime($review['publishTime']) : 0,
                'relative_time_description' => isset($review['relativePublishTimeDescription']) ? $review['relativePublishTimeDescription'] : '',
                'profile_photo_url' => isset($review['authorAttribution']['photoUri']) ? $review['authorAttribution']['photoUri'] : '',
            ];
        }

        return $converted_reviews;
    }

    /**
     * Get summary info: average rating, total reviews, business name.
     *
     * @return array
     */
    public static function get_summary() {
        $result = self::fetch_data();

        return [
            'name'        => isset($result['displayName']['text']) ? sanitize_text_field($result['displayName']['text']) : '',
            'rating'      => isset($result['rating']) ? floatval($result['rating']) : 0,
            'total_count' => isset($result['userRatingCount']) ? intval($result['userRatingCount']) : 0,
        ];
    }
}