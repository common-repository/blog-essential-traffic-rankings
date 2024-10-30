jQuery( document ).ready( function( $ ) {
    $.ajax({
        url: website_traffic_rankings_obj.route,
        headers: {"Authorization": "Bearer " + website_traffic_rankings_obj.bp_token},
        type: 'get',
        dataType: "json",
        contentType: "application/json",
    }).done(function(data) {
        var output = '<table class="display table table-sm table-responsive table-hover"><thead><tr><th>Post Slug</th><th>Page Views</th><th>Sess.</th><th>Avg pos.</th><th>CTR</th><th>Impr.</th></tr></thead>';
        $.each(data.gaResult, function(index, value){
            var position = '';
            if (undefined !== value.gsc && undefined !== value.gsc.position) {
                position = value.gsc.position.toFixed(1);
            }

            var ctr = '';
            if (undefined !== value.gsc && undefined !== value.gsc.ctr) {
                ctr = (value.gsc.ctr * 100).toFixed(0) + '%';
            }

            var impressions = '';
            if (undefined !== value.gsc && undefined !== value.gsc.impressions) {
                impressions = value.gsc.impressions;
            }

            output += '<tr>';
            output += '<td><a href="admin.php?page=post-traffic-analysis&slug='+index+'" target="_blank">'+index+'</a></td>';
            output += '<td>'+value.screenPageViews+'</td>';
            output += '<td>'+value.sessions+'</td>';
            output += '<td>'+position+'</td>';
            output += '<td>'+ctr+'</td>';
            output += '<td>'+impressions+'</td>';
            output += '</tr>';
        });

        output += '</table>';

        if (website_traffic_rankings_obj.websiteIsPremium == 0) {
            output += '<div class="row"><div class="col text-center">';
            output += 'To see all your traffic and rankings, please <a href="admin.php?page=my-subscription">upgrade your subscription</a>';
            output += '</div></div>';
        } else {
            output += '<div class="row"><div class="col text-end">';
            output += '<a href="admin.php?page=traffic-and-rankings" role="button" class="btn btn-light">See more</a>';
            output += '</div></div>';
        }

        $("#website-traffic-and-rankings").html(output);
    }).fail(function(err) {
        var errorStr = bpetr_checkGoogleAuthError(err);
        $("#website-traffic-and-rankings").html(errorStr);
    });
});
