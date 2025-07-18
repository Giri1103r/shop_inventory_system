<style>
    #TrainingMothWiseCount {
        width: 100%;
        height: 350px;
    }

    .no-data {
        text-align: center;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
        font-size: 18px;
        font-weight: bold;
        color: red;
    }
</style>

<div id="TrainingMothWiseCount"></div>


<script>
    TrainingCount = @json($chartData);

    if (!TrainingCount.series.length || TrainingCount.series.every(series => series.data.every(value => value === 0))) {
        document.getElementById("TrainingMothWiseCount").innerHTML = "<div class='no-data'>No Data Found</div>";
    } else {
        const options = {
            series: TrainingCount.series,
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
                        let selectedMonth = TrainingCount.categories[config.dataPointIndex];
                        let selectedStatus = TrainingCount.series[config.seriesIndex].id;
                        const url = "{{ admin_url('training_schedule/list') }}";
                        redirectTrainingcharturl(selectedMonth, selectedStatus, url);

                    }
                }
            },
            colors: ['#f39c12', '#e74c3c', '#3498db', '#2ecc71'],
            plotOptions: {
                bar: {
                    horizontal: false,
                    borderRadius: 10,
                    borderRadiusApplication: 'end',
                    borderRadiusWhenStacked: 'last'
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
                categories: TrainingCount.categories,
            },
            legend: {
                position: 'bottom',
                horizontalAlign: 'center'
            },
            fill: {
                opacity: 1
            }
        };

        const chart = new ApexCharts(document.querySelector("#TrainingMothWiseCount"), options);
        chart.render();

        document.getElementById('month_wise_training_count_download').addEventListener('click', function() {
            chart.dataURI().then(function(uri) {
                const link = document.createElement('a');
                link.href = uri.imgURI;
                link.download = 'MonthWiseTrainingChart.png';
                link.click();
            });
        });
    }
</script>
