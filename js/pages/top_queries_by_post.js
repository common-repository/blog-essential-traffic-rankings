jQuery( document ).ready( function( $ ) {
    $.ajax({
        url: top_search_queries_by_post_page_obj.route,
        headers: {"Authorization": "Bearer " + top_search_queries_by_post_page_obj.bp_token},
        type: 'get',
        dataType: "json",
        contentType: "application/json",
    }).done(function(data) {
        var output = '<table id="data-table" style="display: none; width: 100%;" class="display bp-listing-pagin-table">' +
            '<thead><tr><th>Post</th><th>Query 1</th><th>Query 2</th><th>Query 3</th><th>Query 5</th><th>Query 5</th></tr></thead><tbody>';
        $.each(data, function(index, value){
            output += '<tr><td><a href="'+index+'" target="_blank">'+index+'</a></td>';
            var c = 0;
            $.each(value, function(idx, metrics){
                c++;
                output += '<td>'+metrics.q+' ('+metrics.i+')</td>';
            });

            if (c < 5) {
                for (var i = c+1; i <= 5; i++) {
                    output += '<td>--</td>';
                }
            }
        });

        output += '</tbody></table>';
        $("#top-post-queries-box").html(output);

        $('#data-table').DataTable({
            "pageLength": 50,
            "order": [],
            'bFilter': top_search_queries_by_post_page_obj.websiteIsPremium != 1 ? false : true,
            initComplete: function() {
                $('#data-table').show();
            },
            bAutoWidth: false,
            "columns": [
                { "width": "30%" },
                { "width": "14%" },
                { "width": "14%" },
                { "width": "14%" },
                { "width": "14%" },
                { "width": "14%" }
            ]
        });
    }).fail(function(err) {
        var errorStr = bpetr_checkGoogleAuthError(err);
        $("#top-post-queries-box").html(errorStr);
    });
});
