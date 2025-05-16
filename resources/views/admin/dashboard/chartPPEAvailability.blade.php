@if (empty($chartData['labels']) || empty($chartData['series']))
    <div class="border-0 pb-3" style="margin-top: 166px;">
        <h4 style="text-align: center;">No data Found.</h4>
    </div>
@else
    <div id="PPEAvailabilityChart"></div>

    <script>
        var chartLabels9 = @json($chartData['labels']);
        var chartSeries9 = @json($chartData['series']);
        var totalQuantity = chartSeries9.reduce((a, b) => a + b, 0);

        var options = {
            series: chartSeries9,
            chart: {
                type: 'donut',
                height: 350
            },
            labels: chartLabels9,
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            name: {
                                show: true,
                                fontSize: '16px'
                            },
                            value: {
                                show: true,
                                fontSize: '14px'
                            },
                            total: {
                                show: true,
                                label: 'Total',
                                fontSize: '18px',
                                formatter: function() {
                                    return totalQuantity;
                                }
                            }
                        }
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + ' Units';
                    }
                }
            },
            legend: {
                position: 'bottom'
            }
        };

        var PPEAvailabilityChart = new ApexCharts(document.querySelector("#PPEAvailabilityChart"), options);
        PPEAvailabilityChart.render();


        // Download button
        $("#LoadPPEAvailabilityChart_download").off("click").on("click", function() {
            PPEAvailabilityChart.dataURI().then(({
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

                    // Header
                    ctx.fillStyle = '#203669';
                    ctx.font = '20px Arial';
                    ctx.fillText('PPE Availability Chart', 10, 30);

                    // Filters (if present)
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

                    // Chart
                    ctx.drawImage(image, 0, headerHeight);

                    newCanvas.toBlob(function(blob) {
                        var link = document.createElement('a');
                        link.href = URL.createObjectURL(blob);
                        link.download = 'PPE_Availability_Chart.png';
                        link.click();
                    });
                };

                image.src = imgURI;
            });
        });
    </script>
@endif
