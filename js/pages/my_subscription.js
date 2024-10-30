function bpetr_cancelSubscription() {
    if (confirm('Confirm?')) {
        jQuery('#bp-pp-cancel-btn').hide();
        jQuery('#bp-pp-cancel-loading').show();

        jQuery.ajax({
            url: my_subscription_page_obj.paypalCancelRoute,
            headers: {"Authorization": "Bearer " + my_subscription_page_obj.bp_token},
            method: 'POST',
        }).done(function(data) {
            document.location.href = my_subscription_page_obj.redirectCancelRoute;
        }).fail(function(err) {
            alert(err.status + ': ' + err.statusText);
            jQuery('#bp-pp-cancel-loading').hide();
            jQuery('#bp-pp-cancel-btn').show();
        });
    }
}

jQuery( document ).ready( function( $ ) {
    $('#data-table').DataTable({
        "pageLength": 25,
        "order": []
    });
});
