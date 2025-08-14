<div id="uaucStaticReport"></div>

<script>
   var unitdata = {!! json_encode($unitdata) !!};

    var options = {
        series: [
            {
                name: 'Total',
                data: {!! json_encode($total) !!}
            },
            {
                name: 'Open',
                data: {!! json_encode($open) !!}
            },
            {
                name: 'Closed',
                data: {!! json_encode($closed) !!}
            }
        ],
        chart: {
            type: 'bar',
            height: 350,
            stacked: true,
            toolbar: {
                show: false
            },
            events: {
                dataPointSelection: function(event, chartContext, config) {
                    var dataPointIndex = config.dataPointIndex;
                    var seriesIndex = config.seriesIndex;
                    var unitId = unitdata[dataPointIndex]; // ✅ Get unit ID
                    var incidentTypeName = chartContext.w.config.xaxis.categories[dataPointIndex];

                    const url = "{{ admin_url('incident/initial-incident/list/all/type') }}";

                    let iirType;
                    let iirOpenClose;

                    // ✅ Map seriesIndex to correct type based on colors & labels
                    if (seriesIndex === 1) {           // Open - Yellow
                        iirType = 'open';
                        iirOpenClose = 9;
                    } else if (seriesIndex === 2) {    // Closed - Green
                        iirType = 'close';
                        iirOpenClose = 9;
                    } else if (seriesIndex === 0) {    // Total - Blue
                        iirType = 'all';
                        iirOpenClose = null;
                    } else {
                        return;
                    }

                    redirectcharturl(iirType, iirOpenClose, url, unitId);
                }
            }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                borderRadius: 10,
                columnWidth: '10%',
                borderRadiusApplication: 'end',
                borderRadiusWhenStacked: 'last'
            }
        },
        dataLabels: {
            enabled: true,
            style: {
                colors: ['#fff']
            },
        },
        xaxis: {
            categories: {!! json_encode($units) !!}
        },
        yaxis: {
            title: {
                text: 'Incident Count'
            }
        },
        fill: {
            opacity: 1
        },
        colors: ['#1E90FF', '#ffc107', '#28a745'], // ✅ Blue, Yellow, Green
        tooltip: {
            y: {
                formatter: function(val) {
                    return val + " incidents";
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
