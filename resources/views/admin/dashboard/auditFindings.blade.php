<div id="auditFindings"></div>

<script>
    var auditAssessmentCount = {!! json_encode($auditAssessmentCount) !!};
    var auditAnalysisCount = {!! json_encode($auditAnalysisCount) !!};
    var interUnitCount = {!! json_encode($interUnitCount) !!};
    var auditMonthlyCount = {!! json_encode($auditMonthlyCount) !!};

    var options = {
        series: [auditAssessmentCount, auditAnalysisCount, interUnitCount, auditMonthlyCount],
        chart: {
            height: 350,
            type: 'radialBar',
        },
        plotOptions: {
            radialBar: {
                offsetY: 0,
                startAngle: 0,
                endAngle: 270,
                hollow: {
                    margin: 5,
                    size: '30%',
                    background: 'transparent',
                },
                dataLabels: {
                    name: {
                        show: true,
                    },
                    value: {
                        show: true,
                    }
                }
            }
        },
        colors: ['#1ab7ea', '#0084ff', '#39539E', '#0077B5'],
        labels: ['Audit Assessment', 'Audit Analysis', 'Inter Unit Audit', 'Monthly Audit Plan'],
        legend: {
            show: true,
            floating: true,
            fontSize: '14px',
            position: 'left',
            offsetX: 160,
            offsetY: 15,
            labels: {
                useSeriesColors: true,
            },
            markers: {
                size: 0
            },
            formatter: function(seriesName, opts) {
                return seriesName + ": " + opts.w.globals.series[opts.seriesIndex];
            },
            itemMargin: {
                vertical: 3
            }
        },
        responsive: [{
            breakpoint: 480,
            options: {
                legend: {
                    show: false
                }
            }
        }]
    };

    var auditFindings = new ApexCharts(document.querySelector("#auditFindings"), options);
    auditFindings.render();

    // Download button functionality
    $("#auditFindings_download").off("click").on("click", function() {
        auditFindings.dataURI().then(({
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
                ctx.fillText('Type Of Audit Findings', 10, 30);

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
                    link.download = 'Type Of Audit Findings.png';
                    link.click();
                });
            };

            image.src = imgURI;
        });
    });
</script>
