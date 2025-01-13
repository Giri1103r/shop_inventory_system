<div id="unitwiseptw"></div>

<script>
    // Pass unit data from PHP to JavaScript
    var unitData = @json($unitData); // Assuming you're passing $unitData from the controller.

    // Extract categories (unit names) and series data (permit counts) from the data
    var categories = unitData.map(function(unit) {
        return unit.unit_name; // Extract unit_name for x-axis
    });

    var permitCounts = unitData.map(function(unit) {
        return unit.permit_count; // Extract permit_count for y-axis
    });

    // Define custom colors for each unit
    var customColors = ['#FF5733', '#33FF57', '#5733FF', '#FFC300', '#DAF7A6']; // Customize as needed

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
        colors: customColors, // Assign colors for each bar
        dataLabels: {
            enabled: true // Show data labels on bars
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: categories, // Unit names on the x-axis
            title: {
                text: 'Unit Name' // Label for x-axis
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
    var chart = new ApexCharts(document.querySelector("#unitwiseptw"), options);
    chart.render();

    // Function to download chart as PNG
    document.getElementById('unitwiseptw_download').addEventListener('click', function() {
        chart.dataURI().then(function(uri) {
            var link = document.createElement('a');
            link.href = uri.imgURI;
            link.download = 'Unitwise_PTW_Count.png';
            link.click();
        });
    });
</script>
