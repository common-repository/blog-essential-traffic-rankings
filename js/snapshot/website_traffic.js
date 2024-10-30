function bpetr_getMetricChange(metric) {
    var metricChange = 0;
    if (metric[1] > 0) {
        var diff = metric[0] - metric[1];
        metricChange = (diff / metric[1]) * 100;
    }

    if (metricChange > 0) {
        var arrow = '<i class="fa fa-arrow-up" aria-hidden="true"></i>';
        var arrowClass = 'text-success';
    } else {
        var arrow = '<i class="fa fa-arrow-down" aria-hidden="true"></i>';
        var arrowClass = 'text-danger';
    }

    return [
        metricChange, arrow, arrowClass
    ];
}

jQuery( document ).ready( function( $ ) {
    $.ajax({
        url: website_traffic_obj.route,
        type: 'get',
        headers: {"Authorization": "Bearer " + website_traffic_obj.bp_token},
        dataType: "json",
        contentType: "application/json",
    }).done(function(data) {
        var website_traffic_data = '<div class="row">';
        website_traffic_data += '<div class="col-8"></div>';
        website_traffic_data += '<div class="col-2 text-muted"><small><i><div id="prev-time-frame">Prev. ' + website_traffic_obj.timeframe + '</div></i></small></div>';
        website_traffic_data += '<div class="col-2 text-muted"><small><i>Change</i></small></div>';
        website_traffic_data += '</div>';

        var metricChange = bpetr_getMetricChange(data.screenPageViews);
        website_traffic_data += '<div class="row spa-item">';
        website_traffic_data += '<div class="col-5">Page views</div>';
        website_traffic_data += '<div class="col-3">'+data.screenPageViews[0]+'</div>';
        if (website_traffic_obj.websiteIsPremium == '1') {
            website_traffic_data += '<div class="col-2 text-muted"><i><small>'+data.screenPageViews[1]+'</small></i></div>';
            website_traffic_data += '<div class="col-2"><span class="'+metricChange[2]+'"><small>'+metricChange[1]+metricChange[0].toFixed(1)+'%</small></div>';
        }
        website_traffic_data += '</div>';

        var metricChange = bpetr_getMetricChange(data.sessions);
        website_traffic_data += '<div class="row spa-item">';
        website_traffic_data += '<div class="col-5">Sessions</div>';
        website_traffic_data += '<div class="col-3">'+data.sessions[0]+'</div>';
        if (website_traffic_obj.websiteIsPremium == '1') {
            website_traffic_data += '<div class="col-2 text-muted"><i><small>'+data.sessions[1]+'</small></i></div>';
            website_traffic_data += '<div class="col-2"><span class="'+metricChange[2]+'"><small>'+metricChange[1]+metricChange[0].toFixed(1)+'%</small></div>';
        } else {
            website_traffic_data += '<div class="col-4">';
            website_traffic_data += '<a href="admin.php?page=my-subscription">Upgrade</a> to see all';
            website_traffic_data += '</div>';
        }
        website_traffic_data += '</div>';

        var metricChange = bpetr_getMetricChange(data.organic);
        website_traffic_data += '<div class="row spa-item">';
        website_traffic_data += '<div class="col-5">Organic</div>';
        website_traffic_data += '<div class="col-3">'+data.organic[0]+'</div>';
        if (website_traffic_obj.websiteIsPremium == '1') {
            website_traffic_data += '<div class="col-2 text-muted"><i><small>'+data.organic[1]+'</small></i></div>';
            website_traffic_data += '<div class="col-2"><span class="'+metricChange[2]+'"><small>'+metricChange[1]+metricChange[0].toFixed(1)+'%</small></div>';
        }
        website_traffic_data += '</div>';

        var timeOnPage = '--';
        if (data.screenPageViewsPerSession[0] > 0) {
            timeOnPage = (data.averageSessionDuration[0] / data.screenPageViewsPerSession[0]).toFixed(0)
            var minutes = Math.floor(timeOnPage / 60);
            var seconds = timeOnPage - (minutes * 60);

            timeOnPage = minutes+':'+seconds.toLocaleString('en-US', {minimumIntegerDigits: 2, useGrouping:false});
        }

        website_traffic_data += '<div class="row spa-item">';
        website_traffic_data += '<div class="col-5">Avg. time per view</div>';
        website_traffic_data += '<div class="col-3">'+timeOnPage+'</div>';
        if (website_traffic_obj.websiteIsPremium == '1') {
            var prevTimeOnPage = '--';
            if (data.screenPageViewsPerSession[1] > 0) {
                prevTimeOnPage = (data.averageSessionDuration[1] / data.screenPageViewsPerSession[1]).toFixed(0)
                var minutesPrev = Math.floor(prevTimeOnPage / 60);
                var secondsPrev = prevTimeOnPage - (minutesPrev * 60);
                
                prevTimeOnPage = minutesPrev+':'+secondsPrev.toLocaleString('en-US', {minimumIntegerDigits: 2, useGrouping:false});
            }

            website_traffic_data += '<div class="col-2 text-muted"><i><small>'+prevTimeOnPage+'</small></i></div>';
            website_traffic_data += '<div class="col-2"></div>';
        }

        website_traffic_data += '</div>';

        var position = '';
        if (data.gscResult.position !== undefined) {
            position = data.gscResult.position.toFixed(1);
        }

        var prevPosition = '';
        if (data.prevGscResult.position !== undefined) {
            prevPosition = data.prevGscResult.position.toFixed(1);
        }
        
        var metricChange = 0;
        if (data.gscResult.position !== undefined && data.prevGscResult.position !== undefined ) {
            metricChange = data.gscResult.position - data.prevGscResult.position;
        }

        if (metricChange > 0) {
            var arrow = '<i class="fa fa-arrow-up" aria-hidden="true"></i>';
            var arrowClass = 'text-success';
        } else {
            var arrow = '<i class="fa fa-arrow-down" aria-hidden="true"></i>';
            var arrowClass = 'text-danger';
        }

        website_traffic_data += '<div class="row spa-item">';
        website_traffic_data += '<div class="col-5">Average rank</div>';
        website_traffic_data += '<div class="col-3">'+position+'</div>';
        website_traffic_data += '<div class="col-2 text-muted"><i><small>'+prevPosition+'</small></i></div>';
        website_traffic_data += '<div class="col-2"><span class="'+arrowClass+'"><small>'+arrow+metricChange.toFixed(1)+'%</small></div>';
        website_traffic_data += '</div>';

        var ctr = '--';
        if (undefined !== data.gscResult.ctr) {
            ctr = (data.gscResult.ctr * 100).toFixed(0) + '%';
        }

        var ctrPrev = '--';
        if (undefined !== data.prevGscResult.ctr) {
            ctrPrev = (data.prevGscResult.ctr * 100).toFixed(0) + '%';
        }
        website_traffic_data += '<div class="row spa-item">';
        website_traffic_data += '<div class="col-5">CTR</div>';
        website_traffic_data += '<div class="col-3">'+ctr+'</div>';
        website_traffic_data += '<div class="col-2 text-muted"><i><small>'+ctrPrev+'</small></i></div>';
        website_traffic_data += '<div class="col-2"></div>';
        website_traffic_data += '</div>';

        if (website_traffic_obj.websiteIsPremium == '1') {
            website_traffic_data += '<div class="row mt-2"><div class="col text-end">';
            website_traffic_data += '<a href="admin.php?page=traffic-change-analysis" role="button" class="btn btn-light">View changes by post</a>';
            website_traffic_data += '</div></div>';
        } else {
            website_traffic_data += '<div class="row mt-3"><div class="col text-center">';
            website_traffic_data += 'Please <a href="admin.php?page=my-subscription">upgrade</a> to view changes by post';
            website_traffic_data += '</div></div>';
        }

        $("#website-traffic").html(website_traffic_data);
    }).fail(function(err) {
        var errorStr = bpetr_checkGoogleAuthError(err);
        $("#website-traffic").html(errorStr);
    });
});
