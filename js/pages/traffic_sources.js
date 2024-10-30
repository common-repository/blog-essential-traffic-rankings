jQuery( document ).ready( function( $ ) {
    $('#data-table').DataTable({
        "pageLength": 25,
        "order": [],
        initComplete: function() {
            $('#data-table').show();
        },
        bAutoWidth: false
    });

    $( "#ch_filter" ).change(function() {
        var route = 'admin.php?page=traffic-sources&channelfilter=' + this.value;
        document.location.href = route;
    });
});