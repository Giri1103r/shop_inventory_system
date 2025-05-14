<div id="TrainingCompletion"></div>

<script>
    var TrainingCompletionOptions = {
        series: [
            {{ $formattedData['closed_percentage'] ?? 0 }},
            {{ $formattedData['open_percentage'] ?? 0 }}
        ],
        chart: {
            type: 'pie',
            height: 320,
            id: 'TrainingCompletionChart'
        },
        labels: ['Closed Trainings (%)', 'Open Trainings (%)'],
        colors: ['#28a745', '#dc3545'],
        dataLabels: {
            formatter: function (val) {
                return val.toFixed(2) + '%';
            }
        },
        legend: {
            position: 'bottom'
        }
      
    };

    var TrainingCompletionChart = new ApexCharts(document.querySelector("#TrainingCompletion"), TrainingCompletionOptions);
    TrainingCompletionChart.render();

    // Download button functionality
    $("#TrainingCompletion_download").off("click").on("click", function() {
        TrainingCompletionChart.dataURI().then(({ imgURI, blob }) => {
            const link = document.createElement('a');
            link.href = imgURI;
            link.download = 'training_completion_chart.png';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    });
</script>
