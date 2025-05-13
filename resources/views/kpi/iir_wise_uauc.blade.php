<div id="iirWiseUAUC"></div>

<script>
    var chartData = {!! json_encode($chartData) !!};
    var categories = chartData.map(item => item.incident_type_name); // Moved outside options
    
    var options = {
        series: [{
                name: 'Total Incidents',
                data: chartData.map(item => item.total_incident)
            },
            {
                name: 'Unsafe Act',
                data: chartData.map(item => item.unsafe_act) 
            },
            {
                name: 'Unsafe Condition',
                data: chartData.map(item => item.unsafe_condition) 
            },
            {
                name: 'Natural Causes',
                data: chartData.map(item => item.natural_causes)
            }
        ],
        chart: {
            type: 'bar',
            height: 350,
            stacked: true,
            toolbar: {
                show: false
            },
            zoom: {
                enabled: false
            }
        },
        responsive: [{
            breakpoint: 480,
            options: {
                legend: {
                    position: 'bottom',
                    offsetX: -10,
                    offsetY: 0
                }
            }
        }],
        plotOptions: {
            bar: {
                horizontal: false,
                borderRadius: 10,
                borderRadiusApplication: 'end',
                borderRadiusWhenStacked: 'last',
                dataLabels: {
                    total: {
                        enabled: false 
                    }
                }
            }
        },
        xaxis: {
            type: 'category',
            categories: categories,
            labels: {
                rotate: -45,
                style: {
                    fontSize: '12px'
                }
            }
        },
        legend: {
            position: 'bottom',
            horizontalAlign: 'center',
            offsetY: 10
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
                formatter: function(value) {
                    return value + " incidents";
                }
            }
        }
    };

    var iirWiseUAUC = new ApexCharts(document.querySelector("#iirWiseUAUC"), options);
    iirWiseUAUC.render();

    $("#iirTypewiseUAUC_download").off("click").on("click", function() {
        iirWiseUAUC.dataURI().then(({ imgURI }) => {
            var newCanvas = document.createElement('canvas');
            var ctx = newCanvas.getContext('2d');
            var image = new Image();

            image.onload = function() {
                newCanvas.width = image.width;
                let headerHeight = 120;
                newCanvas.height = image.height + headerHeight;

                ctx.fillStyle = 'white';
                ctx.fillRect(0, 0, newCanvas.width, newCanvas.height);

                ctx.fillStyle = '#203669';
                ctx.font = '20px Arial';
                ctx.fillText('IIR Type Wise UAUC', 10, 30); // More descriptive title

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

                ctx.drawImage(image, 0, headerHeight);

                newCanvas.toBlob(function(blob) {
                    var link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = 'IIR Type Wise UAUC.png'; // Better filename
                    link.click();
                });
            };

            image.src = imgURI;
        });
    });
</script>