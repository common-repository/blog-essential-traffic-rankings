<?php

    if (!defined('ABSPATH')) exit;
    
    $isAuth = get_option('bpetr_is_auth');
    $propertyId = get_option('bpetr_property_id');
    $timeframe = get_option( 'bpetr_timeframe' );
    $websiteIsPremium = get_option( 'bpetr_website_is_premium' );

    if ($propertyId != null && $propertyId != '' && $isAuth == 1 && $websiteIsPremium == 1) {
        (isset($_GET['all-posts']) && $_GET['all-posts'] == '1' )
            ? update_option( 'bpetr_tca_all_posts', 1 )
            : update_option( 'bpetr_tca_all_posts', 0 )
        ;

        $allPosts = get_option( 'bpetr_tca_all_posts');

        $route = sanitize_url(
            sprintf("%s/api/v1/website/%s/traffic-change-analysis/%s/%s", BPETR_Admin::$bpetrApiUrl, $propertyId, $timeframe, $allPosts)
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

        function bpetr_getMetricChange($metric)
        {
            $metricChange = 0;
            if (
                isset($metric[0])
                && isset($metric[1])
                && $metric[1] > 0
            ) {
                $diff = $metric[0] - $metric[1];
                $metricChange = ($diff / $metric[1]) * 100;
            }

            if ($metricChange > 0) {
                $arrow = '<i class="fa fa-arrow-up" aria-hidden="true"></i>';
                $arrowClass = 'text-success';
            } else {
                $arrow = '<i class="fa fa-arrow-down" aria-hidden="true"></i>';
                $arrowClass = 'text-danger';
            }

            return [
                'metricChange' => $metricChange,
                'arrow' => $arrow,
                'arrowClass' => $arrowClass
            ];
        }
    ?>
        <div class="wrap">
            <h1>Traffic Change Analysis</h1>
            <?php
                require_once plugin_dir_path(__FILE__) .'../timeframe.php';

                if (isset($results->data) && count((array) $results->data) > 0) {
                ?>
                    <div class="mt-1"></div>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            Also show non-post URLs <input type="checkbox" name="all_posts" id="all_posts" <?php if ($allPosts == 1) echo 'checked'; ?> />
                        </div>
                        <div class="col-md-6 text-end">
                            <a class="btn btn-sm btn-info" href="admin.php?page=traffic-change-analysis&csv-export=1" role="button">
                                <i class="fa fa-download" aria-hidden="true"></i> CSV Export
                            </a>
                        </div>
                    </div>
                    <table id="data-table" style="display: none;" class="display bp-listing-pagin-table"> <!-- compact -->
                        <thead>
                            <tr>
                                <th class="table-separator-border" style="max-width: 250px;">Post Slug</th>
                                <th>Page Views Current</th>
                                <th>Page Views Previous</th>
                                <th class="table-separator-border">% Change</th>
                                <th>Organic Current</th>
                                <th>Organic Previous</th>
                                <th class="table-separator-border">% Change</th>
                                <th>Avg Pos. Current</th>
                                <th>Avg Pos. Previous</th>
                                <th>% Change</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            foreach ($results->data as $slug => $metrics) {
                                $pvData = bpetr_getMetricChange($metrics->screenPageViews);
                                $organicData = bpetr_getMetricChange($metrics->organic);

                                $positionCurrent = 0;
                                if (isset($metrics->gsc) && isset($metrics->gsc->position)) {
                                    $positionCurrent = $metrics->gsc->position;
                                }

                                $positionPrev = 0;
                                if (isset($metrics->prevGsc) && isset($metrics->prevGsc->position)) {
                                    $positionPrev = $metrics->prevGsc->position;
                                }

                                $posChange = 0;
                                if (
                                    isset($metrics->gsc) && isset($metrics->gsc->position)
                                    && isset($metrics->prevGsc) && isset($metrics->prevGsc->position)
                                ) {
                                    $posChange = $metrics->gsc->position - $metrics->prevGsc->position;
                                }

                                if ($posChange > 0) {
                                    $arrow = '<i class="fa fa-arrow-up" aria-hidden="true"></i>';
                                    $arrowClass = 'text-danger';
                                } else {
                                    $arrow = '<i class="fa fa-arrow-down" aria-hidden="true"></i>';
                                    $arrowClass = 'text-success';
                                }
                            ?>
                                <tr>
                                    <td class="table-separator-border">
                                        <a href="admin.php?page=post-traffic-analysis&slug=<?php echo esc_url($slug); ?>" target="_blank">
                                            <?php echo esc_html($slug); ?>
                                        </a>
                                    </td>
                                    <td class="text-center"><?php echo isset($metrics->screenPageViews[0]) ? (int) $metrics->screenPageViews[0] : 0; ?></td>
                                    <td class="text-center text-muted"><?php echo isset($metrics->screenPageViews[1]) ? (int) $metrics->screenPageViews[1] : 0; ?></td>
                                    <td class="table-separator-border text-center">
                                        <span class="<?php echo esc_attr($pvData['arrowClass']); ?>">
                                            <?php 
                                                echo wp_kses_post($pvData['arrow']);
                                                echo number_format($pvData['metricChange'], 1) . '%'; 
                                            ?>
                                        </span>
                                    </td>
                                    <td class="text-center"><?php echo isset($metrics->organic[0]) ? (int) $metrics->organic[0] : 0; ?></td>
                                    <td class="text-center text-muted"><?php echo isset($metrics->organic[1]) ? (int) $metrics->organic[1] : 0; ?></td>
                                    <td class="table-separator-border text-center">
                                        <span class="<?php echo esc_attr($organicData['arrowClass']); ?>">
                                            <?php 
                                                echo wp_kses_post($organicData['arrow']);
                                                echo number_format($organicData['metricChange'], 1) . '%'; 
                                            ?>
                                        </span>
                                    </td>
                                    <td class="text-center"><?php echo number_format($positionCurrent, 1); ?></td>
                                    <td class="text-center text-muted"><?php echo number_format($positionPrev, 1); ?></td>
                                    <td class="table-separator-border text-center">
                                        <span class="<?php echo esc_attr($arrowClass); ?>">
                                            <?php 
                                                echo wp_kses_post($arrow);
                                                echo number_format($posChange, 1) . '%'; 
                                            ?>
                                        </span>
                                    </td>
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
            <div class="mb-3 mt-2 bg-light p-3">To see all your traffic and rankings, please <a href="admin.php?page=my-subscription">upgrade your subscription</a></div>
        <?php
    }
