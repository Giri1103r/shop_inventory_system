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
            type: 'line',
            height: 350,
            toolbar: {
                show: false
            },
            events: {
                dataPointSelection: function(event, chartContext, config) {
                    var dataPointIndex = config.dataPointIndex;
                    var monthName = chartContext.w.config.xaxis.categories[dataPointIndex];
                    var month = new Date(Date.parse(monthName + " 1, 2000")).getMonth() + 1;
                    redirectToPTW('', '', month, '', '', '');
                }
            }
        },
        colors: ['#008FFB'],
        dataLabels: {
            enabled: true
        },
        stroke: {
            show: true,
            width: 2,
            offsetX: 10,
            offsetY: 10,
            curve: 'smooth'
        },
        xaxis: {
            categories: categories,
            title: {
                text: 'Month',
                offsetX: 10,
                offsetY: 10
            },

        },
        yaxis: {
            title: {
                text: 'Permit Count',
                offsetX: 10,
                offsetY: 30
            },

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
