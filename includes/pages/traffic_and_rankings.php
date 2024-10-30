<?php

    if (!defined('ABSPATH')) exit;
    
    $isAuth = get_option('bpetr_is_auth');
    $propertyId = get_option('bpetr_property_id');
    $timeframe = get_option( 'bpetr_timeframe' );
    $websiteIsPremium = get_option( 'bpetr_website_is_premium' );

    if ($propertyId != null && $propertyId != '' && $isAuth == 1 && $websiteIsPremium == 1) {
        (isset($_GET['all-posts']) && $_GET['all-posts'] == '1')
            ? update_option( 'bpetr_ta_all_posts', 1 )
            : update_option( 'bpetr_ta_all_posts', 0 )
        ;

        $allPosts = get_option( 'bpetr_ta_all_posts');

        $route = sanitize_url(
            sprintf("%s/api/v1/website/%s/traffic-and-rankings/%s/%s", BPETR_Admin::$bpetrApiUrl, $propertyId, $timeframe, $allPosts)
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
            <h1>Traffic &amp; Rankings</h1>
            <?php
                require_once plugin_dir_path(__FILE__) .'../timeframe.php';

                if (isset($results->result) && count((array) $results->result) > 0) {
                    $gaResults = (array) $results->result;
                ?>
                    <div class="mt-1"></div>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            Also show non-post URLs <input type="checkbox" name="all_posts" id="all_posts" <?php if ($allPosts == 1) echo 'checked'; ?> />
                        </div>
                        <div class="col-md-6 text-end">
                            <a class="btn btn-sm btn-info" href="admin.php?page=traffic-and-rankings&csv-export=1" role="button">
                                <i class="fa fa-download" aria-hidden="true"></i> CSV Export
                            </a>
                        </div>
                    </div>
                    <table id="data-table" style="display: none;" class="display bp-listing-pagin-table">
                        <thead>
                            <tr>
                                <th class="table-separator-border" style="max-width: 250px;">Post Slug</th>
                                <th>Page Views</th>
                                <th>%</th>
                                <th>Sess</th>
                                <th>%</th>
                                <th>Organic</th>
                                <th class="table-separator-border">%</th>
                                <th>Avg Pos.</th>
                                <th>CTR</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            foreach ($gaResults as $slug => $metrics) {
                                $position = (isset($metrics->gsc->position))
                                    ? number_format($metrics->gsc->position, 1)
                                    : ''
                                ;

                                $ctr = (isset($metrics->gsc->ctr))
                                    ? (number_format($metrics->gsc->ctr, 2) * 100) . "%"
                                    : ''
                                ;

                                $pvPercent = '';
                                if (isset($results->gaTotals->screenPageViews) && $results->gaTotals->screenPageViews > 0) {
                                    $pvPercent = number_format(($metrics->screenPageViews * 100) / $results->gaTotals->screenPageViews, 1) . '%';
                                }

                                $sessPercent = '';
                                if (isset($results->gaTotals->sessions) && $results->gaTotals->sessions > 0) {
                                    $sessPercent = number_format(($metrics->sessions * 100) / $results->gaTotals->sessions, 1) . '%';
                                }

                                $orgPercent = '';
                                if (isset($results->organicTotals->screenPageViews) && $results->organicTotals->screenPageViews > 0) {
                                    $orgPercent = number_format(($metrics->screenPageViews * 100) / $results->organicTotals->screenPageViews, 1) . '%';
                                }
                            ?>
                                <tr>
                                    <td class="table-separator-border">
                                        <a href="admin.php?page=post-traffic-analysis&slug=<?php echo esc_url($slug); ?>" target="_blank">
                                            <?php echo esc_html($slug); ?>
                                        </a>
                                    </td>
                                    <td><?php echo (int) $metrics->screenPageViews; ?></td>
                                    <td><?php echo esc_html($pvPercent); ?></td>
                                    <td><?php echo (int) $metrics->sessions; ?></td>
                                    <td><?php echo esc_html($sessPercent); ?></td>
                                    <td><?php echo (int) $metrics->organic; ?></td>
                                    <td class="table-separator-border"><?php echo esc_html($orgPercent); ?></td>
                                    <td><?php echo esc_html($position); ?></td>
                                    <td><?php echo esc_html($ctr); ?></td>
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
