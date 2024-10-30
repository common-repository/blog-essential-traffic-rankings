jQuery( document ).ready( function( $ ) {
    $.ajax({
        url: top_search_queries_snapshot_obj.route,
        headers: {"Authorization": "Bearer " + top_search_queries_snapshot_obj.bp_token},
        type: 'get',
        dataType: "json",
        contentType: "application/json",
    }).done(function(data) {
        var top_sq_data = '<table class="display table table-sm table-responsive table-hover"><thead><tr><th>Search query</th><th>CTR</th><th>Position</th><th>Clicks</th><th>Impressions</th></tr></thead>';
        $.each(data, function(index, value){
            top_sq_data += '<tr><td>';
            if (website_traffic_obj.websiteIsPremium == '1') {
                top_sq_data += '<span style="margin-right: 10px;"><a href="'+value.link+'" target="_blank">';
                top_sq_data += '<i class="fa fa-external-link" aria-hidden="true"></i></span></a>';
            }

            top_sq_data += index+'</td>';
            top_sq_data += '<td>'+(value.ctr * 100).toFixed(0)+'%</td>';
            top_sq_data += '<td>'+value.position.toFixed(1)+'</td>';
            top_sq_data += '<td>'+value.clicks+'</td>';
            top_sq_data += '<td>'+value.impressions+'</td>';
            top_sq_data += '</tr>';
        });

        top_sq_data += '</table>';
        top_sq_data += '<div class="row"><div class="col text-end">';
        top_sq_data += '<a href="admin.php?page=search-queries" role="button" class="btn btn-light">See more</a>';
        top_sq_data += '</div></div>';

        $("#top-search-queries").html(top_sq_data);
    }).fail(function(err) {
        var errorStr = bpetr_checkGoogleAuthError(err);
        $("#top-search-queries").html(errorStr);
    });
});
