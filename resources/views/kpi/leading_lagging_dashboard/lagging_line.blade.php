 <div id="lagging_indicator"></div>

 <script>
     var chartLabels = @json($years);
     var chartData = {!! json_encode($datasets) !!};

     var options = {
         series: chartData,
         chart: {
             height: 350,
             type: 'line',
             dropShadow: {
                 enabled: true,
                 color: '#000',
                 top: 18,
                 left: 7,
                 blur: 10,
                 opacity: 0.5
             },
             zoom: {
                 enabled: false
             },
             toolbar: {
                 show: false
             }
         },
         colors: ['#77B6EA', '#545454', '#00E396', '#FEB019'],
         dataLabels: {
             enabled: true
         },
         stroke: {
             curve: 'smooth'
         },
         title: {
             text: 'Lagging Values Over the Years',
             align: 'left'
         },
         grid: {
             borderColor: '#e7e7e7',
             row: {
                 colors: ['#f3f3f3', 'transparent'],
                 opacity: 0.5
             }
         },
         markers: {
             size: 5
         },
         xaxis: {
             categories: chartLabels, // years
             title: {
                 text: 'Year'
             }
         },
         yaxis: {
             title: {
                 text: 'Value'
             },
             min: 0
         },
         legend: {
             position: 'top',
             horizontalAlign: 'right',
             floating: true,
             offsetY: -25,
             offsetX: -5
         }
     };
     var lagging_line = new ApexCharts(document.querySelector("#lagging_indicator"), options);
     lagging_line.render();

     $("#lagging_line_download").off("click").on("click", function() {
         lagging_line.dataURI().then(({
             imgURI
         }) => {
             var newCanvas = document.createElement('canvas');
             var ctx = newCanvas.getContext('2d');
             var image = new Image();

             image.onload = function() {
                 newCanvas.width = image.width;
                 let headerHeight = 120;
                 newCanvas.height = image.height + headerHeight;


                 ctx.fillStyle = 'white';
                 ctx.fillRect(0, 0, newCanvas.width, newCanvas.height);


                 ctx.fillStyle = '#203669';
                 ctx.font = '20px Arial';
                 ctx.fillText('CHART', 10, 30);


                 let yPos = 60;

                 var Fromdate = @json($from_date ?? null);
                 var Todate = @json($to_date ?? null);

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


                 ctx.drawImage(image, 0, headerHeight);


                 newCanvas.toBlob(function(blob) {
                     var link = document.createElement('a');
                     link.href = URL.createObjectURL(blob);
                     link.download = 'CHART.png';
                     link.click();
                 });
             };

             image.src = imgURI;
         });
     });
 </script>
