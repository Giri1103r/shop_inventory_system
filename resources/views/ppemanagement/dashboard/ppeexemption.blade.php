<script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.3/apexcharts.min.js"></script>

<script>
    var chartData = @json($chartData);
    console.log(chartData);

    var unitNames = chartData.map(data => data.unit_name);
    var totalData = chartData.map(data => data.total);
    var approvedData = chartData.map(data => data.approved);
    var rejectedData = chartData.map(data => data.rejected);

    var seriesData = [
        { name: 'Total', data: totalData },
        { name: 'Approved', data: approvedData },
        { name: 'Rejected', data: rejectedData }
    ];

    var options = {
        series: seriesData,
        chart: {
            type: 'bar',
            height: 450,
            stacked: true,
            toolbar: { show: true },
        },
        xaxis: {
            categories: unitNames,
        },
        plotOptions: {
            bar: {
                horizontal: false,
                borderRadius: 5,
            }
        },
        legend: {
            position: 'bottom',
            horizontalAlign: 'left',
        },
        tooltip: {
            shared: true,
            y: {
                formatter: function (val) {
                    return val + ' records';
                }
            }
        }
    };

    var ppeExemptionChart = new ApexCharts(document.querySelector("#Ppe_Exemption_Data"), options);
    ppeExemptionChart.render();
</script>
