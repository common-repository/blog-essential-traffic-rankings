jQuery( document ).ready( function( $ ) {
    $('#data-table').DataTable({
        "pageLength": 25,
        "order": [],
        initComplete: function() {
            $('#data-table').show();
        },
        bAutoWidth: false
    });
});
