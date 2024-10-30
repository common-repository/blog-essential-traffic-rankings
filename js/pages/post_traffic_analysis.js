function bpetr_setGraphMetric(item) {
    var value = item.value;
    if (item.value == '') {
        return;
    }

    var route = post_traffic_analysis_page_obj.graphMetricRoute;
    route = route + value;

    document.location.href = route;
}

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
        url: post_traffic_analysis_page_obj.postTrafficRoute,
        type: 'get',
        headers: {"Authorization": "Bearer " + post_traffic_analysis_page_obj.bp_token},
        dataType: "json",
        contentType: "application/json",
    }).done(function(data) {
        var website_traffic_data = '<div class="row">';
        website_traffic_data += '<div class="col-8"></div>';
        website_traffic_data += '<div class="col-2 text-muted"><small><i><div id="prev-time-frame">Prev ' + post_traffic_analysis_page_obj.timeframe +'</div></i></small></div>';
        website_traffic_data += '<div class="col-2"><small><i>Change</i></small></div>';
        website_traffic_data += '</div>';

        var metricChange = bpetr_getMetricChange(data.screenPageViews);
        website_traffic_data += '<div class="row spa-item">';
        website_traffic_data += '<div class="col-5">Page views</div>';
        website_traffic_data += '<div class="col-3">'+data.screenPageViews[0]+'</div>';
        if (post_traffic_analysis_page_obj.websiteIsPremium == 1) {
            website_traffic_data += '<div class="col-2 text-muted"><i><small>'+data.screenPageViews[1]+'</small></i></div>';
            website_traffic_data += '<div class="col-2"><span class="'+metricChange[2]+'"><small>'+metricChange[1]+metricChange[0].toFixed(1)+'%</small></div>';
        }
        website_traffic_data += '</div>';

        var metricChange = bpetr_getMetricChange(data.sessions);
        website_traffic_data += '<div class="row spa-item">';
        website_traffic_data += '<div class="col-5">Sessions</div>';
        website_traffic_data += '<div class="col-3">'+data.sessions[0]+'</div>';
        if (post_traffic_analysis_page_obj.websiteIsPremium == 1) {
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
        if (post_traffic_analysis_page_obj.websiteIsPremium == 1) {
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
        if (post_traffic_analysis_page_obj.websiteIsPremium == 1) {
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

        $("#post-traffic").html(website_traffic_data);
    }).fail(function(err) {
        $("#post-traffic").html(
            'Error while loading data.<br />Error code: <b>' + err.status+ '</b>. Error message: <b>' + err.statusText + '</b>'
        );
    });

    $.ajax({
        url: post_traffic_analysis_page_obj.postTrafficGraphRoute,
        headers: {"Authorization": "Bearer " + post_traffic_analysis_page_obj.bp_token},
        type: 'get',
        dataType: "json",
        contentType: "application/json",
    }).done(function(data) {
        var ctx = document.getElementById('graph');
        $('#graph-placeholder').html('<canvas id="graph"></canvas>');
        var ctx = document.getElementById('graph');
        ctx.height = 80;

        new Chart(ctx, {
            type: 'line',
            data: {
                datasets: [{
                    label: post_traffic_analysis_page_obj.graphTitleKeys[post_traffic_analysis_page_obj.graphMetric],
                    data: data,
                    borderWidth: 4,
                    fill: {
                        target: 'origin',
                        above: 'rgb(229, 243, 249)',
                    }
                }]
            },
            options: {
                plugins: {
                    legend: {
                        labels: {
                            textAlign: 'right',
                            usePointStyle: true,
                            pointStyle: 'line',
                        }
                    }
                },
                scales: {
                    y: {
                        border: {
                             display: false,
                        },
                        grid: {
                            z: 2
                        },
                        ticks: {
                            maxTicksLimit: 4,
                            font: {
                                size: 11,
                            },
                        }
                    },
                    x: {
                        border: {
                            width: 2,
                            color: 'rgb(0, 0, 0)',
                        },
                        grid: {
                            display: false,
                        },
                        ticks: {
                            maxTicksLimit: 8,
                            font: {
                                size: 11,
                            },
                        }
                    }
                }
            }
        });
    });

    $.ajax({
        url: post_traffic_analysis_page_obj.postTrafficSearchQueries,
        headers: {"Authorization": "Bearer " + post_traffic_analysis_page_obj.bp_token},
        type: 'get',
        dataType: "json",
        contentType: "application/json",
    }).done(function(data) {
        var top_sq_data = '<table id="data-table" class="display compact"><thead><tr><th>#</th><th>Search query</th><th>Clicks</th><th>CTR</th><th>Avg pos.</th><th>Impressions</th></tr></thead>';
        var idx = 1;
        $.each(data, function(index, value){
            top_sq_data += '<tr>';
            top_sq_data += '<td>'+idx+'</td>';
            top_sq_data += '<td>'+index+'</td>';
            top_sq_data += '<td>'+value.clicks+'</td>';
            top_sq_data += '<td>'+(value.ctr * 100).toFixed(0)+'%</td>';
            top_sq_data += '<td>'+value.position.toFixed(1)+'</td>';
            top_sq_data += '<td>'+value.impressions+'</td>';
            top_sq_data += '</tr>';
            idx++;
        });

        top_sq_data += '</table>';

        if (post_traffic_analysis_page_obj.websiteIsPremium != 1) {
            top_sq_data += '<div class="row">';
            top_sq_data += '<div class="col text-center">';
            top_sq_data += 'To see all queries for this post, please <a href="admin.php?page=my-subscription">upgrade your subscription</a>';
            top_sq_data += '</div></div>';
        }

        $("#post-search-queries").html(top_sq_data);

        $('#data-table').DataTable({
            "pageLength": 50,
            "order": [],
            bFilter: (post_traffic_analysis_page_obj.websiteIsPremium != 1) ? false : true,
            lengthChange: (post_traffic_analysis_page_obj.websiteIsPremium != 1) ? false : true,
            paging: (post_traffic_analysis_page_obj.websiteIsPremium != 1) ? false : true,
        });
    }).fail(function(err) {
        $("#post-search-queries").html(
            'Error while loading data.<br />Error code: <b>' + err.status+ '</b>. Error message: <b>' + err.statusText + '</b>'
        );
    });
});
