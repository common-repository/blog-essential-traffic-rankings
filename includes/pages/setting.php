<?php

    if (!defined('ABSPATH')) exit;
    
    $isAuth = get_option('bpetr_is_auth');
    $propertyId = get_option('bpetr_property_id');
    $websiteIsPremium = get_option( 'bpetr_website_is_premium' );

    if ($propertyId != null && $propertyId != '' && $isAuth == 1) {
        $httpHost = sanitize_text_field($_SERVER['HTTP_HOST']);
        $requestUri = sanitize_text_field($_SERVER['REQUEST_URI']);
        
        $actualLink = str_replace(
            "page=setting", 
            "page=blog-essential-traffic-rankings", 
            sprintf('%s://%s%s', (empty($_SERVER['HTTPS']) ? 'http' : 'https'), $httpHost, $requestUri)
        );

        $unauthorizeRoute = sanitize_url(
            sprintf("%s/website/%s/unauthorize?referer=%s", BPETR_Admin::$bpetrApiUrl, $propertyId, $actualLink)
        );
    ?>
        <div class="wrap">
            <h1 class="mb-4">Settings</h1>

            <div class="row mt-4">
                <div class="col-md-2">Property Id</div>
                <div class="col-md-6"><b><?php echo esc_html($propertyId); ?></b></div>
                <div class="col-md-3 text-end" style="text-align: right !important;">
                    <?php 
                        if ($websiteIsPremium == 0) {
                            require_once plugin_dir_path(__FILE__) . '../paypal_button.php';
                        }
                    ?>
                </div>
                <div class="col-md-1 text-end">
                    <a href="#" role="button" class="btn btn-sm btn-light" onclick="bpetr_confirmUrlAction('<?php echo esc_url($unauthorizeRoute); ?>')">
                        Unauthorize
                    </a>
                </div>
            </div>

            <hr />

            <div class="row mt-4">
                <div class="col">
                    Please feel free to <a href="mailto:plugin@bloggerplot.com" target="_blank">contact us</a> for any inquiries.
                </div>
            </div>
        </div>
    <?php
    }
