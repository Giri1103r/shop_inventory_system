<div id="dailyObservation"></div>


<script>
    gembawalk = @json($chartData);

    if (!gembawalk.series.length || gembawalk.series.every(series => series.data.every(value => value.y === 0))) {
        document.getElementById("dailyObservation").innerHTML = "<div class='no-data'>No Data Found</div>";
    } else {
        const options = {
            series: gembawalk.series,
            chart: {
                type: 'bar',
                height: 350,
                stacked: true,
                toolbar: {
                    show: true
                },
                zoom: {
                    enabled: true
                },
                events: {
                    dataPointSelection: function(event, chartContext, config) {
                        let pointData = gembawalk.series[config.seriesIndex].data[config.dataPointIndex];
                        let unitId = pointData.custom.unit_id;
                        let observationTypeId = pointData.custom.observation_type_id;

                        // redirection
                        const url = "{{ admin_url('inspection/gemba-walk/list') }}";
                        redirectcharturl('gemba_walk', observationTypeId, url,unitId);
                    }
                }
            },
            colors: ['#4CAF50', '#2196F3'],
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '10%',

                }
            },
            dataLabels: {
                enabled: true,
                style: {
                    fontSize: '12px',
                    fontWeight: 'bold'
                }
            },
            xaxis: {
                type: 'category',
                categories: gembawalk.categories
            },
            legend: {
                position: 'bottom',
                horizontalAlign: 'center'
            },
            fill: {
                opacity: 1
            }
        };

        const chart = new ApexCharts(document.querySelector("#dailyObservation"), options);
        chart.render();



        document.getElementById('gembaWalkDownload').addEventListener('click', function() {
            chart.dataURI().then(function(uri) {
                const link = document.createElement('a');
                link.href = uri.imgURI;
                link.download = 'MonthWiseTrainingChart.png';
                link.click();
            });
        });
    }
</script>
