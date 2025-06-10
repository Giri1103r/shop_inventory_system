<div id="dailyObservation"></div>


<script>
    var chartData = @json($chartData);
    var options = {
        series: chartData.series, // Using the series data from the backend
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: false
            },
            events: {
                dataPointSelection: function(event, chartContext, config) {
                    var dataPointIndex = config.dataPointIndex;

                    if (dataPointIndex === undefined) {
                        console.error('Invalid dataPointIndex');
                        return;
                    }

                    var unitName = chartContext.w.config.xaxis.categories[dataPointIndex]?.trim();


                    var unitId = unitName;

                    if (unitId) {
                        redirectToGembaWalk(unitId);
                    } else {
                        console.warn('Unit ID not found for:', unitName);
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
            categories: chartData.categories,
        },
        yaxis: {
            title: {
                text: 'Gemba Walk'
            }
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
        }
    };


    // Initialize and render the ApexCharts instance
    var dailyObservation = new ApexCharts(document.querySelector("#dailyObservation"), options);
    dailyObservation.render();

    // Download button functionality
    $("#gembaWalkDownload").off("click").on("click", function() {
        dailyObservation.dataURI().then(({
            imgURI
        }) => {
            var newCanvas = document.createElement('canvas');
            var ctx = newCanvas.getContext('2d');
            var image = new Image();

            image.onload = function() {
                newCanvas.width = image.width;
                let headerHeight = 120;
                newCanvas.height = image.height + headerHeight;

                // White background for the canvas
                ctx.fillStyle = 'white';
                ctx.fillRect(0, 0, newCanvas.width, newCanvas.height);

                // Add a header text
                ctx.fillStyle = '#203669';
                ctx.font = '20px Arial';
                ctx.fillText('Gemba Walk', 10, 30);

                // Optional filter text (if available)
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

                // Draw the chart image below the header
                ctx.drawImage(image, 0, headerHeight);

                // Save the canvas as an image
                newCanvas.toBlob(function(blob) {
                    var link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = 'Gemba Walk.png';
                    link.click();
                });
            };

            image.src = imgURI;
        });
    });
</script>
