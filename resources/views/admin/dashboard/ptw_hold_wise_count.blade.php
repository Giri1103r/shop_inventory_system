<div id="LoadPtwHoldViolation_Count"></div>

<script>
    var hold_count = @json($hold_count);
    var permitStatus = "{{ $permitStatus }}";
    var dynamicColors = [
        '#3B5998', '#26A69A', '#FFC300', '#6C3483', '#E74C3C', '#3498DB',
        '#1ABC9C', '#9B59B6', '#F39C12', '#2ECC71', '#E67E22', '#34495E'
    ];

    var units = hold_count.map(item => item.unit_name);
    var values = hold_count.map(item => item.hold_count);
    var colors = dynamicColors.slice(0, units.length);

    var options = {
        series: [{
            name: 'Hold Count',
            data: values,
        }],
        chart: {
            height: 350,
            type: 'bar',
            toolbar: {
                show: false
            },
            events: {
                dataPointSelection: function(event, chartContext, config) {
                    var selectedItem = hold_count[config.dataPointIndex];
                    var unitId = selectedItem.unit_id;
                    redirectToPTW('', unitId, '', '', '', permitStatus)
                }
            }
        },
        plotOptions: {
            bar: {
                borderRadius: 10,
                horizontal: false,
                distributed: true
            }
        },
        colors: colors,
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
            categories: units,
            position: 'bottom',
            labels: {
                rotate: -45,
                style: {
                    fontSize: '12px'
                }
            },
            axisBorder: {
                show: true
            },
            axisTicks: {
                show: true
            },
            tooltip: {
                enabled: false
            }
        },
        yaxis: {
            labels: {
                show: true
            }
        },
        tooltip: {
            custom: function({
                series,
                seriesIndex,
                dataPointIndex,
                w
            }) {
                const unit = w.globals.labels[dataPointIndex];
                const val = series[seriesIndex][dataPointIndex];
                return `
                    <div style="padding:10px;">
                        <strong>Unit:</strong> ${unit}<br>
                        <strong>Hold Count:</strong> ${val}
                    </div>`;
            }
        },
        legend: {
            show: false
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
