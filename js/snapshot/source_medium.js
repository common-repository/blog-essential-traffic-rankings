jQuery( document ).ready( function( $ ) {
    $.ajax({
        url: source_medium_obj.route,
        headers: {"Authorization": "Bearer " + source_medium_obj.bp_token},
        type: 'get',
        dataType: "json",
        contentType: "application/json",
    }).done(function(data) {
        var output = '<table class="display table table-sm table-responsive table-hover"><thead><tr><th>Source / Medium</th><th>Users</th><th>Page Views</th><th>Sessions</th></tr></thead>';
        $.each(data.result, function(index, value){
            var sourceMedium = (index == '') ? '(not set)' : index;
            output += '<tr>';
            output += '<td>'+sourceMedium+'</td>';
            output += '<td>'+value.activeUsers+'</td>';
            output += '<td>'+value.screenPageViews+'</td>';
            output += '<td>'+value.sessions+'</td>';
            output += '</tr>';
        });

        output += '</table>';

        if (source_medium_obj.websiteIsPremium == 0) {
            output += '<div class="row"><div class="col text-center">';
            output += 'Please <a href="admin.php?page=my-subscription">upgrade</a> to see all your traffic';
            output += '</div></div>';
        } else {
            output += '<div class="row"><div class="col text-end">';
            output += '<a href="admin.php?page=traffic-sources" role="button" class="btn btn-light">See more</a>';
            output += '</div></div>';
        }

        $("#source-medium").html(output);
    }).fail(function(err) {
        var errorStr = bpetr_checkGoogleAuthError(err);
        $("#source-medium").html(errorStr);
    });
});
