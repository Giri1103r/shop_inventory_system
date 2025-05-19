<div id="chart1"></div>

<script>
    (function renderGroupedBarChart() {
        var seriesData = {!! json_encode($chartData['series']) !!};
        var categories = {!! json_encode($chartData['labels']) !!};

        var options = {
            series: seriesData,
            chart: {
                type: 'bar',
                height: 300,
                stacked: false,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    columnWidth: '50%',
                    endingShape: 'rounded'
                }
            },
            xaxis: {

            },
            yaxis: {
                categories: categories
            },
            dataLabels: {
                enabled: true
            },
            legend: {
                position: 'bottom',
                horizontalAlign: 'left'
            },
            fill: {
                opacity: 1
            },
            colors: ['#3B5998', '#26A69A', '#FFC300', '#6C3483']
        };

        var chart = new ApexCharts(document.querySelector("#chart1"), options);
        chart.render();

        document.getElementById("stakcedLeadingChartDownload").addEventListener("click", function() {
            chart.dataURI().then(({
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
                    ctx.fillText('Training Hours by (Company-wise)', 10, 30);

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
                            yPos += 25;

                            @if ($from)
                                ctx.fillText('From Date: {{ $from }}', 10, yPos);
                                yPos += 25;
                            @endif

                            @if ($to)
                                ctx.fillText('To Date: {{ $to }}', 10, yPos);
                                yPos += 25;
                            @endif
                        @endif
                    @endif

                    ctx.drawImage(image, 0, headerHeight);

                    newCanvas.toBlob(function(blob) {
                        var link = document.createElement('a');
                        link.href = URL.createObjectURL(blob);
                        link.download = "{{ getLeadingName(LEADING_CATEGORY_1) }}.png";
                        link.click();
                    });
                };

                image.src = imgURI;
            });
        });
    })();
</script>
