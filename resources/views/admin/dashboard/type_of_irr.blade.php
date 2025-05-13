<div id="typeOfIIRChart"></div>

<script>
    var options = {
        series: [{
            name: 'Incident Count',
            data: {!! json_encode($formattedData['counts']) !!}
        }],
        chart: {
            type: 'bar',
            height: 400,
            toolbar: {
                show: false
            },
        },
        xaxis: {
            categories: {!! json_encode($formattedData['labels']) !!},
            title: {
                text: 'Incident Type'
            }
        },
        yaxis: {
            title: {
                text: 'Number of Incidents'
            }
        },
        dataLabels: {
            enabled: true
        },
        colors: ['#F97316'],
        legend: {
            position: 'bottom'
        }
    };

    var chart = new ApexCharts(document.querySelector("#typeOfIIRChart"), options);
    chart.render();

    // Download button functionality
    $("#TypeofIIR_download").off("click").on("click", function() {
        chart.dataURI().then(({ imgURI, blob }) => {
            const link = document.createElement('a');
            link.href = imgURI;
            link.download = 'type_of_iir_chart.png';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    });
</script>
