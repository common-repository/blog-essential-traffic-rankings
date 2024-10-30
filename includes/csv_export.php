<?php

if (!defined('ABSPATH')) exit;

$isAuth = get_option('bpetr_is_auth');
$propertyId = get_option('bpetr_property_id');
$timeframe = get_option( 'bpetr_timeframe' );
$websiteIsPremium = get_option( 'bpetr_website_is_premium' );

if ($propertyId != null && $propertyId != '' && $isAuth == 1 && $websiteIsPremium == 1) {
    function bpetr_array2csv($array, $filename)
    {
        if (count($array) == 0) {
            return null;
        }

        $output = fopen( 'php://output', 'w' );
        ob_start();

        foreach ($array as $row) {
            fputcsv($output, $row);
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename.'');

        fclose( $output );
        exit;
    }

    function bpetr_getTAData($results)
    {
        $data[] = ["Post slug", "Page Views", "%", "Sessions", "%", "Organic", "%", "Avg Pos.", "CTR"];

        if (!isset($results->result)) {
            return $data;
        }

        $gaResults = (array) $results->result;
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

            $data[] = [
                $slug,
                $metrics->screenPageViews,
                $pvPercent,
                $metrics->sessions,
                $sessPercent,
                $metrics->organic,
                $orgPercent,
                $position,
                $ctr
            ];
        }

        return $data;
    }

    function bpetr_getCsvMetricChange($metric)
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

        return $metricChange;
    }

    function bpetr_getTCAData($results)
    {
        $data[] = [
            "Post slug",
            "Page Views Current",
            "Page Views Previous",
            "% Change",
            "Organic Current",
            "Organic Previous",
            "% Change",
            "Avg Pos. Current",
            "Avg Pos. Previous",
            "% Change"
        ];

        if (!isset($results->data)) {
            return $data;
        }

        $gaResults = (array) $results->data;
        foreach ($gaResults as $slug => $metrics) {
            $pvChange = bpetr_getCsvMetricChange($metrics->screenPageViews);
            $organicChange = bpetr_getCsvMetricChange($metrics->organic);

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

            $data[] = [
                $slug,
                isset($metrics->screenPageViews[0]) ? $metrics->screenPageViews[0] : 0,
                isset($metrics->screenPageViews[1]) ? $metrics->screenPageViews[1] : 0,
                number_format($pvChange, 1) . '%',
                isset($metrics->organic[0]) ? $metrics->organic[0] : 0,
                isset($metrics->organic[1]) ? $metrics->organic[1] : 0,
                number_format($organicChange, 1) . '%',
                number_format($positionCurrent, 1),
                number_format($positionPrev, 1),
                number_format($posChange, 1) . '%',
            ];
        }

        return $data;
    }

    function bpetr_getSearchQueriesData($results)
    {
        $data[] = ["#", "Search query", "Clicks", "CTR", "Avg pos.", "Impressions"];

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

            $data[] = [
                $idx,
                $sq,
                $metrics->clicks,
                $ctr,
                $position,
                $impressions
            ];
        }

        return $data;
    }

    function bpetr_getTrafficSourceData($results)
    {
        $data[] = ["Source / Medium", "Users", "Page Views", "Sessions"];

        foreach ($results->data as $source => $metrics) {
            $data[] = [
                ($source == '') ? '(not set)' : $source,
                $metrics->activeUsers,
                $metrics->screenPageViews,
                $metrics->sessions
            ];
        }

        return $data;
    }

    function bpetr_getTrafficByCountryData($results)
    {
        $data[] = ["Country", "Users", "%", "Sessions", "%"];

        foreach ($results->result as $country => $metrics) {
            $usersPercent = '';
            if (isset($results->gaTotals->activeUsers) && $results->gaTotals->activeUsers > 0) {
                $usersPercent = number_format(($metrics->activeUsers * 100) / $results->gaTotals->activeUsers, 1) . '%';
            }

            $sessPercent = '';
            if (isset($results->gaTotals->sessions) && $results->gaTotals->sessions > 0) {
                $sessPercent = number_format(($metrics->sessions * 100) / $results->gaTotals->sessions, 1) . '%';
            }
            
            $data[] = [
                ucfirst($country),
                $metrics->activeUsers,
                $usersPercent,
                $metrics->sessions,
                $sessPercent,
            ];
        }

        return $data;
    }

    function bpetr_getTopQueriesByPostData($results)
    {
        $data[] = ["Post", "Query 1", "Query 2", "Query 3", "Query 4", "Query 5"];
        foreach ($results as $slug => $value) {
            $line = [];
            $line[] = $slug;
            foreach ($value as $metrics) {
                $line[] = $metrics->q;
            }

            $data[] = $line;
        }

        return $data;
    }

    function bpetr_getSearchQueriesByPostData($results)
    {
        $data[] = ["#", "Search query", "Clicks", "CTR", "Avg pos.", "Impressions"];

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

            $data[] = [
                $idx,
                $sq,
                $metrics->clicks,
                $ctr,
                $position,
                $impressions
            ];
        }

        return $data;
    }

    if (isset($_GET['csv-export']) && $_GET['csv-export'] && isset($_GET['page']) && $_GET['page'] != '') {
        if ($_GET['page'] == 'traffic-and-rankings') {
            $allPosts = get_option( 'bpetr_ta_all_posts');
            $route = sprintf("%s/api/v1/website/%s/traffic-and-rankings/%s/%s", BPETR_Admin::$bpetrApiUrl, $propertyId, $timeframe, $allPosts);
        } elseif ($_GET['page'] == 'traffic-change-analysis') {
            $allPosts = get_option( 'bpetr_tca_all_posts');
            $route = sprintf("%s/api/v1/website/%s/traffic-change-analysis/%s/%s", BPETR_Admin::$bpetrApiUrl, $propertyId, $timeframe, $allPosts);
        } elseif ($_GET['page'] == 'search-queries') {
            $route = sprintf("%s/api/v1/website/%s/search-queries/%s", BPETR_Admin::$bpetrApiUrl, $propertyId, $timeframe);
        } elseif ($_GET['page'] == 'traffic-sources') {
            $channelFilter = get_option( 'bpetr_channel_filter');
            $route = sprintf("%s/api/v1/website/%s/source-medium/%s/%s", BPETR_Admin::$bpetrApiUrl, $propertyId, $timeframe, $channelFilter);
        } elseif ($_GET['page'] == 'traffic-by-country') {
            $route = sprintf("%s/api/v1/website/%s/traffic-by-country/%s", BPETR_Admin::$bpetrApiUrl, $propertyId, $timeframe);
        } elseif ($_GET['page'] == 'top-queries-by-post') {
            $route = sprintf("%s/api/v1/website/%s/top-queries-by-post/%s", BPETR_Admin::$bpetrApiUrl, $propertyId, $timeframe);
        } elseif ($_GET['page'] == 'post-traffic-analysis' && isset($_GET['slug']) && $_GET['slug'] != '') {
            $route = sprintf(
                "%s/api/v1/website/%s/post-traffic/search-queries/%s?slug=%s",
                BPETR_Admin::$bpetrApiUrl,
                $propertyId,
                $timeframe,
                sanitize_text_field($_GET['slug'])
            );
        }

        $data = wp_remote_get(
            sanitize_url($route), [
                'headers' => ["Authorization" => sprintf("Bearer %s", get_option('bpetr_token'))],
                'timeout' => 50
            ]
        );

        $results = isset($data['body'])
            ? json_decode($data['body'])
            : []
        ;

        switch ($_GET['page']) {
            case 'traffic-change-analysis':
                $csvData = bpetr_getTCAData($results);
                $filename = 'traffic-change.csv';
                break;

            case 'search-queries':
                $csvData = bpetr_getSearchQueriesData($results);
                $filename = 'search-queries.csv';
                break;

            case 'traffic-sources':
                $csvData = bpetr_getTrafficSourceData($results);
                $filename = 'traffic-sources.csv';
                break;

            case 'traffic-by-country':
                $csvData = bpetr_getTrafficByCountryData($results);
                $filename = 'traffic-by-country.csv';
                break;

            case 'top-queries-by-post':
                $csvData = bpetr_getTopQueriesByPostData($results);
                $filename = 'top-queries-by-post.csv';
                break;

            case 'post-traffic-analysis':
                $csvData = bpetr_getSearchQueriesByPostData($results);
                $filename = 'post-traffic-analysis.csv';
                break;
            
            default:
                $csvData = bpetr_getTAData($results);
                $filename = 'traffic-and-rankings.csv';
                break;
        }
        
        bpetr_array2csv($csvData, $filename);
    }
}
