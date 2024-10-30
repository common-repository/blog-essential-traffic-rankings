function bpetr_loadAsync(url, callback) {
    var s = document.createElement('script');
    s.setAttribute('src', url); s.onload = callback;
    document.head.insertBefore(s, document.head.firstElementChild);
}

bpetr_loadAsync(paypal_button_obj.paypalSdk, function() {
    paypal.Buttons({
        style: {
            shape: 'rect',
            color: 'white',
            layout: 'horizontal',
            label: 'subscribe',
            tagline: false,
        },
        createSubscription: function(data, actions) {
            return actions.subscription.create({
                // Creates the subscription
                plan_id: paypal_button_obj.paypalPlanId,
                custom_id: paypal_button_obj.propertyId,
                application_context: {
                    shipping_preference: "NO_SHIPPING"
                }
            });
        },
        onApprove: function(data, actions) {
            var paypalOrderRoute = paypal_button_obj.paypalOrderRoute;
            paypalOrderRoute = paypalOrderRoute.replace("__subscriptionId__", data.subscriptionID);

            jQuery('#ppPaymentTnxProgressModal').modal('show');
            jQuery.ajax({
                url: paypalOrderRoute,
                headers: {"Authorization": "Bearer " + paypal_button_obj.bp_token},
                type: 'get',
                contentType: "application/json",
            }).done(function() {
                var callbackUrl = 'admin.php?page=blog-essential-traffic-rankings&pp-success=1';
                document.location.href = callbackUrl;
            }).fail(function() {
                alert('There was an error while saving your transaction. Please contact us.')
                var callbackUrl = 'admin.php?page=blog-essential-traffic-rankings&pp-txn-error=1';
                document.location.href = callbackUrl;
            })
        }
    }).render("#paypal-button-container-" + paypal_button_obj.paypalPlanId); // Renders the PayPal button
});
