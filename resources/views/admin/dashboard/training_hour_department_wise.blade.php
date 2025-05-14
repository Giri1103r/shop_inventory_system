<div id="LoadTrainingHourSafetyDepartmentWise_Count"></div>

<script>
    // Chart data from the controller
    var departments = {!! json_encode($chartData['departments']) !!};
    var categories = {!! json_encode($chartData['labels']) !!};
    var seriesData = {!! json_encode($chartData['series']) !!};

    var dynamicColors = [
        '#3B5998', '#26A69A', '#FFC300', '#6C3483', '#E74C3C', '#3498DB',
        '#1ABC9C', '#9B59B6', '#F39C12', '#2ECC71', '#E67E22', '#34495E'
    ];

    // Ensure colors match the number of bars
    var barCount = seriesData.length;
    var colors = dynamicColors.slice(0, barCount); // Or generate random colors if needed

    var options = {
        series: [{
            name: 'Total Hours',
            data: seriesData
        }],
        chart: {
            type: 'bar',
            height: 500,
            toolbar: {
                show: false
            },
            zoom: {
                enabled: false
            }
        },
        plotOptions: {
            bar: {
                borderRadius: 10,
                horizontal: false,
                distributed: true // ✅ Enables individual bar colors
            }
        },
        colors: colors, // ✅ Apply individual colors
        dataLabels: {
            enabled: true
        },
        xaxis: {
            categories: categories,
            labels: {
                rotate: -45,
                formatter: function(value) {
                    return value.length > 8 ? value.substring(0, 8) + '...' : value;
                }
            },
            style: {
                fontSize: '8px',
            },
        },
        grid: {
            padding: {
                bottom: 60
            }
        },
        tooltip: {
            custom: function({
                series,
                seriesIndex,
                dataPointIndex,
                w
            }) {
                return `
                <div style="padding:10px;">
                    <strong>Topic:</strong> ${w.globals.labels[dataPointIndex]}<br>
                    <strong>Hours:</strong> ${series[seriesIndex][dataPointIndex]}<br>
                    <strong>Department:</strong> ${departments[dataPointIndex]}
                </div>`;
            }
        },
        fill: {
            opacity: 1
        },
        legend: {
            show: false // ✅ Hide legend since each bar is unique
        }
    };

    var chartPPEIssuanceGroupWise = new ApexCharts(document.querySelector(
        "#LoadTrainingHourSafetyDepartmentWise_Count"), options);
    chartPPEIssuanceGroupWise.render();


    $("#LoadTrainingHourSafetyDepartmentWise_download").off("click").on("click", function() {
        chartPPEIssuanceGroupWise.dataURI().then(({
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
                ctx.fillText('Training Hours by Topic (Department-wise)', 10, 30);

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

                // Draw the chart image under the header
                ctx.drawImage(image, 0, headerHeight);

                // Save chart as image
                newCanvas.toBlob(function(blob) {
                    var link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = 'TrainingHours_DepartmentWise.png';
                    link.click();
                });
            };

            image.src = imgURI;
        });
    });
</script>
