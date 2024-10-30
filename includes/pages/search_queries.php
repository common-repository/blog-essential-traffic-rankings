<?php

    if (!defined('ABSPATH')) exit;
    
    $isAuth = get_option('bpetr_is_auth');
    $propertyId = get_option('bpetr_property_id');
    $timeframe = get_option( 'bpetr_timeframe' );
    $websiteIsPremium = get_option( 'bpetr_website_is_premium' );

    if ($propertyId != null && $propertyId != '' && $isAuth == 1) {
        $route = sanitize_url(
            sprintf("%s/api/v1/website/%s/search-queries/%s", BPETR_Admin::$bpetrApiUrl, $propertyId, $timeframe)
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
            <h1>Top Search Queries</h1>
            <?php
                require_once plugin_dir_path(__FILE__) .'../timeframe.php';

                if (count((array) $results) > 0) {
                ?>
                	<div class="mt-3"></div>
                    <div class="row mb-2">
                        <div class="col text-end">
                            <a href="admin.php?page=top-queries-by-post">Top queries by post</a>
                            <?php if ($websiteIsPremium == 1) { ?>
                                <a class="btn btn-sm btn-info" href="admin.php?page=search-queries&csv-export=1" role="button">
                                    <i class="fa fa-download" aria-hidden="true"></i> CSV Export
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                    
                	<table id="data-table" style="display: none;" class="display bp-listing-pagin-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Search query</th>
                                <th>Clicks</th>
                                <th>CTR</th>
                                <th>Avg pos.</th>
                                <th>Impressions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        	$idx = 1;
                            foreach ($results as $sq => $metrics) {
                            	$ctr = (isset($metrics->ctr))
                                    ? (number_format($metrics->ctr, 2) * 100) . "%"
                                    : ''
                                ;

                            	$position = (isset($metrics->position))
                                    ? number_format($metrics->position, 1)
                                    : ''
                                ;

                                $impressions = (isset($metrics->impressions))
                                    ? $metrics->impressions
                                    : ''
                                ;
                            ?>
                            	<tr>
                            		<td><?php echo (int) $idx++; ?></td>
                            		<td>
                                        <?php if ($websiteIsPremium == true) { ?>
                                            <span style="margin-right: 10px;">
                                                <a href="<?php echo esc_url($metrics->link); ?>" target="_blank">
                                                    <i class="fa fa-external-link" aria-hidden="true"></i>
                                                </a>
                                            </span>
                                        <?php } ?>
                                        
                                        <?php echo esc_html($sq); ?>
                                    </td>
                            		<td><?php echo (int) $metrics->clicks; ?></td>
                            		<td><?php echo esc_html($ctr); ?></td>
                            		<td><?php echo esc_html($position); ?></td>
                            		<td><?php echo (int) $impressions; ?></td>
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
   	}