@php
    $units = collect($formattedData)
        ->flatMap(function ($types) {
            return array_keys($types);
        })
        ->unique()
        ->values()
        ->all();
 
    $series = [];
    foreach ($formattedData as $incidentType => $unitData) {
        $data = [];
        foreach ($units as $unit) {
            $data[] = $unitData[$unit] ?? 0;
        }
        $series[] = [
            'name' => $incidentType,
            'data' => $data,
        ];
    }
@endphp
 
<div id="TotalIncidentsCount"></div>
 
<script>
    var options = {
        series: {!! json_encode($series) !!},
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
            categories: {!! json_encode($units) !!}
        },
        yaxis: {
            title: {
                text: 'Incident Count'
            }
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val + " incidents"
                }
            }
        }
    };
    var TotalIncidentsCount = new ApexCharts(document.querySelector("#TotalIncidentsCount"), options);
    TotalIncidentsCount.render();
 
    // Download button functionality
    $("#total_incidents_download").off("click").on("click", function() {
        TotalIncidentsCount.dataURI().then(({
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
                ctx.fillText('Total Incidents (YTD)', 10, 30);
 
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
                    link.download = 'Total Incidents (YTD).png';
                    link.click();
                });
            };
 
            image.src = imgURI;
        });
    });
</script>