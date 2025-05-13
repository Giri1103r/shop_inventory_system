<div id="heatmapofImsData"></div>
<div id="customLegend" style="margin-top: 10px;"></div>

<script>
    var chartData = @json($formattedData);

    // Map injury types to colors
    var injuryColors = {
        'Major': '#FF0000', // Red
        'Minor': '#FFB200', // Orange
        'Fatal': '#128FD9' // Blue
    };

    // Get colors for each series
    var seriesColors = chartData.map(series => injuryColors[series.name] || '#999');

    var options = {
        series: chartData,
        chart: {
            height: 400,
            type: 'heatmap',
            toolbar: {
                show: false
            },
        },
        dataLabels: {
            enabled: true
        },
        colors: seriesColors, // Use mapped colors
        plotOptions: {
            heatmap: {
                shadeIntensity: 0.5,
                radius: 4,
                useFillColorAsStroke: true,
            }
        },

        xaxis: {
            type: 'category',
            title: {
                text: 'Month'
            }
        },
        yaxis: {
            title: {
                text: 'Nature of Injury'
            }
        },
        tooltip: {
            enabled: true,
            y: {
                formatter: function(val) {
                    return val + " Incidents";
                }
            }
        },
        legend: {
            show: false // Hide default legend
        }
    };

    var heatmapofImsData = new ApexCharts(document.querySelector("#heatmapofImsData"), options);
    heatmapofImsData.render();

    var customLegendHTML = '<div style="display: flex; gap: 15px; justify-content: center;">';
    for (const [label, color] of Object.entries(injuryColors)) {
        customLegendHTML += `<div style="display: flex; align-items: center; gap: 5px;">
            <div style="width: 12px; height: 12px; background-color: ${color}; border-radius: 2px;"></div>
            <span>${label}</span>
        </div>`;
    }
    customLegendHTML += '</div>';
    document.getElementById('customLegend').innerHTML = customLegendHTML;

    $("#heatmapofImsData_download").off("click").on("click", function() {
        heatmapofImsData.dataURI().then(({
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
                ctx.fillText('Heatmap of IMS Data', 10, 30);

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

                newCanvas.toBlob(function(blob) {
                    var link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = 'Heatmap of IMS Data.png';
                    link.click();
                });
            };

            image.src = imgURI;
        });
    });
</script>
