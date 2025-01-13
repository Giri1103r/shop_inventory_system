<div id="monthwiseptw"></div>

<script>
    // Pass month-wise data from PHP to JavaScript
    var monthlyData = @json($monthlyCounts); // Assuming $monthlyCounts is passed from the controller.

    // Extract categories (months) and series data (permit counts) from the data
    var categories = Object.keys(monthlyData).map(function(month) {
        return new Date(2000, month - 1, 1).toLocaleString('default', { month: 'short' }); // Convert month number to name (e.g., Jan, Feb)
    });

    var permitCounts = Object.values(monthlyData); // Permit counts for y-axis

    // ApexCharts configuration
    var options = {
        series: [{
            name: 'Permit Count',
            data: permitCounts // y-axis data
        }],
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: false // Hide the entire toolbar
            }
        },
        plotOptions: {
            bar: {
                horizontal: false, // Vertical bars
                columnWidth: '55%',
                endingShape: 'rounded'
            },
        },
        colors: ['#008FFB'], // Default color for the bars
        dataLabels: {
            enabled: true // Show data labels on bars
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: categories, // Months on the x-axis
            title: {
                text: 'Month' // Label for x-axis
            }
        },
        yaxis: {
            title: {
                text: 'Permit Count' // Label for y-axis
            }
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val + " permits"; // Tooltip format
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
