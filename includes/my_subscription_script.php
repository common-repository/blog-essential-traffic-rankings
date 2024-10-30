<?php

    if (!defined('ABSPATH')) exit;
    
    $propertyId = get_option('bpetr_property_id');
    $websiteIsPremium = get_option( 'bpetr_website_is_premium' );

    if ($websiteIsPremium == 1) {
        $paypalCancelRoute = sanitize_url(sprintf(
            "%s/api/v1/website/%s/paypal/cancel-subscription",
            BPETR_Admin::$bpetrApiUrl,
            $propertyId
        ));
    }

    $httpHost = sanitize_text_field($_SERVER['HTTP_HOST']);
    $requestUri = sanitize_text_field($_SERVER['REQUEST_URI']);

    $redirectCancelRoute = sprintf(
        '%s://%s%s&pp-sub-cancel=1',
        (empty($_SERVER['HTTPS']) ? 'http' : 'https'),
        $httpHost,
        $requestUri
    );

    wp_localize_script(
        'bp-page-js-script',
        'my_subscription_page_obj',
        array(
            'bp_token' => esc_js(get_option('bpetr_token')),
            'paypalCancelRoute' => esc_url($paypalCancelRoute),
            'redirectCancelRoute' => esc_url(sanitize_url($redirectCancelRoute))
        )
    );
