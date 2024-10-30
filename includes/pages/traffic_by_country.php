<?php

    if (!defined('ABSPATH')) exit;
    
    $isAuth = get_option('bpetr_is_auth');
    $propertyId = get_option('bpetr_property_id');
    $timeframe = get_option( 'bpetr_timeframe' );
    $websiteIsPremium = get_option( 'bpetr_website_is_premium' );

    if ($propertyId != null && $propertyId != '' && $isAuth == 1 && $websiteIsPremium == 1) {
        $route = sanitize_url(
            sprintf("%s/api/v1/website/%s/traffic-by-country/%s", BPETR_Admin::$bpetrApiUrl, $propertyId, $timeframe)
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
            <h1>Traffic by Country</h1>
            <?php
                require_once plugin_dir_path(__FILE__) .'../timeframe.php';

                if (isset($results->result) && count((array) $results->result) > 0) {
                    $gaResults = (array) $results->result;
                ?>
                    <div class="mt-1"></div>
                    <div class="row mb-2">
                        <div class="col text-end">
                            <a class="btn btn-sm btn-info" href="admin.php?page=traffic-by-country&csv-export=1" role="button">
                                <i class="fa fa-download" aria-hidden="true"></i> CSV Export
                            </a>
                        </div>
                    </div>
                    <table id="data-table" style="display: none;" class="display compact">
                        <thead>
                            <tr>
                                <th class="table-separator-border">Country</th>
                                <th>Users</th>
                                <th>%</th>
                                <th>Sessions</th>
                                <th>%</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            foreach ($gaResults as $country => $metrics) {
                                $usersPercent = '';
                                if (isset($results->gaTotals->activeUsers) && $results->gaTotals->activeUsers > 0) {
                                    $usersPercent = number_format(($metrics->activeUsers * 100) / $results->gaTotals->activeUsers, 1) . '%';
                                }

                                $sessPercent = '';
                                if (isset($results->gaTotals->sessions) && $results->gaTotals->sessions > 0) {
                                    $sessPercent = number_format(($metrics->sessions * 100) / $results->gaTotals->sessions, 1) . '%';
                                }
                            ?>
                                <tr>
                                    <td class="table-separator-border"><?php echo esc_html(ucfirst($country)); ?></td>
                                    <td><?php echo (int) $metrics->activeUsers; ?></td>
                                    <td><?php echo esc_html($usersPercent); ?></td>
                                    <td><?php echo (int) $metrics->sessions; ?></td>
                                    <td><?php echo esc_html($sessPercent); ?></td>
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
            <div class="mb-3 mt-2 bg-light p-3">To see all countries, please <a href="admin.php?page=my-subscription">upgrade your subscription</a></div>
        <?php
    }
