<div id="ptw_open_close"></div>

<script>
    var open_close = @json($active_close_count);
    var options = {
        series: [{
            data: Object.values(open_close)
        }],
        chart: {
            width: 380,
            type: 'pie',
          
        },
        labels: Object.keys(open_close),
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

    var ptw_open_close = new ApexCharts(document.querySelector("#ptw_open_close"), options);
    ptw_open_close.render();

    // Download chart as image
    $("#ptw_open_close_download").off("click").on("click", function() {
        ptw_open_close.dataURI().then(({
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
