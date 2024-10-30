jQuery( document ).ready( function( $ ) {
    $('#data-table').DataTable({
        "pageLength": 50,
        "order": [],
        initComplete: function() {
            $('#data-table').show();
        },
        bAutoWidth: false
    });

    $( "#all_posts" ).change(function() {
        var route = 'admin.php?page=traffic-change-analysis&all-posts=';

        if ($('#all_posts').is(':checked')) {
            route += '1'
        } else {
            route += '0'
        }

        document.location.href = route;
    });
});
