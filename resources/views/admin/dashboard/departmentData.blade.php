<div id="departmentData"></div>

@php
    $departmentNames = $departmentDetails->mapWithKeys(fn($d) => [$d->id => $d->department_name]);
@endphp

<script>
    var chartData = @json($chartDataArray); // { 1: 10, 5: 7 }
    var departmentNames = @json($departmentNames); // { 1: "HR", 5: "Finance" }

    var sortedData = Object.entries(chartData).sort((a, b) => b[1] - a[1]);
    var sortedDepartmentIds = sortedData.map(item => parseInt(item[0]));
    var sortedCounts = sortedData.map(item => item[1]);

    var sortedLabels = sortedDepartmentIds.map(id => {
        var name = departmentNames[id] ?? 'Unknown';
        return name.length > 8 ? name.substring(0, 8) + '...' : name;
    });

    var seriesData = [{
        name: 'Department Count',
        data: sortedCounts
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
                    var selectedDepartmentID = sortedDepartmentIds[config.dataPointIndex];
                    const url = "{{ admin_url('training_schedule/list') }}";
                    redirectcharturl('department', selectedDepartmentID, url);
                }
            },
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '10%',
                distributed: true,
                dataLabels: {
                    total: {
                        enabled: true,
                        style: {
                            fontSize: '13px',
                            fontWeight: 900
                        }
                    }
                }
            }
        },
        xaxis: {
            categories: sortedLabels,
            labels: {
                rotate: -45,
                formatter: function(val) {
                    return val.length > 8 ? val.substring(0, 8) + '...' : val;
                }
            }
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

                var Fromdate = @json($getdashdata->Fromdate ?? null);
                var Todate = @json($getdashdata->Todate ?? null);

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
                    link.download = 'DEPARTMENT WISE TRAINING COUNT.png';
                    link.click();
                });
            };
            image.src = imgURI;
        });
    });
</script>
