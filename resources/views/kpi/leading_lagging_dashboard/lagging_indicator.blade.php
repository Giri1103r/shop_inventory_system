 <div id="lagging_doughnut"></div>

<script>
    var chartData = {!! $chartData !!};

    var options = {
        chart: {
            height: 350,
            type: 'donut',
            dropShadow: {
                enabled: true,
                color: '#000',
                top: 18,
                left: 7,
                blur: 10,
                opacity: 0.2
            },
            toolbar: {
                show: false
            }
        },
        series: chartData.map(d => d.count),
        labels: chartData.map(d => d.name),
        colors: ['#77B6EA', '#545454', '#00E396', '#FEB019', '#FF4560', '#775DD0', '#3F51B5'],
        dataLabels: {
            enabled: true,
            style: {
                fontSize: '14px'
            }
        },
        legend: {
            position: 'bottom',
            horizontalAlign: 'center',
            offsetY: -18
        },
        title: {
            text: 'Lagging Indicator Distribution',
            align: 'center',
            style: {
                fontSize: '16px'
            }
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 300
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };
    var lagging_doughnut = new ApexCharts(document.querySelector("#lagging_doughnut"), options);
    lagging_doughnut.render();

    $("#lagging_doughnut_download").off("click").on("click", function() {
        lagging_line.dataURI().then(({
            imgURI
        }) => {
            var newCanvas = document.createElement('canvas');
            var ctx = newCanvas.getContext('2d');
            var image = new Image();

            image.onload = function() {
                newCanvas.width = image.width;
                let headerHeight = 120;
                newCanvas.height = image.height + headerHeight;


                ctx.fillStyle = 'white';
                ctx.fillRect(0, 0, newCanvas.width, newCanvas.height);


                ctx.fillStyle = '#203669';
                ctx.font = '20px Arial';
                ctx.fillText('CHART', 10, 30);


                let yPos = 60;

                var Fromdate = @json($from_date ?? null);
                var Todate = @json($to_date ?? null);

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


                ctx.drawImage(image, 0, headerHeight);


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