<?php

    if (!defined('ABSPATH')) exit;
    
    $isAuth = get_option('bpetr_is_auth');
    $propertyId = get_option('bpetr_property_id');
    $timeframe = get_option( 'bpetr_timeframe' );
    $websiteIsPremium = get_option( 'bpetr_website_is_premium' );

    if ($propertyId != null && $propertyId != '' && $isAuth == 1) {
        $route = sanitize_url(
            sprintf("%s/api/v1/website/%s/top-queries-by-post/%s", BPETR_Admin::$bpetrApiUrl, $propertyId, $timeframe)
        );
    ?>
        <div class="wrap">
            <h1>Top Queries By Post</h1>
            
            <?php require_once plugin_dir_path(__FILE__) .'../timeframe.php'; ?>

        	<div class="mt-3"></div>
            <div class="row mb-2">
                <div class="col-md-8">
                    List of posts and their top 5 search queries (keywords) along with the number of impressions for each keyword.
                </div>
                <div class="col-md-4 text-end">
                    <a href="admin.php?page=search-queries">Top search queries</a>
                    <?php if ($websiteIsPremium == 1) { ?>
                        <a class="btn btn-sm btn-info" href="admin.php?page=top-queries-by-post&csv-export=1" role="button">
                            <i class="fa fa-download" aria-hidden="true"></i> CSV Export
                        </a>
                    <?php } ?>
                </div>
            </div>

            <div id="top-post-queries-box">
                <div class="gradient-placeholder placeholder-h-30 mt-3 mb-4"></div>

                <div class="gradient-placeholder placeholder-h-100"></div>
                <div class="gradient-placeholder placeholder-h-30"></div>
                <div class="gradient-placeholder placeholder-h-200"></div>
            </div>
        </div>
    <?php
   	}
