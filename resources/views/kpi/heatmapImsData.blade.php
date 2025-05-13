<div id="heatmapofImsData"></div>
<div id="customLegend" style="margin-top: 10px;"></div>

<script>
    var chartData = @json($formattedData);

    // Map injury types to colors
    var injuryColors = {
        'Major': '#FF0000', // Red
        'Minor': '#FFB200', // Orange
        'Fatal': '#128FD9'  // Blue
    };

    // Get colors for each series
    var seriesColors = chartData.map(series => injuryColors[series.name] || '#999');

    var options = {
        series: chartData,
        chart: {
            height: 400,
            type: 'heatmap',
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

    var chart = new ApexCharts(document.querySelector("#heatmapofImsData"), options);
    chart.render();

    // Manual custom legend
    var customLegendHTML = '<div style="display: flex; gap: 15px; justify-content: center;">';
    for (const [label, color] of Object.entries(injuryColors)) {
        customLegendHTML += `<div style="display: flex; align-items: center; gap: 5px;">
            <div style="width: 12px; height: 12px; background-color: ${color}; border-radius: 2px;"></div>
            <span>${label}</span>
        </div>`;
    }
    customLegendHTML += '</div>';
    document.getElementById('customLegend').innerHTML = customLegendHTML;

    // Download button functionality
    $("#heatmapofImsData_download").off("click").on("click", function() {
        heatmapofImsData.dataURI().then(({
            imgURI
        }) => {
            var newCanvas = document.createElement('canvas');
            var ctx = newCanvas.getContext('2d');
            var image = new Image();

            image.onload = function() {
                newCanvas.width = image.width;
                newCanvas.height = image.height + 250;

                ctx.fillStyle = 'white';
                ctx.fillRect(0, 0, newCanvas.width, 250);
                ctx.fillStyle = '#203669';
                ctx.font = '20px Arial';
                var headerText = 'DEPARTMENT WISE TRAINING COUNT';
                ctx.fillText(headerText, 10, 30);

                // var factoryNames = '';
                // @if ($getdashdata->Factory && is_array($getdashdata->Factory) && isset($getdashdata->Factory))
                //     factoryNames = @json(getFactoryNames(arrayDecrypt($getdashdata->Factory)));
                // @endif

                var Fromdate = @json($getdashdata->Fromdate ?? null);
                var Todate = @json($getdashdata->Todate ?? null);

                var yPos = 60;

                if (Fromdate || Todate) {
                    var subHeaderText = 'Filtered By:';
                    ctx.fillText(subHeaderText, 10, yPos);

                    // if (factoryNames) {
                    //     yPos += 50;
                    //     ctx.fillText('Factory: ' + factoryNames, 10, yPos);
                    // }

                    if (Fromdate) {
                        yPos += 30;
                        ctx.fillText('From Date: ' + Fromdate, 10, yPos);
                    }
                    if (Todate) {
                        yPos += 30;
                        ctx.fillText('To Date: ' + Todate, 10, yPos);
                    }
                }

                ctx.drawImage(image, 0, yPos);

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
