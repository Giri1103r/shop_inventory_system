<div id="Training-Open-Close"></div>

<script>
    var TrainingCompletionOptions = {
        series: [
            {{ $formattedData['closed'] ?? 0 }},
            {{ $formattedData['open'] ?? 0 }}
        ],
        chart: {
            type: 'pie',
            height: 320,
            id: 'TrainingCompletionChart',
            events: {
                dataPointSelection: function(event, chartContext, config) {
                    var dataPointIndex = config.dataPointIndex;
                    var status = dataPointIndex === 0 ? 1 : 0;
                    const url = "{{ admin_url('training_schedule/list') }}";
                    redirectcharturl('open_close_status', status, url);
                }
            }
        },
        labels: ['Closed Trainings', 'Open Trainings'],
        colors: ['#28a745', '#dc3545'],
        dataLabels: {
            enabled: true,
            formatter: function (val, opts) {
                return opts.w.config.series[opts.seriesIndex] + ' Trainings';
            }
        },
        legend: {
            position: 'bottom'
        },
        tooltip: {
            y: {
                formatter: function (val, opts) {
                    let total = opts.w.globals.series.reduce((a, b) => a + b, 0);
                    let percentage = ((val / total) * 100).toFixed(1);
                    return `${val} Trainings (${percentage}%)`;
                }
            }
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom',
                    offsetX: 0,
                    offsetY: 0
                }
            }
        }]
    };

    var TrainingCompletionChart = new ApexCharts(document.querySelector("#Training-Open-Close"), TrainingCompletionOptions);
    TrainingCompletionChart.render();


    // Download button functionality
    $("#TrainingCompletion_download").off("click").on("click", function() {
        TrainingCompletionChart.dataURI().then(({
            imgURI,
            blob
        }) => {
            const link = document.createElement('a');
            link.href = imgURI;
            link.download = 'training_completion_chart.png';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    });
</script>
