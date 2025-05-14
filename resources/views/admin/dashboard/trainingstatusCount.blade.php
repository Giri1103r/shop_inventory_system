<div id="trainingStatusPieChart"></div>

<script>
    // Ensure chartData is correctly passed from the server
    var chartData = @json($chartDataArray);

    // Convert string values to numbers for the series data
    var series = [
        Number(chartData['Pending']),
        Number(chartData['Rejected']),
        Number(chartData['In Progress']),
        Number(chartData['Completed'])
    ];

    // Define the chart options
    var chartOptions = {
        series: series,
        chart: {
            width: 400,
            type: 'pie',
        },
        labels: ['Pending', 'Rejected', 'In Progress', 'Completed'],
        colors: ['#f39c12', '#e74c3c', '#3498db', '#2ecc71'],
        dataLabels: {
            enabled: true,
            formatter: function(val, opts) {
                return opts.w.config.series[opts.seriesIndex];
            },
            style: {
                fontSize: '14px',
                fontWeight: 'bold',
                colors: ['#333']
            },
            dropShadow: {
                enabled: false
            }
        },
        legend: {
            position: 'bottom', // Position the legend on the right side
            horizontalAlign: 'center',
            labels: {
                useSeriesColors: true
            },
            formatter: function(seriesName, opts) {
                // Append the actual count to the legend label
                return seriesName + ": " + opts.w.globals.series[opts.seriesIndex];
            }
        }
    };

    var trainingStatusPieChart = new ApexCharts(document.querySelector("#trainingStatusPieChart"), chartOptions);
    trainingStatusPieChart.render();

    // Download chart logic
    document.getElementById("training_count_download").addEventListener("click", function() {
        trainingStatusPieChart.dataURI().then(({
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
                var headerText = 'TRAINING STATUS COUNT';
                ctx.fillText(headerText, 10, 30);

                var Fromdate = @json($getdashdata->Fromdate ?? null);
                var Todate = @json($getdashdata->Todate ?? null);

                var yPos = 60;

                if (Fromdate || Todate) {
                    var subHeaderText = 'Filtered By:';
                    ctx.fillText(subHeaderText, 10, yPos);
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
                    link.download = 'TRAINING STATUS COUNT.png';
                    link.click();
                });
            };
            image.src = imgURI;

        });
    });
</script>
