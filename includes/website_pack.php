<?php

    if (!defined('ABSPATH')) exit;
    
	$isAuth = get_option('bpetr_is_auth');
    $propertyId = get_option('bpetr_property_id');
    $bpToken = get_option('bpetr_token');

    if ($propertyId != null && $propertyId != '' && $isAuth == 1) {
        $route = sanitize_url(
            sprintf("%s/api/v1/website/%s/is-premium", BPETR_Admin::$bpetrApiUrl, $propertyId)
        );

        $data = wp_remote_get(
            $route, [
                'headers' => ["Authorization" => sprintf("Bearer %s", $bpToken)],
                'timeout' => 30
            ]
        );

        $isPremium = 0;
        if (isset($data['body'])) {
            $data = json_decode($data['body']);
            if (isset($data->isPremium)) {
                $isPremium = (int) $data->isPremium;
            }
        }

        update_option( 'bpetr_website_is_premium', $isPremium );
    }
