<div id="iirWiseRcpa"></div>

<script>
    var iirChartData = @json($chartData);
    var series = [{
            name: 'Total Incidents',
            data: iirChartData.map(item => item.total_incident)
        },
        {
            name: 'Total RCPA',
            data: iirChartData.map(item => item.total_rcpa)
        }
    ];

    var categories = iirChartData.map(item => item.incident_type_name);

    var options = {
        series: series,
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: false
            },
            events: {
                dataPointSelection: function(event, chartContext, config) {
                    var seriesIndex = config.seriesIndex;

                    var dataPointIndex = config.dataPointIndex;

                    var incidentTypeName = chartContext.w.config.xaxis.categories[dataPointIndex];
                    var selectedItem = iirChartData.find(item => item.incident_type_name === incidentTypeName);

                    var iirType = selectedItem.iir_type;

                    if (seriesIndex === 0) {
                        redirectToIms(iirType, '', '', '', '', '', );
                    } else if (seriesIndex === 1) {
                          
                        redirectToImsRCPA(iirType, '', '', '', '', '', );
                    }
                }
            }

        },

        plotOptions: {
            bar: {
                horizontal: false,
              columnWidth: '25%',

                borderRadiusApplication: 'end'
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: categories,
            labels: {
                rotate: -45,
                style: {
                    fontSize: '12px'
                }
            }
        },
        yaxis: {
            title: {
                text: 'Count'
            },
            min: 0
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val;
                }
            }
        },
        colors: ['#008FFB', '#00E396'],
        legend: {
            position: 'bottom'
        }
    };

    var iirWiseRcpa = new ApexCharts(document.querySelector("#iirWiseRcpa"), options);
    iirWiseRcpa.render();


    $("#iirTypewiseRCPA_download").off("click").on("click", function() {
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
                ctx.fillText('IIR Type wise RCPA', 10, 30);

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
                    link.download = 'IIR Type wise RCPA.png';
                    link.click();
                });
            };

            image.src = imgURI;
        });
    });
</script>
