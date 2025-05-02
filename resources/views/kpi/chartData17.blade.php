<div id="chartData17"></div>

<script>
    var options = {
        series: [{
            data: [{
                    x: 'New Delhi',
                    y: 218
                },
                {
                    x: 'Kolkata',
                    y: 149
                },
                {
                    x: 'Mumbai',
                    y: 184
                },
                {
                    x: 'Ahmedabad',
                    y: 55
                },
                {
                    x: 'Bangaluru',
                    y: 84
                },
                {
                    x: 'Pune',
                    y: 31
                },
                {
                    x: 'Chennai',
                    y: 70
                },
                {
                    x: 'Jaipur',
                    y: 30
                },
                {
                    x: 'Surat',
                    y: 44
                },
                {
                    x: 'Hyderabad',
                    y: 68
                },
                {
                    x: 'Lucknow',
                    y: 28
                },
                {
                    x: 'Indore',
                    y: 19
                },
                {
                    x: 'Kanpur',
                    y: 29
                }
            ]
        }],
        legend: {
            show: false
        },
        chart: {
            height: 350,
            toolbar: {
                show: false 
            },
            type: 'treemap'
        },
        title: {
            text: 'Distibuted Treemap (different color for each cell)',
            align: 'center'
        },
        colors: [
            '#3B93A5',
            '#F7B844',
            '#ADD8C7',
            '#EC3C65',
            '#CDD7B6',
            '#C1F666',
            '#D43F97',
            '#1E5D8C',
            '#421243',
            '#7F94B0',
            '#EF6537',
            '#C0ADDB'
        ],
        plotOptions: {
            treemap: {
                distributed: true,
                enableShades: false
            }
        }
    };
    var chartData17 = new ApexCharts(document.querySelector("#chartData17"), options);
    chartData17.render();

    // Download button functionality
    $("#LoadChart17_download").off("click").on("click", function() {
        chartData17.dataURI().then(({
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
                ctx.fillText('CHART', 10, 30);

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
                    link.download = 'CHART.png';
                    link.click();
                });
            };

            image.src = imgURI;
        });
    });
</script>
