<div id="nearMiss"></div>

<script>
    var nearMissData = @json($chartData);
    // console.log(nearMissData);
    var monthNames = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];

    var monthData = Array(12).fill(0);
    nearMissData.forEach(item => {
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

            events: {
                dataPointSelection: function(event, chartContext, config) {
                    var dataPointIndex = config.dataPointIndex;
                    var selectedData = nearMissData.find(item => item.month === dataPointIndex + 1);
                    if (selectedData) {
                        var iirType = selectedData.iir_type;
                        var month = selectedData.month;
                        const url = "{{ admin_url('incident/initial-incident/list/all/type') }}";
                        redirectchartIMSurl('near_miss', iirType, url, month);
                    }
                }
            }
        },
        plotOptions: {
            bar: {
                columnWidth: '50%',
                dataLabels: {
                    position: 'top',
                },
                distributed: true // <-- enables different color per bar
            }
        },
        colors: [
            '#008FFB', '#00E396', '#FEB019', '#775DD0',
            '#3F51B5', '#546E7A', '#D4526E', '#8D5B4C',
            '#F86624', '#2E294E', '#1B998B', '#9C27B0'
        ],
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
            position: 'bottom',
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
            },

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
        // title: {
        //     text: 'Monthly Near Miss Incidents',
        //     floating: true,
        //     offsetY: 330,
        //     align: 'center',
        //     style: {
        //         color: '#444'
        //     }
        // }
    };

    var nearMiss = new ApexCharts(document.querySelector("#nearMiss"), options);
    nearMiss.render();

    $("#nearMiss_download").off("click").on("click", function() {
        iirWiseRcpa.dataURI().then(({
            imgURI
        }) => {
            var newCanvas = document.createElement('canvas');
            var ctx = newCanvas.getContext('2d');
            var image = new Image();

            image.onload = function() {
                newCanvas.width = image.width;
                let headerHeight = 120;
                newCanvas.height = image.height + headerHeight;

                // White background
                ctx.fillStyle = 'white';
                ctx.fillRect(0, 0, newCanvas.width, newCanvas.height);

                // Header text
                ctx.fillStyle = '#203669';
                ctx.font = '20px Arial';
                ctx.fillText('Near Miss Frequency Rate', 10, 30);

                // Optional filter text
                let yPos = 60;

                @if (isset($getdashdata))
                    @php
                        $from = $getdashdata->Fromdate ?? null;
                        $to = $getdashdata->Todate ?? null;
                    @endphp

                    @if ($from || $to)
                        ctx.fillStyle = '#203669';
                        ctx.font = '16px Arial';
                        ctx.fillText('Filtered By:', 10, yPos);
                        yPos += 30;

                        @if ($from)
                            ctx.fillText('From Date: {{ $from }}', 10, yPos);
                            yPos += 30;
                        @endif

                        @if ($to)
                            ctx.fillText('To Date: {{ $to }}', 10, yPos);
                            yPos += 30;
                        @endif
                    @endif
                @endif

                // Draw chart image below header
                ctx.drawImage(image, 0, headerHeight);

                // Save as image
                newCanvas.toBlob(function(blob) {
                    var link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = 'Near Miss Frequency Rate.png';
                    link.click();
                });
            };

            image.src = imgURI;
        });
    });
</script>
