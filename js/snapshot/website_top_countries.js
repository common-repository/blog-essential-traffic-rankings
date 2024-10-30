jQuery( document ).ready( function( $ ) {
    $.ajax({
        url: website_top_countries_obj.route,
        headers: {"Authorization": "Bearer " + website_top_countries_obj.bp_token},
        type: 'get',
        dataType: "json",
        contentType: "application/json",
    }).done(function(data) {
        $('#ctry-graph-placeholder').html('<canvas id="ctry-graph"></canvas>');
        var ctx = document.getElementById('ctry-graph');
        //ctx.height = 50;

        var labels = data.labels;
        var values = data.values;

        new Chart(ctx, {
            type: 'pie',
            data: {
                datasets: [{
                    data: values,
                }],
                labels: labels
            },
            options: {
                responsive: true,
                legend: {
                    position: 'top',
                },
                title: {
                    display: false,
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
        });

        if (website_top_countries_obj.websiteIsPremium == 0) {
            var output = '<div class="row mt-2"><div class="col text-center">';
            output += 'Please <a href="admin.php?page=my-subscription">upgrade</a> to see all';
            output += '</div></div>';
        } else {
            var output = '<div class="row"><div class="col text-end">';
            output += '<a href="admin.php?page=traffic-by-country" role="button" class="btn btn-light">See more</a>';
            output += '</div></div>';
        }
        
        $('#see-more-countries').html(output);
    }).fail(function(err) {
        var errorStr = bpetr_checkGoogleAuthError(err);
        $("#ctry-graph-placeholder").html(errorStr);
    });
});
