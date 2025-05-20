<div id="departmentData"></div>

@php
    $departments = $departmentDetails->pluck('department_name')->toArray();
    $departmentsID = $departmentDetails->pluck('id')->toArray();

@endphp

<script>
    var departments = @json($departments);
    var chartData = @json($chartDataArray);

    var sortedData = Object.entries(chartData).sort((a, b) => b[1] - a[1]);

    var sorteddepartments = sortedData.map(item => item[0]);
    var sortedCounts = sortedData.map(item => item[1]);
    // Prepare series data
    var seriesData = [{
        name: 'Department Count',
        data: sortedCounts
        // data: departments.map(category => chartData[category] || 0)
    }];

    var options = {
        series: seriesData,
        chart: {
            type: 'bar',
            height: 450,
            stacked: true,
            toolbar: {
                show: false
            },
            events: {
                dataPointSelection: function(event, chartContext, config) {
                    var departmentIndex = config.seriesIndex;
                    var department_id = departmentsID[departmentIndex];
                    redirectToTraininglist('', '', department_id)
                },
            },
        },
        responsive: [{
            breakpoint: 480,
            options: {
                legend: {
                    position: 'bottom',
                    offsetX: -10,
                    offsetY: 0
                }
            }
        }],
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '10%',
                dataLabels: {
                    total: {
                        enabled: true,
                        style: {
                            fontSize: '13px',
                            fontWeight: 900
                        }
                    }
                },
                distributed: true
            },
        },

        xaxis: {
            categories: sorteddepartments,
            labels: {
                rotate: -45,
                formatter: function(value) {
                    return value.length > 8 ? value.substring(0, 8) + '...' : value;
                }
            },

        },

        fill: {
            opacity: 1
        }
    };

    var departmentData = new ApexCharts(document.querySelector("#departmentData"), options);
    departmentData.render();

    // Download button functionality
    $("#LoadDepartmentCount_download").off("click").on("click", function() {
        departmentData.dataURI().then(({
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
                var headerText = 'DEPARTMENT WISE TRAINING COUNT';
                ctx.fillText(headerText, 10, 30);

                // var factoryNames = '';
                // @if ($getdashdata->Factory && is_array($getdashdata->Factory) && isset($getdashdata->Factory))
                //     factoryNames = @json(getFactoryNames(arrayDecrypt($getdashdata->Factory)));
                // @endif

                var Fromdate = @json($getdashdata->Fromdate ?? null);
                var Todate = @json($getdashdata->Todate ?? null);

                var yPos = 60;

                if (Fromdate || Todate) {
                    var subHeaderText = 'Filtered By:';
                    ctx.fillText(subHeaderText, 10, yPos);

                    // if (factoryNames) {
                    //     yPos += 50;
                    //     ctx.fillText('Factory: ' + factoryNames, 10, yPos);
                    // }

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
                    link.download = 'DEPARTMENT WISE TRAINING COUNT.png';
                    link.click();
                });
            };
            image.src = imgURI;
        });
    });
</script>
