<?php

    if (!defined('ABSPATH')) exit;
    
    $isAuth = get_option('bpetr_is_auth');
    $propertyId = get_option('bpetr_property_id');
    $websiteIsPremium = get_option( 'bpetr_website_is_premium' );

    function bpetr_settings_section_callback() {
    }

    function bpetr_ga4_property_id() {
        $propertyId = get_option('bpetr_property_id');

        wp_nonce_field(
            sprintf('bpetr-property-id_%s_%s', sanitize_text_field($_SERVER['SERVER_NAME']), date('YmdHi')),
            'bpetr_check'
        )
        ;
        ?>
        <input type="text" name="ga4_property_id_fld" id="ga4_property_id_fld" class="form-control" placeholder="Property Id" value="<?php echo isset( $propertyId ) ? esc_attr( $propertyId ) : ''; ?>">
        <?php
    }

    const API_ERR_PAGE_MANE = 1;
    const API_ERR_EMPTY_PROPERTY_ID = 2;

    $flash = get_option('bpetr_setting_saved_flash');
    delete_option('bpetr_setting_saved_flash');
    ?>
    <div class="wrap">
        <?php if ($websiteIsPremium == 0) { ?>
            <div class="row">
                <div class="col-md-9">
                    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
                </div>
                <div class="col-md-3">
                    <?php 
                        if ($websiteIsPremium == 0) {
                            require_once plugin_dir_path(__FILE__) . 'paypal_button.php';
                        }
                    ?>
                </div>
            </div>
        <?php } else { ?>
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <?php } ?>
        <hr />

        <?php
            $apiErrorCode = isset($_GET['err']) ? $_GET['err'] : '';
            $apiErrorMessage = '';
            switch ((int) $apiErrorCode) {
                case API_ERR_PAGE_MANE:
                    $apiErrorMessage = 'Plugin page name not recognized';
                    break;

                case API_ERR_EMPTY_PROPERTY_ID:
                    $apiErrorMessage = 'The property id is mantadory';
                    break;
                
                default:
                    $apiErrorMessage = '';
                    break;
            }

            if ($apiErrorMessage != '') {
                ?>
                    <div id="message" class="error"><?php echo esc_html($apiErrorMessage); ?></div>
                <?php
            }
        ?>

        <?php
            if ($flash == true) {
        ?>
            <div id="message" class="updated">Settings saved</div>
        <?php
            }

            if ($isAuth != 1) {
            ?>
                <form action="<?php menu_page_url( 'blog-essential-traffic-rankings' ) ?>" method="post">

                    <div class="row mt-5">
                        <div class="col-md-6">
                            <?php do_settings_sections( 'bpetr-config' ); ?>
                        </div>

                        <div class="col-md-3">
                            <button class="btn btn-success mt-3" type="submit">Save</button>
                        </div>
                        <?php
                            if ($propertyId != null && $propertyId != '') {
                                $httpHost = sanitize_text_field($_SERVER['HTTP_HOST']);
                                $requestUri = sanitize_text_field($_SERVER['REQUEST_URI']);

                                $authorizeRoute = sanitize_url(sprintf(
                                    "%s/website/authorization?website=%s&property_id=%s&page=blog-essential-traffic-rankings&referer=%s",
                                    BPETR_Admin::$bpetrApiUrl,
                                    get_bloginfo('url'),
                                    $propertyId,
                                    sprintf('%s://%s%s', (empty($_SERVER['HTTPS']) ? 'http' : 'https'), $httpHost, $requestUri)
                                ));
                            ?>
                                <div class="col-md-3 mt-3 text-end">
                                    <a 
                                        role="button" 
                                        class="btn btn-info"
                                        href="<?php echo esc_url($authorizeRoute); ?>"
                                    >
                                        Authorize GA/GSC
                                    </a>
                                </div>
                            <?php
                            }
                        ?>
                    </div>
                </form>
                <?php
            }

            if ($propertyId != null && $propertyId != '' && $isAuth == 1) {
                require_once plugin_dir_path(__FILE__) .'timeframe.php';
                ?>

                <div class="row mt-5">
                    <div class="col-md-5">
                        <h2>Website Summary</h2>

                        <div id="website-traffic">
                            <div class="gradient-placeholder placeholder-h-100"></div>
                            <div class="gradient-placeholder placeholder-h-100"></div>
                        </div>
                    </div>

                    <div class="col-md-1"></div>

                    <div class="col-md-6">
                        <h2>Traffic Over Time</h2>

                        <div id="graph-placeholder">
                            <div class="gradient-placeholder placeholder-h-200"></div>
                        </div>
                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col-md-3">
                        <h2>Top Countries</h2>

                        <h6>Users</h6>

                        <div id="ctry-graph-placeholder">
                            <div class="gradient-placeholder placeholder-h-200"></div>
                        </div>

                        <div id="see-more-countries">
                        </div>
                    </div>

                    <div class="col-md-1"></div>

                    <div class="col-md-8">
                        <h2>Traffic & Rankings</h2>

                        <div id="website-traffic-and-rankings">
                            <div class="gradient-placeholder placeholder-h-150"></div>
                            <div class="gradient-placeholder placeholder-h-100"></div>
                        </div>
                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col-md-12">
                        <h2>Traffic Sources</h2>

                        <div id="source-medium">
                            <div class="gradient-placeholder placeholder-h-150"></div>
                            <div class="gradient-placeholder placeholder-h-100"></div>
                        </div>
                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col-md-12">
                        <h2>Top Search Queries</h2>

                        <div id="top-search-queries">
                            <div class="gradient-placeholder placeholder-h-150"></div>
                            <div class="gradient-placeholder placeholder-h-100"></div>
                        </div>
                    </div>
                </div>
                <?php
            }
        ?>

        <div class="row mt-5">
            <div class="col12">
                <small>This plugin for WordPress' use and transfer of information received from Google APIs to any other app will adhere to <a href="https://developers.google.com/terms/api-services-user-data-policy#additional_requirements_for_specific_api_scopes" target="_blank">Google API Services User Data Policy</a>, including the Limited Use requirements.</small>
            </div>
        </div>
    </div>
