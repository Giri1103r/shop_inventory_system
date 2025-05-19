<div id="typeOfIIRChart"></div>

<script>
      var incidentTypeIdMap = {!! json_encode($formattedData['idMap']) !!};

    var options = {
        series: [{
            name: 'Incident Count',
            data: {!! json_encode($formattedData['counts']) !!}
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
                    var incidentTypeName = chartContext.w.config.xaxis.categories[dataPointIndex];
                    var incidentTypeId = incidentTypeIdMap[incidentTypeName];

                    if (incidentTypeId) {
                        redirectToIms(incidentTypeId);
                    }
                }
            }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                borderRadius: 5,
                borderRadiusApplication: 'end'
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
        colors: [
            '#1E90FF',
            '#32CD32',
            '#FF6347',
            '#FFD700',
            '#6A5ACD',
            '#00CED1',
            '#DC143C',
            '#FFA500',
            '#2E8B57',
            '#8B4513'
        ],

        legend: {
            position: 'bottom'
        }
    };

    var chart = new ApexCharts(document.querySelector("#typeOfIIRChart"), options);
    chart.render();

    // Download button functionality
    $("#TypeofIIR_download").off("click").on("click", function() {
        chart.dataURI().then(({
            imgURI,
            blob
        }) => {
            const link = document.createElement('a');
            link.href = imgURI;
            link.download = 'type_of_iir_chart.png';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    });
</script>
