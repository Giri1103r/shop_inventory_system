<div id="uaucStaticReport"></div>

<script>
    var options = {
        series: {!! json_encode($series) !!},
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                borderRadius: 5,
                borderRadiusApplication: 'end'
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: {!! json_encode($units) !!}
        },
       
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val + " incidents"
                }
            }
        }
    };
    var uaucStaticReport = new ApexCharts(document.querySelector("#uaucStaticReport"), options);
    uaucStaticReport.render();

    // Download Button
    $("#uaucstaticreport_download").off("click").on("click", function() {
        uaucStaticReport.dataURI().then(({
            imgURI
        }) => {
            var canvas = document.createElement('canvas');
            var ctx = canvas.getContext('2d');
            var image = new Image();

            image.onload = function() {
                canvas.width = image.width;
                let headerHeight = 120;
                canvas.height = image.height + headerHeight;

                ctx.fillStyle = 'white';
                ctx.fillRect(0, 0, canvas.width, canvas.height);

                ctx.fillStyle = '#203669';
                ctx.font = '20px Arial';
                ctx.fillText('UAUC Static Report', 10, 30);

                let yPos = 60;
                @if ($request->Fromdate || $request->Todate)
                    ctx.font = '16px Arial';
                    ctx.fillText('Filtered By:', 10, yPos);
                    yPos += 30;
                    @if ($request->Fromdate)
                        ctx.fillText('From Date: {{ $request->Fromdate }}', 10, yPos);
                        yPos += 30;
                    @endif
                    @if ($request->Todate)
                        ctx.fillText('To Date: {{ $request->Todate }}', 10, yPos);
                        yPos += 30;
                    @endif
                @endif

                ctx.drawImage(image, 0, headerHeight);

                canvas.toBlob(function(blob) {
                    var link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = 'uauc_combined_static_report.png';
                    link.click();
                });
            };

            image.src = imgURI;
        });
    });
</script>
