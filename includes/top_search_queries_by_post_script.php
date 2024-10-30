<?php
    
    if (!defined('ABSPATH')) exit;
    
    $propertyId = get_option('bpetr_property_id');
    $timeframe = get_option( 'bpetr_timeframe' );
    $websiteIsPremium = get_option( 'bpetr_website_is_premium' );

    $route = sanitize_url(
        sprintf("%s/api/v1/website/%s/top-queries-by-post/%s", BPETR_Admin::$bpetrApiUrl, $propertyId, $timeframe)
    );
    wp_localize_script(
        'bp-page-js-script',
        'top_search_queries_by_post_page_obj',
        array(
            'route' => esc_url($route),
            'bp_token' => esc_js(get_option('bpetr_token')),
            'websiteIsPremium' => (int) $websiteIsPremium
        )
    );
