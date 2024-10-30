<?php

    if (!defined('ABSPATH')) exit;
    
    $isAuth = get_option('bpetr_is_auth');
    $propertyId = get_option('bpetr_property_id');
    $timeframe = get_option( 'bpetr_timeframe' );
    $websiteIsPremium = get_option( 'bpetr_website_is_premium' );

    if ($propertyId != null && $propertyId != '' && $isAuth == 1 && $websiteIsPremium == 1) {
        if (isset($_GET['channelfilter'])) {
            update_option( 'bpetr_channel_filter', sanitize_text_field($_GET['channelfilter']) );
        }
        $channelFilter = get_option( 'bpetr_channel_filter');

        $route = sanitize_url(
            sprintf("%s/api/v1/website/%s/source-medium/%s/%s", BPETR_Admin::$bpetrApiUrl, $propertyId, $timeframe, $channelFilter)
        );

        $data = wp_remote_get(
            $route, [
                'headers' => ["Authorization" => sprintf("Bearer %s", get_option('bpetr_token'))],
                'timeout' => 50
            ]
        );

        $results = isset($data['body'])
            ? json_decode($data['body'])
            : []
        ;
    ?>
        <div class="wrap">
            <h1>Traffic sources</h1>
            <?php
                require_once plugin_dir_path(__FILE__) .'../timeframe.php';
                ?>
                <div class="row mb-2 mt-1">
                    <div class="col-md-1">Filter</div>
                    <div class="col-md-3">
                        <select class="form-select form-select-sm" name="ch_filter" id="ch_filter">
                            <option value="">All Channels</option>
                            <option value="Direct" <?php if ($channelFilter == 'Direct') echo 'selected'; ?>>Direct</option>
                            <option value="Organic Search" <?php if ($channelFilter == 'Organic Search') echo 'selected'; ?>>Organic Search</option>
                            <option value="Organic Social" <?php if ($channelFilter == 'Organic Social') echo 'selected'; ?>>Organic Social</option>
                            <option value="Referral" <?php if ($channelFilter == 'Referral')  echo 'selected'; ?>>Referral</option>
                            <option value="Affiliates" <?php if ($channelFilter == 'Affiliates') echo 'selected'; ?>>Affiliates</option>
                            <option value="Paid Search" <?php if ($channelFilter == 'Paid Search') echo 'selected'; ?>>Paid Search</option>
                            <option value="Paid Social" <?php if ($channelFilter == 'Paid Social') echo 'selected'; ?>>Paid Social</option>
                            <option value="Video" <?php if ($channelFilter == 'Video') echo 'selected'; ?>>Video</option>
                            <option value="Email" <?php if ($channelFilter == 'Email') echo 'selected'; ?>>Email</option>
                            <option value="Display" <?php if ($channelFilter == 'Display') echo 'selected'; ?>>Display</option>
                        </select>
                    </div>
                     <div class="col-md-8 text-end">
                        <a class="btn btn-sm btn-info" href="admin.php?page=traffic-sources&csv-export=1" role="button">
                            <i class="fa fa-download" aria-hidden="true"></i> CSV Export
                        </a>
                    </div>
                </div>
                <?php
                if (isset($results->data) && count((array) $results->data) > 0) {
                ?>
                    <table id="data-table" style="display: none;" class="display bp-listing-pagin-table">
                        <thead>
                            <tr>
                                <th>Source / Medium</th>
                                <th>Users</th>
                                <th>Page Views</th>
                                <th>Sessions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            foreach ($results->data as $source => $metrics) {
                            ?>
                                <tr>
                                    <td><?php echo ($source == '') ? '(not set)' : esc_html($source); ?></td>
                                    <td><?php echo (int) $metrics->activeUsers; ?></td>
                                    <td><?php echo (int) $metrics->screenPageViews; ?></td>
                                    <td><?php echo (int) $metrics->sessions; ?></td>
                                </tr>
                            <?php
                            }
                        ?>
                        </tbody>
                    </table>
                <?php
                } else {
                    echo '<div class="mb-3 mt-2 bg-light p-3">No data found!</div>';
                }
            ?>
        </div>
    <?php
    } else {
        ?>
            <div class="mb-3 mt-2 bg-light p-3">To see all your traffic, please <a href="admin.php?page=my-subscription">upgrade your subscription</a></div>
        <?php
    }
