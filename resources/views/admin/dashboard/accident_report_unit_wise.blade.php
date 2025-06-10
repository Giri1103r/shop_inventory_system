<div id="AccidentReportChart"></div>

<script>
    var lookup = {!! json_encode($formattedData['lookup']) !!};

    var options = {
        series: [{
                name: 'Major',
                data: {!! json_encode($formattedData['major']) !!}
            },
            {
                name: 'Minor',
                data: {!! json_encode($formattedData['minor']) !!}
            },
            {
                name: 'Fatal',
                data: {!! json_encode($formattedData['fatal']) !!}
            }
        ],
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: false
            },
            events: {
                dataPointSelection: function(event, chartContext, config) {
                    var seriesName = chartContext.w.config.series[config.seriesIndex].name;
                    var unitName = chartContext.w.config.xaxis.categories[config.dataPointIndex];

                    var selected = lookup[seriesName] && lookup[seriesName][unitName];
                    if (selected) {
                        var unitId = selected.unit_id;
                        var injuryType = selected.injury_type;
                        redirectToIms('',unitId, '',injuryType,'','','');
                    }
                }
            }
        },

        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '50%',
                borderRadiusApplication: 'end'
            },
        },
        dataLabels: {
            enabled: true
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: {!! json_encode($formattedData['labels']) !!},
            title: {
                text: 'Unit Name'
            }
        },
        yaxis: {
            title: {
                text: 'Number of Incidents'
            }
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val + " incident(s)";
                }
            }
        },
        legend: {
            position: 'bottom'
        },
        colors: ['#EF4444', '#FACC15', '#6366F1']
    };

    var chart = new ApexCharts(document.querySelector("#AccidentReportChart"), options);
    chart.render();

    // Download chart image
    $("#AccidentReportUnitWise_download").off("click").on("click", function() {
        chart.dataURI().then(({
            imgURI
        }) => {
            const link = document.createElement('a');
            link.href = imgURI;
            link.download = 'accident_report_unit_wise.png';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    });
</script>
