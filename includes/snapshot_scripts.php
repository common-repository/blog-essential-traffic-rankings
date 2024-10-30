<?php

    if (!defined('ABSPATH')) exit;
    
    $isAuth = get_option('bpetr_is_auth');
    $propertyId = get_option('bpetr_property_id');
    $timeframe = get_option( 'bpetr_timeframe' );
    $websiteIsPremium = get_option( 'bpetr_website_is_premium' );
    $bpToken = get_option('bpetr_token');

    if ($propertyId != null && $propertyId != '' && $isAuth == 1) {
        /* Website Summary */
        wp_enqueue_script(
            'website-traffic-js',
            plugins_url( '../js/snapshot/website_traffic.js', __FILE__ ),
            array( 'jquery' )
        );

        $route = sanitize_url(
            sprintf("%s/api/v1/website/%s/snapshot/website-traffic/%s", BPETR_Admin::$bpetrApiUrl, $propertyId, $timeframe)
        );

        wp_localize_script(
            'website-traffic-js',
            'website_traffic_obj',
            array(
                'route' => esc_url($route),
                'bp_token' => esc_js($bpToken),
                'timeframe' => esc_js($timeframe),
                'websiteIsPremium' => (int) $websiteIsPremium
            ) 
        );

        /* Taffic over time */
        wp_enqueue_script(
            'website-graph-js',
            plugins_url( '../js/snapshot/website_graph.js', __FILE__ ),
            array( 'jquery' )
        );

        $route = sanitize_url(
            sprintf("%s/api/v1/website/%s/snapshot/traffic-over-time/%s", BPETR_Admin::$bpetrApiUrl, $propertyId, $timeframe)
        );

        wp_localize_script(
            'website-graph-js',
            'website_graph_obj',
            array(
                'route' => esc_url($route),
                'bp_token' => esc_js($bpToken)
            ) 
        );

        /* Top countries */
        wp_enqueue_script(
            'website-top-countries-js',
            plugins_url( '../js/snapshot/website_top_countries.js', __FILE__ ),
            array( 'jquery' )
        );

        $route = sanitize_url(
            sprintf("%s/api/v1/website/%s/snapshot/top-countries/%s", BPETR_Admin::$bpetrApiUrl, $propertyId, $timeframe)
        );

        wp_localize_script(
            'website-top-countries-js',
            'website_top_countries_obj',
            array(
                'route' => esc_url($route),
                'bp_token' => esc_js($bpToken),
                'websiteIsPremium' => (int) $websiteIsPremium
            )
        );

        /* Traffic & Rankings */
        wp_enqueue_script(
            'website-traffic-rankings-js',
            plugins_url( '../js/snapshot/website_traffic_rankings.js', __FILE__ ),
            array( 'jquery' )
        );

        $route = sanitize_url(
            sprintf("%s/api/v1/website/%s/snapshot/traffic-and-rankings/%s", BPETR_Admin::$bpetrApiUrl, $propertyId, $timeframe)
        );

        wp_localize_script(
            'website-traffic-rankings-js',
            'website_traffic_rankings_obj',
            array(
                'route' => esc_url($route),
                'bp_token' => esc_js($bpToken),
                'websiteIsPremium' => (int) $websiteIsPremium
            )
        );

        /* Traffic Sources */
        wp_enqueue_script(
            'source-medium-js',
            plugins_url( '../js/snapshot/source_medium.js', __FILE__ ),
            array( 'jquery' )
        );

        $route = sanitize_url(
            sprintf("%s/api/v1/website/%s/snapshot/source-medium/%s", BPETR_Admin::$bpetrApiUrl, $propertyId, $timeframe)
        );

        wp_localize_script(
            'source-medium-js',
            'source_medium_obj',
            array(
                'route' => esc_url($route),
                'bp_token' => esc_js($bpToken),
                'websiteIsPremium' => (int) $websiteIsPremium
            )
        );

        /* Top Search Queries */
        wp_enqueue_script(
            'top-search-queries-snapshot-js',
            plugins_url( '../js/snapshot/top_search_queries_snapshot.js', __FILE__ ),
            array( 'jquery' )
        );

        $route = sanitize_url(
            sprintf("%s/api/v1/website/%s/snapshot/top-search-queries/%s", BPETR_Admin::$bpetrApiUrl, $propertyId, $timeframe)
        );

        wp_localize_script(
            'top-search-queries-snapshot-js',
            'top_search_queries_snapshot_obj',
            array(
                'route' => esc_url($route),
                'bp_token' => esc_js($bpToken),
                'websiteIsPremium' => (int) $websiteIsPremium
            )
        );
    }
