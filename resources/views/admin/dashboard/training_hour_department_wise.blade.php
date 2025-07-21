<div id="TrainingTopicWiseCount"></div>

<script>
    // Blade Variables
    var categories = {!! json_encode($chartData['labels']) !!}; // topic names
    var seriesData = {!! json_encode($chartData['series']) !!}; // total hours
    var topicIds = {!! json_encode($chartData['training_topic_id']) !!}; // training_topic_id

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
                    var topicId = topicIds[config.dataPointIndex];
                    const url = "{{ admin_url('training_schedule/list') }}";
                    redirectcharturl('topic_id', topicId, url);
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
                },
                style: {
                    fontSize: '10px'
                }
            }
        },
        grid: {
            padding: {
                bottom: 60
            }
        },
        fill: {
            opacity: 1
        },
        legend: {
            show: false
        }
    };

    var TrainingTopicWiseCount = new ApexCharts(document.querySelector("#TrainingTopicWiseCount"), options);
    TrainingTopicWiseCount.render();

    // Handle chart download
    var filterFrom = "{{ $getdashdata->from_date ?? '' }}";
    var filterTo = "{{ $getdashdata->to_date ?? '' }}";

    $("#LoadTrainingHourSafetyDepartmentWise_download").off("click").on("click", function () {
        TrainingTopicWiseCount.dataURI().then(({ imgURI }) => {
            var newCanvas = document.createElement('canvas');
            var ctx = newCanvas.getContext('2d');
            var image = new Image();

            image.onload = function () {
                newCanvas.width = image.width;
                let headerHeight = 120;
                newCanvas.height = image.height + headerHeight;

                ctx.fillStyle = 'white';
                ctx.fillRect(0, 0, newCanvas.width, newCanvas.height);

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

                ctx.drawImage(image, 0, headerHeight);

                newCanvas.toBlob(function (blob) {
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
