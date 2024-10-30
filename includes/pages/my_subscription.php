<?php

    if (!defined('ABSPATH')) exit;
    
    $isAuth = get_option('bpetr_is_auth');
    $propertyId = get_option('bpetr_property_id');
    $websiteIsPremium = get_option( 'bpetr_website_is_premium' );

    if ($propertyId != null && $propertyId != '' && $isAuth == 1) {
    ?>
        <div class="wrap">
            <?php if ($websiteIsPremium == 1) { ?>
                <h1 class="mb-4">My Subscription</h1>
            <?php } else { ?>
                <div class="row mb-4">
                    <div class="col-md-9">
                        <h1>My Subscription</h1>
                    </div>
                    <div class="col-md-3">
                        <?php require_once plugin_dir_path(__FILE__) . '../paypal_button.php'; ?>
                    </div>
                </div>
            <?php } ?>
    <?php
        if ($websiteIsPremium == 1) {
            $renewalDateRoute = sanitize_url(
                sprintf("%s/api/v1/website/%s/paypal/renewal-date", BPETR_Admin::$bpetrApiUrl, $propertyId)
            );

            $data = wp_remote_get(
                $renewalDateRoute, [
                    'headers' => ["Authorization" => sprintf("Bearer %s", get_option('bpetr_token'))],
                    'timeout' => 50
                ]
            );

            $results = isset($data['body'])
                ? json_decode($data['body'])
                : []
            ;

            $renewalDate = (isset($results->renewalDate)) ? $results->renewalDate : '';
            $cancelDate = (isset($results->cancelDate)) ? $results->cancelDate : '';

        ?>
            <div class="row">
                <div class="col text-end"><a href="admin.php?page=blog-essential-traffic-rankings" class="btn btn-info">Go to your dashboard</a></div>
            </div>

            <div class="row mb-2">
                <div class="col">
                    <h5 class="mb-4">Premium features are active!</h5>

                    Detailed traffic &amp; ranking stats are available for your website, including: 
                    <ul class="subscription-info">
                        <li><i class="fa fa-check" aria-hidden="true"></i> Traffic & ranking stats beyond the top 10</li>
                        <li><i class="fa fa-check" aria-hidden="true"></i> Change the time frame to 7 days, 14 days, 3 months, etc</li>
                        <li><i class="fa fa-check" aria-hidden="true"></i> Detailed geo users and sessions</li>
                        <li><i class="fa fa-check" aria-hidden="true"></i> Traffic change vs previous 30 days</li>
                        <li><i class="fa fa-check" aria-hidden="true"></i> Detailed referrals report that reveals where your traffic comes from</li>
                        <li><i class="fa fa-check" aria-hidden="true"></i> Search queries users enter in Google to get to this website</li>
                        <li><i class="fa fa-check" aria-hidden="true"></i> Search queries used to get to each post</li>
                    </ul>
                </div>
            </div>

            <?php
                if ($renewalDate != '' || $cancelDate != '') {
            ?>
                <div class="row mb-1">
                    <?php if ($cancelDate != '') { ?>
                        <div class="col text-end text-danger">Subscription cancelled on <b><?php echo esc_html($cancelDate); ?></b></div>
                    <?php } else { ?>
                        <div class="col text-end text-muted"><small>Renewal date: <?php echo esc_html($renewalDate); ?></small></div>
                    <?php } ?>
                </div>
            <?php
                }
            ?>

            <?php
                if ($cancelDate == '') {
            ?>
                <div class="row">
                    <div class="col text-end">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#cancelSubscriptionModal" class="btn btn-sm btn-light">Cancel your subscription</a>
                    </div>
                </div>
            <?php } ?>
        <?php
        } else {
        ?>
            <div class="row mb-5">
                <div class="col">
                    <h5 class="mb-4">Get Premium for a lot more detailed traffic & ranking data for your website:</h5>

                    <ul class="subscription-info">
                        <li><i class="fa fa-check" aria-hidden="true"></i> Traffic & ranking stats beyond the top 10</li>
                        <li><i class="fa fa-check" aria-hidden="true"></i> Change the time frame to 7 days, 14 days, 3 months, etc</li>
                        <li><i class="fa fa-check" aria-hidden="true"></i> Detailed geo users and sessions</li>
                        <li><i class="fa fa-check" aria-hidden="true"></i> Traffic change vs previous 30 days</li>
                        <li><i class="fa fa-check" aria-hidden="true"></i> Detailed referrals report that reveals where your traffic comes from</li>
                        <li><i class="fa fa-check" aria-hidden="true"></i> Search queries users enter in Google to get to this website</li>
                        <li><i class="fa fa-check" aria-hidden="true"></i> Search queries used to get to each post</li>
                </div>
            </div>
        <?php
        }
    ?>
            <hr />

            <h1 class="mb-4">Invoices</h1>
            <?php
                $invoiceRoute = sanitize_url(
                    sprintf("%s/api/v1/website/%s/invoices", BPETR_Admin::$bpetrApiUrl, $propertyId)
                );

                $invoiceData = wp_remote_get(
                    $invoiceRoute, [
                        'headers' => ["Authorization" => sprintf("Bearer %s", get_option('bpetr_token'))],
                        'timeout' => 50
                    ]
                );

                $invoiceResults = isset($invoiceData['body'])
                    ? json_decode($invoiceData['body'])
                    : []
                ;

                if (isset($invoiceResults->data) && count((array) $invoiceResults->data) > 0) {
                    $data = (array) $invoiceResults->data;
                ?>
                    <div class="mt-3"></div>
                    <table id="data-table" class="display">
                        <thead>
                            <tr>
                                <th class="table-separator-border">#</th>
                                <th>Invoice Date</th>
                                <th>Amount</th>
                                <th>Paypal Subscription Id</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                foreach ($data as $invoice) {
                                ?>
                                    <tr>
                                        <td class="table-separator-border">
                                            <b><?php echo esc_html($invoice->invoiceId); ?></b>
                                        </td>
                                        <td><?php echo esc_html($invoice->createAt); ?></td>
                                        <td>&dollar;<?php echo number_format($invoice->amount, 2); ?></td>
                                        <td><?php echo esc_html($invoice->subscriptionId); ?></td>
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

            <!-- Modal -->
            <div class="modal fade" id="cancelSubscriptionModal" tabindex="-1" aria-labelledby="cancelSubscriptionModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="cancelSubscriptionModalLabel">Cancel your subscription</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row mb-2">
                                <div class="col">
                                    Once you cancel, you will continue to have access to the premium features until the end of the period.
                                    Once the current period expires, you will revert to the free version of the plugin.
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col">To cancel your subscription, simply click the button below.</div>
                            </div>

                            <div class="row mb-3">

                                <div id="bp-pp-cancel-btn">
                                    <div class="col">
                                        <a href="#" onclick="bpetr_cancelSubscription()" class="btn btn-danger">Cancel your subscription</a>
                                    </div>
                                </div>

                                <div id="bp-pp-cancel-loading" style="display: none;">
                                    <div class="spinner-border text-secondary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
