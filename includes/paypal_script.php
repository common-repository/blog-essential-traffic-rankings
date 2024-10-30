<?php

    if (!defined('ABSPATH')) exit;
    
    $isAuth = get_option('bpetr_is_auth');
    $propertyId = get_option('bpetr_property_id');
    $websiteIsPremium = get_option( 'bpetr_website_is_premium' );

    if ($propertyId != null && $propertyId != '' && $isAuth == 1 && $websiteIsPremium != 1) {
        wp_enqueue_script(
            'paypal-js',
            plugins_url( '../js/paypal/paypal.js', __FILE__ )
        );

        $paypalOrderRoute = sanitize_url(sprintf(
            "%s/api/v1/website/%s/order/paypal?pSubscriptionId=__subscriptionId__",
            BPETR_Admin::$bpetrApiUrl,
            $propertyId
        ));

        wp_localize_script(
            'paypal-js',
            'paypal_button_obj',
            array(
                'paypalButtonClientId' => esc_js(get_option('bpetr_pp_button_client_id')),
                'paypalSdk' => esc_js(get_option('bpetr_pp_skd_url')),
                'paypalPlanId' => esc_js(get_option('bpetr_pp_plan_id')),
                'propertyId' => esc_js($propertyId),
                'paypalOrderRoute' => esc_url($paypalOrderRoute),
                'bp_token' => esc_js(get_option('bpetr_token'))
            )
        );
    }
