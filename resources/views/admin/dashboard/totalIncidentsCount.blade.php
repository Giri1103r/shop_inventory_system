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
    var units = @json($units);
    var lookup_array = @json($lookup);

    var options = {
        series: {!! json_encode($series) !!},
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: false
            },
            events: {
                dataPointSelection: function(event, chartContext, config) {
                    var seriesIndex = config.seriesIndex;
                    var dataPointIndex = config.dataPointIndex;

                    if (seriesIndex === undefined || dataPointIndex === undefined) {
                        console.error('Invalid seriesIndex or dataPointIndex');
                        return;
                    }

                    var incidentType = chartContext.w.config.series[seriesIndex]?.name?.trim();
                    console.log(incidentType);
                    var unit = chartContext.w.config.xaxis.categories[dataPointIndex]?.trim();

                    console.log(lookup_array);

                    console.log('Selected Incident Type:', incidentType);
                    console.log('Selected Unit:', unit);
                    console.log('Available lookup keys:', Object.keys(lookup));

                    var incidentTypeObj = lookup_array[incidentType];

                    if (incidentTypeObj) {
                        var unitObj = incidentTypeObj[unit];
                        if (unitObj) {
                            var iirType = unitObj.incident_type_id;
                            var unitId = unitObj.unit_id;
                            redirectToIms(iirType, unitId);
                        } else {
                            console.warn('Unit not found in incident type object:', unit);
                        }
                    } else {
                        console.warn('Incident type not found in lookup:', incidentType);
                    }
                }

            }

        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '50%',

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
