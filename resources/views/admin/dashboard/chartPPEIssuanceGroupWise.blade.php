@if (empty($chartData['labels']) || empty($chartData['series']))
    <div class="border-0 pb-3" style="margin-top: 166px;">
        <h4 style="text-align: center;">No data Found.</h4>
    </div>
@else
    <div id="chartPPEIssuanceGroupWise"></div>

    <script>
        var seriesData = {!! json_encode($chartData['series']) !!};
        var dynamicColors = [
            '#3B5998', '#26A69A', '#FFC300', '#6C3483', '#E74C3C', '#3498DB',
            '#1ABC9C', '#9B59B6', '#F39C12', '#2ECC71', '#E67E22', '#34495E'
        ];

        // Ensure colors match the number of bars
        var barCount = seriesData.length;
        var colors = dynamicColors.slice(0, barCount);
        var options = {
            series: [{
                name: 'PPE Issuance (in Count)',
                data: {!! json_encode($chartData['series']) !!}
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: false
                },
                stacked: false,
                zoom: {
                    enabled: false
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    legend: {
                        position: 'bottom',
                        offsetX: 0,
                        offsetY: 0
                    }
                }
            }],
            plotOptions: {
                bar: {
                    horizontal: false,
                    distributed: true,
                    columnWidth: '50px',
                    dataLabels: {
                        total: {
                            enabled: true,
                            style: {
                                fontSize: '13px',
                                fontWeight: 900
                            }
                        }
                    }
                }
            },

            colors: colors,
            xaxis: {
                categories: {!! json_encode($chartData['labels']) !!}
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val;
                    }
                }
            },
            legend: {
                position: 'bottom',
                horizontalAlign: 'center',
                offsetY: 10
            },
            fill: {
                opacity: 1
            }
        };


        var chartPPEIssuanceGroupWise = new ApexCharts(document.querySelector("#chartPPEIssuanceGroupWise"), options);
        chartPPEIssuanceGroupWise.render();

        // Download button functionality
        $("#LoadPPEIssuanceGroupWise_download").off("click").on("click", function() {
            chartPPEIssuanceGroupWise.dataURI().then(({
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
                    ctx.fillText('PPEIssuanceGroupWiseChart', 10, 30);

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
                        link.download = 'PPEIssuanceGroupWiseChart.png';
                        link.click();
                    });
                };

                image.src = imgURI;
            });
        });
    </script>
@endif
