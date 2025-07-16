
<style>
    #IncidentTypeChart {
        width: 100%;
        height: 350px;
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>
<div id="IncidentTypeChart"></div>

@php
    $labels = array_keys($formattedData);
    $data = array_values($formattedData);
@endphp

<script>

    var incidentTypeIdMap = {!! json_encode($typeIdMap) !!};
    var data = {!! json_encode($data) !!};

    var options = {
        series: data,
        chart: {
            type: 'donut',
            width: 380,
            toolbar: {
                show: false
            },
            events: {
                dataPointSelection: function(event, chartContext, config) {
                    var dataPointIndex = config.dataPointIndex;
                    var incidentTypeName = chartContext.w.config.labels[dataPointIndex];
                    var iirType = incidentTypeIdMap[incidentTypeName];
                    if (iirType) {
                        redirectToIms(iirType,'','','','','','');
                    }
                }
            }
        },
        labels: {!! json_encode($labels) !!},
        legend: {
            position: 'bottom'
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom',
                    offsetX: 0,
                    offsetY: 0
                }
            }
        }]
    };

    // Create and render the chart
    var IncidentTypeChart = new ApexCharts(document.querySelector("#IncidentTypeChart"), options);
    IncidentTypeChart.render();

    // Download button functionality (optional)
    $("#IncidentType_download").off("click").on("click", function() {
        IncidentTypeChart.dataURI().then(({
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
                ctx.fillText('Incident Type', 10, 30);

                // Optional filter text (date filters)
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
                    link.download = 'Incident Type.png';
                    link.click();
                });
            };

            image.src = imgURI;
        });
    });
</script>
