<?php

if (!defined('ABSPATH')) exit;

function bpetr_settingPageData()
{
    global $wpdb;
    $tableName = $wpdb->prefix . 'bpetr_config';

    $query = $wpdb->prepare(
        "SELECT * FROM %i Limit 1", $tableName
    );

    $result = $wpdb->get_row($query);

    update_option( 'bpetr_property_id', $result->property_id );
    update_option( 'bpetr_is_auth', $result->is_auth );
    update_option( 'bpetr_username', $result->username );

    if ($result->is_auth == 0) {
        update_option( 'bpetr_website_is_premium', 0 );
    }
}

function bpetr_getCurrentTimeframe()
{
    $timeframe = sanitize_text_field($_SESSION['bpetr_timeframe']);

    if ($timeframe == '') {
        $timeframe = BPETR_Admin::BPETR_DEFAULT_TIMEFRAME;
    }

    update_option( 'bpetr_timeframe', $timeframe );
}

function bpetr_getGraphMetric()
{
    $metric = sanitize_text_field($_SESSION['bpetr_graphMetric']);

    if ($metric == '') {
        $metric = BPETR_Admin::BPETR_DEFAULT_GRAPH_METRIC;
    }

    update_option( 'bpetr_graphMetric', $metric );
}

bpetr_settingPageData();
bpetr_getCurrentTimeframe();
bpetr_getGraphMetric();
