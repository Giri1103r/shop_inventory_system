@if (empty($totalCount) || array_sum(array_column($work_wise_count, 'count')) === 0)
    <div class="border-0 pb-3" style="margin-top: 166px;">
        <h4 style="text-align: center;">No data Found.</h4>
    </div>
@else
    <div id="ptw_type_wise"></div>
    <script>
        var type_wise = @json($work_wise_count);
        type_wise = Object.values(type_wise);
        var options = {
            series: [{
                name: 'Work Count',
                data: type_wise.map(item => item.count)
            }],
            chart: {
                height: 350,
                type: 'bar',
                toolbar: {
                    show: false
                },
                events: {
                    dataPointSelection: function(event, chartContext, config) {
                        var selectedItem = type_wise[config.dataPointIndex];

                        var typeOfWork = selectedItem.id;
                        redirectToPTW('', '', '', '', typeOfWork);
                    }
                }
            },
            plotOptions: {
                bar: {
                    columnWidth: '25%',
                    horizontal: false,
                    distributed: true
                }
            },
            xaxis: {
                categories: type_wise.map(item => item.name),
                position: 'bottom',
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                },
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
                tooltip: {
                    enabled: false,
                }
            },
            yaxis: {
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false,
                },
                labels: {
                    show: true,
                    formatter: function(val) {
                        return val;
                    }
                }
            },
            grid: {
                padding: {
                    bottom: 60
                }
            },
            tooltip: {
                y: {
                    formatter: function(val, opts) {
                        const category = opts.w.globals.labels[opts.dataPointIndex];
                        return category + ": " + val;
                    }
                }
            }
        };

        var ptw_type_wise = new ApexCharts(document.querySelector("#ptw_type_wise"), options);
        ptw_type_wise.render();

        $("#ptw_type_wise_download").off("click").on("click", function() {
            ptw_type_wise.dataURI().then(({
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
                    ctx.fillText('PTW Type Wise', 10, 30);

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
                        link.download = 'PTW Type Wise.png';
                        link.click();
                    });
                };

                image.src = imgURI;
            });
        });
    </script>
@endif
