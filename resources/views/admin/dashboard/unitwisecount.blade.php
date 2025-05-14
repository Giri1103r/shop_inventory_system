<div id="unitwiseptw"></div>

<script>
    var unitData = @json($unitData); 
    var categories = unitData.map(function(unit) {
        return unit.unit_name; 
    });

    var permitCounts = unitData.map(function(unit) {
        return unit.permit_count; 
    });

    var customColors = [
        '#FF6347', '#32CD32', '#1E90FF', '#FF4500',
        '#8A2BE2', '#D2691E', '#00FA9A', '#FF1493',
        '#8B0000', '#4B0082', '#FF8C00', '#20B2AA'
    ];

    var options = {
        series: [{
            name: 'Permit Count',
            data: permitCounts 
        }],
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: false 
            }
        },
        plotOptions: {
            bar: {
                horizontal: false, 
                columnWidth: '55%',
                endingShape: 'rounded',
                distributed: true
            },
        },
        colors: customColors, 
        dataLabels: {
            enabled: true 
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: categories, 
            title: {
                text: 'Unit Name' 
            }
        },
        yaxis: {
            title: {
                text: 'Permit Count' 
            }
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val + " permits";
                }
            }
        }
    };
    var chart = new ApexCharts(document.querySelector("#unitwiseptw"), options);
    chart.render();

    // Function to download chart as PNG
    document.getElementById('unitwiseptw_download').addEventListener('click', function() {
        chart.dataURI().then(function(uri) {
            var link = document.createElement('a');
            link.href = uri.imgURI;
            link.download = 'Unitwise_PTW_Count.png';
            link.click();
        });
    });
</script>
