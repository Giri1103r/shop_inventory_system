@if (!$hasData)
    <div class="border-0 pb-3" style="margin-top: 166px;">
        <h4 style="text-align: center;">No data available.</h4>
    </div>
@else
    <div id="auditFindings"></div>

    <script>
        var auditAssessmentCount = {!! json_encode($auditAssessmentCount) !!};
        var auditAnalysisCount = {!! json_encode($auditAnalysisCount) !!};
        var interUnitCount = {!! json_encode($interUnitCount) !!};
        var auditMonthlyCount = {!! json_encode($auditMonthlyCount) !!};

        var labels = [
            'Audit Assessment',
            'Audit Analysis',
            'Inter Unit Audit',
            'EHS Audit Calendar'
        ];

        var values = [
            auditAssessmentCount,
            auditAnalysisCount,
            interUnitCount,
            auditMonthlyCount
        ];

        var options = {
            series: [{
                name: 'Audit Findings',
                data: values
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: false
                },
                events: {
                    dataPointSelection: function(event, chartContext, config) {
                        var dataPointIndex = config.dataPointIndex;

                        var redirectUrls = [
                            '{{admin_url('audit/assessment/list')}}',
                            '{{admin_url('audit/6s-analysis/list')}}',
                            '{{admin_url('audit/inter-unit-audit/checklist/list')}}',
                            '{{admin_url('audit/monthly-audit/audit-plan/list')}}'
                        ];

                        if (redirectUrls[dataPointIndex]) {
                            window.location.href = redirectUrls[dataPointIndex];
                        }
                    }
                }

            },
            plotOptions: {
                bar: {
                    columnWidth: '25%',
                    horizontal: false,
                    distributed: true
                }
            },
            colors: ['#3B5998', '#26A69A', '#FFC300', '#6C3483'],

            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return val;
                },
                offsetY: -10,
                style: {
                    fontSize: '12px',
                    colors: ['#333']
                }
            },
            xaxis: {
                categories: labels,
                labels: {
                    rotate: -15
                }
            },
            yaxis: {
                title: {
                    text: 'Count'
                }
            },
            legend: {
                show: false
            }
        };

        var auditFindings = new ApexCharts(document.querySelector("#auditFindings"), options);
        auditFindings.render();
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
@endif
