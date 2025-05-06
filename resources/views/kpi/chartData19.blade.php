<div id="chartData19"></div>

<script>
    var options = {
        series: [{
            name: 'Actual',
            data: [{
                    x: '2011',
                    y: 12,
                    goals: [{
                        name: 'Expected',
                        value: 14,
                        strokeWidth: 2,
                        strokeDashArray: 2,
                        strokeColor: '#775DD0'
                    }]
                },
                {
                    x: '2012',
                    y: 44,
                    goals: [{
                        name: 'Expected',
                        value: 54,
                        strokeWidth: 5,
                        strokeHeight: 10,
                        strokeColor: '#775DD0'
                    }]
                },
                {
                    x: '2013',
                    y: 54,
                    goals: [{
                        name: 'Expected',
                        value: 52,
                        strokeWidth: 10,
                        strokeHeight: 0,
                        strokeLineCap: 'round',
                        strokeColor: '#775DD0'
                    }]
                },
                {
                    x: '2014',
                    y: 66,
                    goals: [{
                        name: 'Expected',
                        value: 61,
                        strokeWidth: 10,
                        strokeHeight: 0,
                        strokeLineCap: 'round',
                        strokeColor: '#775DD0'
                    }]
                },
                {
                    x: '2015',
                    y: 81,
                    goals: [{
                        name: 'Expected',
                        value: 66,
                        strokeWidth: 10,
                        strokeHeight: 0,
                        strokeLineCap: 'round',
                        strokeColor: '#775DD0'
                    }]
                },
                {
                    x: '2016',
                    y: 67,
                    goals: [{
                        name: 'Expected',
                        value: 70,
                        strokeWidth: 5,
                        strokeHeight: 10,
                        strokeColor: '#775DD0'
                    }]
                }
            ]
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
                horizontal: true,
            }
        },
        colors: ['#00E396'],
        dataLabels: {
            formatter: function(val, opt) {
                const goals =
                    opt.w.config.series[opt.seriesIndex].data[opt.dataPointIndex]
                    .goals

                if (goals && goals.length) {
                    return `${val} / ${goals[0].value}`
                }
                return val
            }
        },
        legend: {
            show: true,
            showForSingleSeries: true,
            customLegendItems: ['Actual', 'Expected'],
            markers: {
                fillColors: ['#00E396', '#775DD0']
            }
        }
    };

    var chartData19 = new ApexCharts(document.querySelector("#chartData19"), options);
    chartData19.render();

    // Download button functionality
    $("#LoadChart19_download").off("click").on("click", function() {
        chartData19.dataURI().then(({
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
                ctx.fillText('CHART', 10, 30);

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
                    link.download = 'CHART.png';
                    link.click();
                });
            };

            image.src = imgURI;
        });
    });
</script>
