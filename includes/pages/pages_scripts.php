<?php

    if (!defined('ABSPATH')) exit;
    
    $isAuth = get_option('bpetr_is_auth');
    $propertyId = get_option('bpetr_property_id');
    $websiteIsPremium = get_option( 'bpetr_website_is_premium' );

    if ($propertyId != null && $propertyId != '' && $isAuth == 1) {
        $premiumPages = ['traffic-and-rankings', 'traffic-change-analysis', 'traffic-sources', 'traffic-by-country'];

        if (
            !in_array($_GET['page'], $premiumPages)
            || (in_array($_GET['page'], $premiumPages) && $websiteIsPremium == 1)
        ) {
            wp_enqueue_script(
                'bp-page-js-script',
                plugins_url( $pageScript, __FILE__ ),
                array( 'jquery' )
            );

            if ($_GET['page'] == 'search-queries') {
                wp_localize_script(
                    'bp-page-js-script',
                    'top_search_queries_page_obj',
                    array(
                        'websiteIsPremium' => (int) $websiteIsPremium
                    )
                );
            } elseif ($_GET['page'] == 'top-queries-by-post') {
                require_once plugin_dir_path(__FILE__) .'../../includes/top_search_queries_by_post_script.php';
            } elseif ($_GET['page'] == 'my-subscription') {
                require_once plugin_dir_path(__FILE__) .'../../includes/my_subscription_script.php';
            } elseif ($_GET['page'] == 'post-traffic-analysis') {
                require_once plugin_dir_path(__FILE__) .'../../includes/post_traffic_analysis_script.php';
            }
        }
    }
