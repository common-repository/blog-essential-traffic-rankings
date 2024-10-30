<?php

    if (!defined('ABSPATH')) exit;

    if (isset($_GET['timeframe']) && $_GET['timeframe'] != '' && isset($_GET['page']) && $_GET['page'] != '') {
        $websiteIsPremium = get_option( 'bpetr_website_is_premium' );

        $_SESSION['bpetr_timeframe'] = ($websiteIsPremium == 0)
            ? '30days'
            : sanitize_text_field($_GET['timeframe'])
        ;

        update_option( 'bpetr_timeframe', sanitize_text_field($_SESSION['bpetr_timeframe']) );

        $headerLocation = sprintf('admin.php?page=%s', sanitize_text_field($_GET['page']));
        if (isset($_GET['return-slug']) && $_GET['return-slug'] != '') {
            $headerLocation = sanitize_url(sprintf('%s&slug=%s', $headerLocation, sanitize_text_field($_GET['return-slug'])));
        }

        header('Location:' . $headerLocation);
    }
