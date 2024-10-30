<?php

    if (!defined('ABSPATH')) exit;
    
    $isAuth = get_option('bpetr_is_auth');
    $propertyId = get_option('bpetr_property_id');
    $timeframe = get_option( 'bpetr_timeframe' );
    $websiteIsPremium = get_option( 'bpetr_website_is_premium' );

    if (isset($_GET['graphMetric']) && $_GET['graphMetric'] != '') {
        update_option( 'bpetr_graphMetric', sanitize_text_field($_GET['graphMetric']) );
    }
    $graphMetric = get_option( 'bpetr_graphMetric' );

    $slug = isset($_GET['slug']) ? sanitize_text_field($_GET['slug']) : '';

    if ($propertyId != null && $propertyId != '' && $isAuth == 1) {
    ?>
        <div class="wrap">
            <h1 class="">Post Traffic Analysis</h1>

            <?php require_once plugin_dir_path(__FILE__) .'../timeframe.php'; ?>

            <div class="row">
                <div class="col" style="font-size: 18px !important;">Post slug: <b><?php echo esc_html($slug) ; ?></b></div>
            </div>

            <div class="row mt-5">
                <div class="col-md-5">
                    <h3>Post Summary</h3>

                    <div id="post-traffic">
                        <div class="gradient-placeholder placeholder-h-100"></div>
                        <div class="gradient-placeholder placeholder-h-100"></div>
                    </div>
                </div>

                <div class="col-md-1"></div>

                <div class="col-md-6">
                    <h3>Traffic over time</h3>

                    <div class="row mb-2">
                        <div class="col">
                            <select class="form-control input-sm float-end" onchange="bpetr_setGraphMetric(this);" <?php if ($websiteIsPremium == 0) echo 'disabled="disabled"'; ?>>
                                <option value="screenPageViews" <?php if ($graphMetric == 'screenPageViews') echo 'selected'; ?>>Screen Page Views</option>
                                <option value="sessions" <?php if ($graphMetric == 'sessions') echo 'selected'; ?>>Sessions</option>
                                <option value="pv_organic" <?php if ($graphMetric == 'pv_organic') echo 'selected'; ?>>Organic</option>
                                <option value="averageSessionDuration" <?php if ($graphMetric == 'averageSessionDuration') echo 'selected'; ?>>Average Time Per Pageview</option>
                            </select>
                        </div>
                    </div>

                    <div id="graph-placeholder">
                        <div class="gradient-placeholder placeholder-h-200"></div>
                    </div>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-12">
                    <h3 class="mb-3">Top search queries for this post</h3>
                    
                    <div class="row mb-2">
                        <div class="col text-end">
                            <?php if ($websiteIsPremium == 1) { ?>
                                <a class="btn btn-sm btn-info" href="admin.php?page=post-traffic-analysis&slug=<?php echo esc_url($slug); ?>&csv-export=1" role="button">
                                    <i class="fa fa-download" aria-hidden="true"></i> CSV Export
                                </a>
                            <?php } ?>
                        </div>
                    </div>

                    <div id="post-search-queries">
                        <div class="gradient-placeholder placeholder-h-150"></div>
                        <div class="gradient-placeholder placeholder-h-100"></div>
                    </div>
                </div>
            </div>
        </div>
    <?php
	}
