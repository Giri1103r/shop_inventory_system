<div id="chartData7"></div>

<script>
    var options = {
        series: [44, 55, 13, 43, 22],
        chart: {
            width: 380,
            type: 'pie',
          
        },
        labels: ['Team A', 'Team B', 'Team C', 'Team D', 'Team E'],
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
                    position: 'bottom'
                }
            }
        }]
    };

    var chartData7 = new ApexCharts(document.querySelector("#chartData7"), options);
    chartData7.render();

    // Download chart as image
    $("#LoadChart7_download").off("click").on("click", function() {
        chartData7.dataURI().then(({
            imgURI
        }) => {
            var newCanvas = document.createElement('canvas');
            var ctx = newCanvas.getContext('2d');
            var image = new Image();

            image.onload = function() {
                newCanvas.width = image.width;
                let headerHeight = 120;
                newCanvas.height = image.height + headerHeight;

                // Background
                ctx.fillStyle = 'white';
                ctx.fillRect(0, 0, newCanvas.width, newCanvas.height);

                // Header
                ctx.fillStyle = '#203669';
                ctx.font = '20px Arial';
                ctx.fillText('CHART', 10, 30);

                // Optional filter info
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

                // Draw image
                ctx.drawImage(image, 0, headerHeight);

                // Download
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
