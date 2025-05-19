<div id="monthwiseptw"></div>

<script>
    var monthlyData = @json($monthlyCounts);

    var categories = Object.keys(monthlyData).map(function(month) {
        return new Date(2000, month - 1, 1).toLocaleString('default', {
            month: 'short'
        });
    });

    var permitCounts = Object.values(monthlyData);

    var options = {
        series: [{
            name: 'Permit Count',
            data: permitCounts
        }],
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: false
            },
            events: {
                dataPointSelection: function(event, chartContext, config) {
                    var dataPointIndex = config.dataPointIndex;
                    var monthName = chartContext.w.config.xaxis.categories[dataPointIndex];
                    var month = new Date(Date.parse(monthName + " 1, 2000")).getMonth() + 1;
                    redirectToPTW('', '', month,'');
                }
            }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                distributed: true
            },
        },
        colors: [
            '#008FFB', '#00E396', '#FEB019', '#FF4560',
            '#775DD0', '#546E7A', '#26a69a', '#D10CE8',
            '#9C27B0', '#F86624', '#2E294E', '#1B998B'
        ],
        dataLabels: {
            enabled: true
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: categories,
            title: {
                text: 'Month'
            }
        },
        yaxis: {
            title: {
                text: 'Permit Count'
            }
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val + " permits";
                }
            }
        }
    };

    // Render the chart
    var chart = new ApexCharts(document.querySelector("#monthwiseptw"), options);
    chart.render();

    // Function to download chart as PNG
    document.getElementById('monthwiseptw_download').addEventListener('click', function() {
        chart.dataURI().then(function(uri) {
            var link = document.createElement('a');
            link.href = uri.imgURI;
            link.download = 'Monthwise_PTW_Count.png';
            link.click();
        });
    });
</script>
