<div id="Ppe_Exemption_Data"></div>

<script>
    var chartData = @json($chartData);


    var unitNames = chartData.map(data => data.unit_name);
    var totalData = chartData.map(data => data.total);
    var approvedData = chartData.map(data => data.approved);
    var rejectedData = chartData.map(data => data.rejected);

    var isDataAvailable = totalData.some(total => total > 0);

    if (!isDataAvailable) {
        document.getElementById('Ppe_Exemption_Data').innerHTML =
            "<div class='text-center fw-bold mt-5' style='position: absolute; top: 40%; left: 50%; transform: translate(-50%, -50%);'>No data is available</div>";
    }



    var seriesData = [{
            name: 'Total',
            data: totalData
        },
        {
            name: 'Approved',
            data: approvedData
        },
        {
            name: 'Rejected',
            data: rejectedData,
            // color: '#FF0000'
        }
    ];

    var options = {
        series: seriesData,
        chart: {
            type: 'bar',
            height: 450,
            stacked: false,
            toolbar: {
                show: false
            },
        },
        xaxis: {
            categories: unitNames,
        },
        plotOptions: {
            bar: {
                horizontal: false,
                borderRadius: 5,
                columnWidth: '40%' // You can adjust the width of bars
            }
        },
        legend: {
            position: 'bottom',
            horizontalAlign: 'left',
        },
        tooltip: {
            shared: false, // Disable shared tooltips
            intersect: true, // Only show tooltip when you intersect with a bar
            y: {
                formatter: function(val, {
                    seriesIndex
                }) {
                    const seriesName = seriesData[seriesIndex].name;
                    return seriesName + ' ' + val + ' records';
                }
            }
        }
    };

    var ppeExemptionChart = new ApexCharts(document.querySelector("#Ppe_Exemption_Data"), options);
    ppeExemptionChart.render();

    document.getElementById('ppeExemptionDownload').addEventListener('click', function() {
        ppeExemptionChart.dataURI().then(function(uri) {
            var link = document.createElement('a');
            link.href = uri.imgURI;
            link.download = 'Unitwise_PPE_Exemption_Count.png';
            link.click();
        });
    })
</script>
