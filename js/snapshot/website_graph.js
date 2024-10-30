jQuery( document ).ready( function( $ ) {
    $.ajax({
        url: website_graph_obj.route,
        headers: {"Authorization": "Bearer " + website_graph_obj.bp_token},
        type: 'get',
        dataType: "json",
        contentType: "application/json",
    }).done(function(data) {
        $('#graph-placeholder').html('<canvas id="graph"></canvas>');
        var ctx = document.getElementById('graph');
        ctx.height = 110;

        var jsonSessionsData = data.graphDataSesions;
        var jsonPvData = data.graphDataPv;

        new Chart(ctx, {
            type: 'line',
            data: {
                datasets: [
                    {
                        label: "Sessions",
                        data: jsonSessionsData,
                        borderWidth: 2
                    },
                    {
                        label: "screenPageViews",
                        data: jsonPvData,
                        borderWidth: 2
                    },
                ]
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
        
    }).fail(function(err) {
        var errorStr = bpetr_checkGoogleAuthError(err);
        $("#graph-placeholder").html(errorStr);
    });
});
