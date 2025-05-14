<div id="inspection_wise_count"></div>


<script>
    var inspection_wise_count = @json($inspection_wise_count);

    var dynamicColors = [
        '#3B5998', '#26A69A', '#FFC300', '#6C3483', '#E74C3C', '#3498DB',
        '#1ABC9C', '#9B59B6', '#F39C12', '#2ECC71', '#E67E22', '#34495E'
    ];

    // Generate the color slice based on the number of bars
    var barCount = Object.keys(inspection_wise_count).length;
    var colors = dynamicColors.slice(0, barCount);

    var options = {
        series: [{
            name: 'Inspection Count',
            data: Object.values(inspection_wise_count)
        }],
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            bar: {
                borderRadius: 10,
                borderRadiusApplication: 'end',
                horizontal: false,
                distributed: true // Important for per-bar color
            }
        },
        colors: colors,
        dataLabels: {
            enabled: true,
            style: {
                colors: ['#000']
            }
        },
        xaxis: {
            categories: Object.keys(inspection_wise_count),
            labels: {
                rotate: -45
            }
        },
        tooltip: {
            y: {
                formatter: function(val, opts) {
                    const category = opts.w.globals.labels[opts.dataPointIndex];
                    return category + ': ' + val;
                }
            }
        }
    };



    var inspection_wise_count = new ApexCharts(document.querySelector("#inspection_wise_count"), options);
    inspection_wise_count.render();

    // Download button functionality
    $("#inspection_wise_count_download").off("click").on("click", function() {
        inspection_wise_count.dataURI().then(({
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
                var headerText = 'INSPECTION TYPE WISE COUNT';
                ctx.fillText(headerText, 10, 30);

                var Fromdate = @json($from_date ?? null);
                var Todate = @json($to_date ?? null);

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
                    link.download = 'Inspection Type Wise Count.png';
                    link.click();
                });
            };
            image.src = imgURI;
        });
    });
</script>
