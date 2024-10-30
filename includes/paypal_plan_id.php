<?php

    if (!defined('ABSPATH')) exit;
    
    $isAuth = get_option('bpetr_is_auth');
    $websiteIsPremium = get_option( 'bpetr_website_is_premium' );

    if ($isAuth == 1 && $websiteIsPremium != 1) {
        $route = sanitize_url(
            sprintf("%s/api/v1/paypal/plan-id", BPETR_Admin::$bpetrApiUrl)
        );
        
        $data = wp_remote_get(
            $route, [
                'headers' => ["Authorization" => sprintf("Bearer %s", get_option('bpetr_token'))],
                'timeout' => 30
            ]
        );

        $planId = null;
        $paypalButtonClientId = null;
        $planPrice = null;
        $paypalSkdUrl = null;
        if (isset($data['body'])) {
            $planJson = json_decode($data['body']);

            if (isset($planJson->planId)) {
                $planId = $planJson->planId;
            }

            if (isset($planJson->paypalButtonClientId)) {
                $paypalButtonClientId = $planJson->paypalButtonClientId;
            }

            if (isset($planJson->price)) {
                $planPrice = $planJson->price;
            }

            if (isset($planJson->paypalSkdUrl)) {
                $paypalSkdUrl = $planJson->paypalSkdUrl;
            }
        }

        update_option( 'bpetr_pp_plan_id', $planId );
        update_option( 'bpetr_pp_button_client_id', $paypalButtonClientId );
        update_option( 'bpetr_pp_plan_price', $planPrice );
        update_option( 'bpetr_pp_skd_url', $paypalSkdUrl );
    }
