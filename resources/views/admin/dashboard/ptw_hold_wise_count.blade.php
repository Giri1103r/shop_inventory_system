<div id="LoadPtwHoldViolation_Count"></div>

<script>
var hold_count = @json($hold_count);

    var options = {
        series: [{
            name: 'Hold Count',
            data: Object.values(hold_count),
        }],
        chart: {
            height: 350,
            type: 'bar',
            toolbar: { show: false }
        },
        plotOptions: {
            bar: {
                borderRadius: 10,
                dataLabels: { position: 'top' }
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val;
            },
            offsetY: -20,
            style: {
                fontSize: '12px',
                colors: ["#304758"]
            }
        },
        xaxis: {
            categories: Object.keys(hold_count),
            position: 'top',
            tooltip: {
                enabled: false
            },
            axisBorder: { show: false },
            axisTicks: { show: false },
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
        },
        yaxis: {
            labels: {
                show: true
            }
        },
        tooltip: {
            enabled: true,
            y: {
                formatter: function (val, { series, seriesIndex, dataPointIndex, w }) {
                    const unit = w.globals.labels[dataPointIndex];
                    return `${unit}: ${val} Holds`;
                }
            }
        },
        title: {
            text: 'PTW Hold Violation Compliance',
            floating: true,
            offsetY: 330,
            align: 'center',
            style: { color: '#444' }
        }
    };

    var ptwChart = new ApexCharts(document.querySelector("#LoadPtwHoldViolation_Count"), options);
    ptwChart.render();

    // Download button functionality
    $("#LoadPtwHoldViolation_download").off("click").on("click", function() {
        ptwChart.dataURI().then(({
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
                ctx.fillText('PTW Hold Violation', 10, 30);

                // Optional filter text
                let yPos = 60;

                @if (isset($dates))
                    @php
                        $from = $dates['from_date'] ?? null;
                        $to = $dates['to_date'] ?? null;
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
                    link.download = 'PTW Hold Violation.png';
                    link.click();
                });
            };

            image.src = imgURI;
        });
    });
</script>
