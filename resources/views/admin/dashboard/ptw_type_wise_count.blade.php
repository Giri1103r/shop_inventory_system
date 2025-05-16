@if (empty($work_wise_count) || array_sum($work_wise_count) === 0)
    <div class="border-0 pb-3" style="margin-top: 166px;">
        <h4 style="text-align: center;">No data Found.</h4>
    </div>
@else
    <div id="ptw_type_wise"></div>
    <script>
        var type_wise = @json($work_wise_count);


        // var dynamicColors = [
        //     '#3B5998', '#26A69A', '#FFC300', '#6C3483', '#E74C3C', '#3498DB',
        //     '#1ABC9C', '#9B59B6', '#F39C12', '#2ECC71', '#E67E22', '#34495E'
        // ];

        // // Ensure colors match the number of bars
        // var barCount = seriesData.length;
        // var colors = dynamicColors.slice(0, barCount);
        var options = {
            series: [{
                name: 'Work Count',
                data: Object.values(type_wise)
            }],
            chart: {
                height: 350,
                type: 'bar',
                toolbar: {
                    show: false
                },
            },
            plotOptions: {
                bar: {
                    borderRadius: 10,
                    horizontal: false,
                    distributed: true // ✅ Enables individual bar colors
                }
            },
            // colors: colors,
            xaxis: {
                categories: Object.keys(type_wise),
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
                    show: false,
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

        // Download button functionality
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
