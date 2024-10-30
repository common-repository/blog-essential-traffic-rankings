<?php

    if (!defined('ABSPATH')) exit;
    
    $isAuth = get_option('bpetr_is_auth');
    $propertyId = get_option('bpetr_property_id');
    $websiteIsPremium = get_option( 'bpetr_website_is_premium' );

    if ($propertyId != null && $propertyId != '' && $isAuth == 1) {
        $timeframe = get_option( 'bpetr_timeframe' );
        $returnPage = (isset($_GET['page'])) ? sanitize_text_field($_GET['page']) : '';
        $returnSlug = (isset($_GET['slug']) && $_GET['slug'] != '') ? sanitize_text_field($_GET['slug']) : '';

        $updateRoute = sanitize_url(sprintf('admin.php?page=%s&return-slug=%s&timeframe=', $returnPage, $returnSlug));
    ?>
        <div class="row mt-1">
            <div class="col-md-12 text-end timeframe-links">
                <?php if ($websiteIsPremium == 0) {
                    ?>
                        <a href="#" class="timeframe-deactivated text-muted">
                            <?php if ($timeframe == '7days') echo '<i class="fa fa-check" aria-hidden="true"></i>'; ?>
                            7 days
                        </a>
                        <a href="#" class="timeframe-deactivated text-muted">
                            <?php if ($timeframe == '14days') echo '<i class="fa fa-check" aria-hidden="true"></i>'; ?>
                            14 days
                        </a>
                    <?php
                } else {
                    ?>
                    <a href="<?php echo esc_url($updateRoute . '7days'); ?>" <?php if ($timeframe == '7days') echo 'class="timeframe-link-selected"'; ?>>
                        <?php if ($timeframe == '7days') echo '<i class="fa fa-check" aria-hidden="true"></i>'; ?>
                        7 days
                    </a>
                    <a href="<?php echo esc_url($updateRoute . '14days'); ?>" <?php if ($timeframe == '14days') echo 'class="timeframe-link-selected"'; ?>>
                        <?php if ($timeframe == '14days') echo '<i class="fa fa-check" aria-hidden="true"></i>'; ?>
                        14 days
                    </a>
                    <?php
                }
                ?>
                <a href="<?php echo esc_url($updateRoute . '30days'); ?>" <?php if ($timeframe == '30days') echo 'class="timeframe-link-selected"'; ?>>
                    30 days
                    <?php if ($timeframe == '30days') echo '<i class="fa fa-check" aria-hidden="true"></i>'; ?>
                </a>

                <?php if ($websiteIsPremium == 0) {
                    ?>
                    <a href="#" class="timeframe-deactivated text-muted">
                        <?php if ($timeframe == '3months') echo '<i class="fa fa-check" aria-hidden="true"></i>'; ?>
                        3 months
                    </a>
                    <a href="#" class="timeframe-deactivated text-muted">
                        <?php if ($timeframe == '6months') echo '<i class="fa fa-check" aria-hidden="true"></i>'; ?>
                        6 months
                    </a>
                    <a href="#" class="timeframe-deactivated text-muted">
                        <?php if ($timeframe == '1year') echo '<i class="fa fa-check" aria-hidden="true"></i>'; ?>
                        1 year
                    </a>
                    <a href="#" class="timeframe-deactivated text-muted">
                        <?php if ($timeframe == '2years') echo '<i class="fa fa-check" aria-hidden="true"></i>'; ?>
                        2 years
                    </a>
                    <?php
                } else {
                ?>
                    <a href="<?php echo esc_url($updateRoute . '3months'); ?>" <?php if ($timeframe == '3months') echo 'class="timeframe-link-selected"'; ?>>
                        <?php if ($timeframe == '3months') echo '<i class="fa fa-check" aria-hidden="true"></i>'; ?>
                        3 months
                    </a>
                    <a href="<?php echo esc_url($updateRoute . '6months'); ?>" <?php if ($timeframe == '6months') echo 'class="timeframe-link-selected"'; ?>>
                        <?php if ($timeframe == '6months') echo '<i class="fa fa-check" aria-hidden="true"></i>'; ?>
                        6 months
                    </a>
                    <a href="<?php echo esc_url($updateRoute . '1year'); ?>" <?php if ($timeframe == '1year') echo 'class="timeframe-link-selected"'; ?>>
                        <?php if ($timeframe == '1year') echo '<i class="fa fa-check" aria-hidden="true"></i>'; ?>
                        1 year
                    </a>
                    <a href="<?php echo esc_url($updateRoute . '2years'); ?>" <?php if ($timeframe == '2years') echo 'class="timeframe-link-selected"'; ?>>
                        <?php if ($timeframe == '2years') echo '<i class="fa fa-check" aria-hidden="true"></i>'; ?>
                        2 years
                    </a>
                    <?php
                }
                ?>

            </div>
        </div>
    <?php
    }
