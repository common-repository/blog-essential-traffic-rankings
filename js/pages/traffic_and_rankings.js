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
        var route = 'admin.php?page=traffic-and-rankings&all-posts=';

        ($('#all_posts').is(':checked'))
            ? route += '1'
            : route += '0'
        ;

        document.location.href = route;
    });
});
