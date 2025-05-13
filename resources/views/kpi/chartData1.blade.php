<div id="chartData1"></div>


<script>
    var options = {
        series: [{
            data: [400, 430, 448, 470, 540, 580, 690, 1100, 1200, 1380]
        }],
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: false
            },
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                borderRadiusApplication: 'end',
                horizontal: true,
            }
        },
        dataLabels: {
            enabled: true
        },
        xaxis: {
            categories: ['South Korea', 'Canada', 'United Kingdom', 'Netherlands', 'Italy', 'France', 'Japan',
                'United States', 'China', 'Germany'
            ],
        }
    };

    var chartData1 = new ApexCharts(document.querySelector("#chartData1"), options);
    chartData1.render();

    // Download button functionality
    $("#LoadChart1_download").off("click").on("click", function() {
        chartData1.dataURI().then(({
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
                var headerText = 'DEPARTMENT WISE TRAINING COUNT';
                ctx.fillText(headerText, 10, 30);

                // var factoryNames = '';
                // @if ($getdashdata->Factory && is_array($getdashdata->Factory) && isset($getdashdata->Factory))
                //     factoryNames = @json(getFactoryNames(arrayDecrypt($getdashdata->Factory)));
                // @endif

                var Fromdate = @json($getdashdata->Fromdate ?? null);
                var Todate = @json($getdashdata->Todate ?? null);

                var yPos = 60;

                if (Fromdate || Todate) {
                    var subHeaderText = 'Filtered By:';
                    ctx.fillText(subHeaderText, 10, yPos);

                    // if (factoryNames) {
                    //     yPos += 50;
                    //     ctx.fillText('Factory: ' + factoryNames, 10, yPos);
                    // }

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
                    link.download = 'CHART.png';
                    link.click();
                });
            };
            image.src = imgURI;
        });
    });
</script>