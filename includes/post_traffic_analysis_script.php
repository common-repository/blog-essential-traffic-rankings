<?php

    if (!defined('ABSPATH')) exit;
    
    $propertyId = get_option('bpetr_property_id');
    $timeframe = get_option( 'bpetr_timeframe' );
    $websiteIsPremium = get_option( 'bpetr_website_is_premium' );

    if (isset($_GET['graphMetric']) && $_GET['graphMetric'] != '') {
        update_option( 'bpetr_graphMetric', sanitize_text_field($_GET['graphMetric']) );
    }
    $graphMetric = get_option( 'bpetr_graphMetric' );

    $slug = isset($_GET['slug']) ? sanitize_text_field($_GET['slug']) : '';

    $postTrafficRoute = sanitize_url(
        sprintf("%s/api/v1/website/%s/post-traffic/%s?slug=%s", BPETR_Admin::$bpetrApiUrl, $propertyId, $timeframe, $slug)
    );

    $postTrafficGraphRoute = sanitize_url(
        sprintf("%s/api/v1/website/%s/post-traffic-graph/%s/%s?slug=%s", BPETR_Admin::$bpetrApiUrl, $propertyId, $timeframe, $graphMetric, $slug)
    );

    $httpHost = sanitize_text_field($_SERVER['HTTP_HOST']);
    $requestUri = sanitize_text_field($_SERVER['REQUEST_URI']);
    $graphMetricRoute = sanitize_url(
        sprintf(
            '%s&graphMetric=',
            sprintf('%s://%s%s', (empty($_SERVER['HTTPS']) ? 'http' : 'https'), $httpHost, $requestUri)
        )
    );

    $graphTitleKeys = [
        'screenPageViews' => 'Screen Page Views',
        'sessions' => 'Sessions',
        'pv_organic' => 'Organic',
        'averageSessionDuration' => 'Average Time Per Pageview',
    ];

    $postTrafficSearchQueries = sanitize_url(
        sprintf("%s/api/v1/website/%s/post-traffic/search-queries/%s?slug=%s", BPETR_Admin::$bpetrApiUrl, $propertyId, $timeframe, $slug)
    );

    wp_localize_script(
        'bp-page-js-script',
        'post_traffic_analysis_page_obj',
        array(
            'postTrafficRoute' => esc_url($postTrafficRoute),
            'postTrafficGraphRoute' => esc_url($postTrafficGraphRoute),
            'graphMetricRoute' => esc_url($graphMetricRoute),
            'postTrafficSearchQueries' => esc_url($postTrafficSearchQueries),
            'bp_token' => esc_js(get_option('bpetr_token')),
            'websiteIsPremium' => (int) $websiteIsPremium,
            'timeframe' => esc_js($timeframe),
            'graphTitleKeys' => $graphTitleKeys,
            'graphMetric' => $graphMetric
        )
    );
