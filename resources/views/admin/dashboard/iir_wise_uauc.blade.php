<div id="iirWiseUAUC"></div>

<script>
    var chartData = {!! json_encode($chartData) !!};


    var series = [{
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
        },
    ];


    var categories = chartData.map(item => item.incident_type_name);

    var options = {
        series: series,
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: false
            },
       
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                borderRadius: 5,
                borderRadiusApplication: 'end'
            },
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
                    return val
                }
            }
        },
        colors: ['#008FFB', '#00E396', '#FEB019', '#775DD0'],
        legend: {
            position: 'bottom'
        }
    };

    var iirWiseUAUC = new ApexCharts(document.querySelector("#iirWiseUAUC"), options);
    iirWiseUAUC.render();

    $("#iirTypewiseUAUC_download").off("click").on("click", function() {
        iirWiseUAUC.dataURI().then(({
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
                ctx.fillText('IIR Type Wise UAUC', 10, 30);

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
                    link.download = 'IIR Type Wise UAUC.png';
                    link.click();
                });
            };

            image.src = imgURI;
        });
    });
</script>
