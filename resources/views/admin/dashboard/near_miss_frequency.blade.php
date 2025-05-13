<div id="nearMiss"></div>

<script>
    var chartData = {!! json_encode($chartData) !!};

    // Map month numbers to names
    var monthNames = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];

    // Create an array of 12 zeros for each month
    var monthData = Array(12).fill(0);

    // Fill monthData with counts from chartData
    chartData.forEach(item => {
        const monthIndex = item.month - 1;
        monthData[monthIndex] = item.count;
    });

    var options = {
        series: [{
            name: 'Near Miss Count',
            data: monthData
        }],
        chart: {
            height: 350,
            type: 'bar',
            toolbar: {
                show: false
            },
        },
        plotOptions: {
            bar: {
                borderRadius: 10,
                dataLabels: {
                    position: 'top',
                },
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function(val) {
                return val;
            },
            offsetY: -20,
            style: {
                fontSize: '12px',
                colors: ["#304758"]
            }
        },
        xaxis: {
            categories: monthNames,
            position: 'top',
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false
            },
            crosshairs: {
                fill: {
                    type: 'gradient',
                    gradient: {
                        colorFrom: '#D8E3F0',
                        colorTo: '#BED1E6',
                        stops: [0, 100],
                        opacityFrom: 0.4,
                        opacityTo: 0.5,
                    }
                }
            },
            tooltip: {
                enabled: true,
            }
        },
        yaxis: {
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false
            },
            labels: {
                show: true,
                formatter: function(val) {
                    return val;
                }
            }
        },
        title: {
            text: 'Monthly Near Miss Incidents',
            floating: true,
            offsetY: 330,
            align: 'center',
            style: {
                color: '#444'
            }
        }
    };

    var nearMiss = new ApexCharts(document.querySelector("#nearMiss"), options);
    nearMiss.render();
</script>
