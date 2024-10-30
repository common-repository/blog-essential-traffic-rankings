<?php

    if (!defined('ABSPATH')) exit;
    
    $isAuth = get_option('bpetr_is_auth');
    $paypalPlanId = get_option('bpetr_pp_plan_id');
    $paypalPlanPrice = get_option('bpetr_pp_plan_price');
    $propertyId = get_option('bpetr_property_id');

    if ($isAuth == 1 && null != $paypalPlanId) {
    ?>
        <div class="row">
            <div class="col">
                <div id="paypal-button-container-<?php echo esc_attr($paypalPlanId); ?>"></div>
            </div>
        </div>
        <div class="row" style="margin-top: -5px;">
            <div class="col text-end">
                <small><i><?php echo sprintf('$%s', number_format($paypalPlanPrice, 2)); ?>/month</i></small>
            </div>
        </div>

         <!-- Modal -->
        <div class="modal fade" id="ppPaymentTnxProgressModal" data-bs-backdrop="static"  data-bs-keyboard="false" tabindex="-1" aria-labelledby="ppPaymentTnxProgressModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content text-start">
                    <div class="modal-header text-start">
                        <h5 class="modal-title text-start" id="ppPaymentTnxProgressModalLabel">Transaction in Progress</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-2 text-start">
                            <div class="col">Transaction in Progress ...</div>
                        </div>

                        <div class="row mb-2">
                            <div class="col">Please do not reload the page</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
