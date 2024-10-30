jQuery( document ).ready( function( $ ) {
    $('#data-table').DataTable({
        "pageLength": 50,
        "order": [],
        'bFilter': top_search_queries_page_obj.websiteIsPremium != 1 ? false : true,
        initComplete: function() {
            $('#data-table').show();
        },
        bAutoWidth: false
    });
});
