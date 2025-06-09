<div id="LoadTrainingHourSafetyDepartmentWise_Count"></div>

<script>
    var departments = {!! json_encode($chartData['departments']) !!};
    var categories = {!! json_encode($chartData['labels']) !!};
    var seriesData = {!! json_encode($chartData['series']) !!};
    var departmentIds = {!! json_encode($chartData['department_ids']) !!};

    var dynamicColors = [
        '#3B5998', '#26A69A', '#FFC300', '#6C3483', '#E74C3C', '#3498DB',
        '#1ABC9C', '#9B59B6', '#F39C12', '#2ECC71', '#E67E22', '#34495E'
    ];

    var barCount = seriesData.length;
    var colors = dynamicColors.slice(0, barCount);

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
            events: {
                dataPointSelection: function(event, chartContext, config) {
                    var departmentId = departmentIds[config.dataPointIndex];
                    redirectTotrainigschedule(departmentId);
                }
            },
            zoom: {
                enabled: false
            }
        },
        plotOptions: {
            bar: {
                columnWidth: '10%',
                horizontal: false,
                distributed: true
            }
        },
        colors: colors,
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
            show: false
        }
    };

    var LoadTrainingHourSafetyDepartmentWise_Count = new ApexCharts(document.querySelector(
        "#LoadTrainingHourSafetyDepartmentWise_Count"), options);
    LoadTrainingHourSafetyDepartmentWise_Count.render();

    // Prepare date filters (embedded as strings from Blade)
    var filterFrom = "{{ $getdashdata->Fromdate ?? '' }}";
    var filterTo = "{{ $getdashdata->Todate ?? '' }}";

    $("#LoadTrainingHourSafetyDepartmentWise_download").off("click").on("click", function() {
        LoadTrainingHourSafetyDepartmentWise_Count.dataURI().then(({
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

                if (filterFrom || filterTo) {
                    ctx.fillStyle = '#203669';
                    ctx.font = '16px Arial';
                    ctx.fillText('Filtered By:', 10, yPos);
                    yPos += 25;

                    if (filterFrom) {
                        ctx.fillText('From Date: ' + filterFrom, 10, yPos);
                        yPos += 25;
                    }

                    if (filterTo) {
                        ctx.fillText('To Date: ' + filterTo, 10, yPos);
                        yPos += 25;
                    }
                }

                // Draw chart
                ctx.drawImage(image, 0, headerHeight);

                // Save chart
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
