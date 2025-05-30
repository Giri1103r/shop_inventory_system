<div id="monthwisetraining"></div>

<script>
    var chartData = @json($chartDataArray); // Pass data from the controller

    // Populate the chart series with counts for each training status
    var seriesData = [{
            name: 'Pending',
            data: [
                chartData['January']['pending'], chartData['February']['pending'], chartData['March'][
                'pending'],
                chartData['April']['pending'], chartData['May']['pending'], chartData['June']['pending'],
                chartData['July']['pending'], chartData['August']['pending'], chartData['September']['pending'],
                chartData['October']['pending'], chartData['November']['pending'], chartData['December'][
                    'pending'
                ]
            ]
        },
        {
            name: 'Rejected',
            data: [
                chartData['January']['rejected'], chartData['February']['rejected'], chartData['March'][
                    'rejected'
                ],
                chartData['April']['rejected'], chartData['May']['rejected'], chartData['June']['rejected'],
                chartData['July']['rejected'], chartData['August']['rejected'], chartData['September'][
                    'rejected'
                ],
                chartData['October']['rejected'], chartData['November']['rejected'], chartData['December'][
                    'rejected'
                ]
            ]
        },
        {
            name: 'In Progress',
            data: [
                chartData['January']['in_progress'], chartData['February']['in_progress'], chartData['March'][
                    'in_progress'
                ],
                chartData['April']['in_progress'], chartData['May']['in_progress'], chartData['June'][
                    'in_progress'
                ],
                chartData['July']['in_progress'], chartData['August']['in_progress'], chartData['September'][
                    'in_progress'
                ],
                chartData['October']['in_progress'], chartData['November']['in_progress'], chartData['December']
                ['in_progress']
            ]
        },
        {
            name: 'Completed',
            data: [
                chartData['January']['completed'], chartData['February']['completed'], chartData['March'][
                    'completed'
                ],
                chartData['April']['completed'], chartData['May']['completed'], chartData['June']['completed'],
                chartData['July']['completed'], chartData['August']['completed'], chartData['September'][
                    'completed'
                ],
                chartData['October']['completed'], chartData['November']['completed'], chartData['December'][
                    'completed'
                ]
            ]
        }
    ];

    // Chart options
    var options = {
        series: seriesData,
        chart: {
            type: 'bar',
            height: 450,
            stacked: false,
            toolbar: {
                show: false
            },
            zoom: {
                enabled: true
            },
            events: {
                dataPointSelection: function(event, chartContext, config) {
                    var selectedMonthIndex = config.dataPointIndex +
                    1; // Get selected month index (1 for Jan, 2 for Feb, etc.)
                    redirectTotrainingList('', '', '', '', '', selectedMonthIndex);
                }
            }
        },
        responsive: [{
            breakpoint: 480,
            options: {
                legend: {
                    position: 'bottom',
                    offsetX: -10,
                    offsetY: 0
                }
            }
        }],
        colors: ["#FFA500", "#FF4C4C", "#80C4E9", "#06D001"],
        plotOptions: {
            bar: {
                horizontal: false,
               columnWidth: '100%',
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
        xaxis: {
            categories: ["January", "February", "March", "April", "May", "June", "July", "August", "September",
                "October", "November", "December"
            ]
        },
        fill: {
            opacity: 1
        }
    };

    var monthwisetraining = new ApexCharts(document.querySelector("#monthwisetraining"), options);
    monthwisetraining.render();

    // Download chart logic
    document.getElementById("monthwisetraining_download").addEventListener("click", function() {
        monthwisetraining.dataURI().then(({
            imgURI
        }) => {
            var canvas = document.createElement('canvas');
            var ctx = canvas.getContext('2d');
            var image = new Image();

            image.onload = function() {
                canvas.width = image.width;
                canvas.height = image.height + 100; // Extra space for header

                // Add custom header
                ctx.fillStyle = 'white';
                ctx.fillRect(0, 0, canvas.width, 100);
                ctx.fillStyle = '#203669';
                ctx.font = '20px Arial';
                ctx.fillText('MONTH WISE TRAINING COUNT', 10, 30);

                // Add date filters if available
                var Fromdate = @json($getdashdata->Fromdate ?? null);
                var Todate = @json($getdashdata->Todate ?? null);

                if (Fromdate || Todate) {
                    ctx.fillStyle = 'black';
                    ctx.font = '16px Arial';
                    var yPos = 60;
                    if (Fromdate) {
                        ctx.fillText('From Date: ' + Fromdate, 10, yPos);
                        yPos += 20;
                    }
                    if (Todate) {
                        ctx.fillText('To Date: ' + Todate, 10, yPos);
                    }
                }

                // Draw the chart image
                ctx.drawImage(image, 0, 100);

                // Export the chart image
                canvas.toBlob(function(blob) {
                    var link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = 'MONTH WISE TRAINING COUNT.png'; // File name
                    link.click();
                });
            };
            image.src = imgURI;
        });
    });
</script>
