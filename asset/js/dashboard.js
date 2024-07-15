$(function () {

  'use strict';
  // Option

  $('#reservation').daterangepicker({
    "maxSpan": {
        "days": 15
    },
    "maxDate": moment()
});

});
var dashboardChartOptions = {
  // Boolean - If we should show the scale at all
  showScale               : true,
  // Boolean - Whether grid lines are shown across the chart
  scaleShowGridLines      : false,
  // String - Colour of the grid lines
  scaleGridLineColor      : 'rgba(0,0,0,.05)',
  // Number - Width of the grid lines
  scaleGridLineWidth      : 1,
  // Boolean - Whether to show horizontal lines (except X axis)
  scaleShowHorizontalLines: true,
  // Boolean - Whether to show vertical lines (except Y axis)
  scaleShowVerticalLines  : true,
  // Boolean - Whether the line is curved between points
  bezierCurve             : true,
  // Number - Tension of the bezier curve between points
  bezierCurveTension      : 0.3,
  // Boolean - Whether to show a dot for each point
  pointDot                : false,
  // Number - Radius of each point dot in pixels
  pointDotRadius          : 4,
  // Number - Pixel width of point dot stroke
  pointDotStrokeWidth     : 1,
  // Number - amount extra to add to the radius to cater for hit detection outside the drawn point
  pointHitDetectionRadius : 20,
  // Boolean - Whether to show a stroke for datasets
  datasetStroke           : true,
  // Number - Pixel width of dataset stroke
  datasetStrokeWidth      : 2,
  // Boolean - Whether to fill the dataset with a color
  datasetFill             : true,
  // String - A legend template
  legendTemplate          : '<ul class=\'<%=name.toLowerCase()%>-legend\'><% for (var i=0; i<datasets.length; i++){%><li><span style=\'background-color:<%=datasets[i].lineColor%>\'></span><%=datasets[i].label%></li><%}%></ul>',
  // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
  maintainAspectRatio     : true,
  // Boolean - whether to make the chart responsive to window resizing
  responsive              : true
};

function loadGraph1(){
  Pace.restart();
  $("#grafik-resign").html('<canvas id="resignChart" style="height: 180px;"></canvas>');
  var periode = $("#reservation").val();
  var sArea   = $("#sArea").val();
  var sCabang = $("#sCabang").val();
  var arrPeriode = periode.split(" - ");
  $("#resign-from").html(arrPeriode[0]);
  $("#resign-to").html(arrPeriode[1]);

  $.ajax({
    method : "POST",
    url    : url + "dashboard-getdata-resign",
    data   : {periode:periode,area:sArea,cabang:sCabang},
    success: function(res){
      var arrResult = jQuery.parseJSON(res);
      // Get context with jQuery - using jQuery's .get() method.
      var resignChartCanvas = $('#resignChart').get(0).getContext('2d');
      // This will get the first returned node in the jQuery collection.
      var resignChart       = new Chart(resignChartCanvas);

      var resignChartData = {
        labels  : arrResult.label,
        datasets: [
          {
            label               : 'Digital Goods',
            fillColor           : 'rgba(21,101,192,0.9)',
            strokeColor         : 'rgba(60,141,188,0.8)',
            pointColor          : '#3b8bba',
            pointStrokeColor    : 'rgba(60,141,188,1)',
            pointHighlightFill  : '#fff',
            pointHighlightStroke: 'rgba(60,141,188,1)',
            data                : arrResult.value
          }
        ]
      };
      // Create the line chart
      resignChart.Line(resignChartData, dashboardChartOptions);
    }
  });

}

function loadGraph2(){
  Pace.restart();
  $("#grafik-mutation-in").html('<canvas id="mutationInChart" style="height: 180px;"></canvas>');
  var periode = $("#reservation").val();
  var sArea   = $("#sArea").val();
  var sCabang = $("#sCabang").val();
  var arrPeriode = periode.split(" - ");
  $("#mutation-in-from").html(arrPeriode[0]);
  $("#mutation-in-to").html(arrPeriode[1]);

  $.ajax({
    method :"POST",
    url    : url + "dashboard-getdata-mutation-in",
    data   : {periode:periode,area:sArea,cabang:sCabang},
    success: function(res){
      var arrResult = jQuery.parseJSON(res);
      // Get context with jQuery - using jQuery's .get() method.
      var mutationInChartCanvas = $('#mutationInChart').get(0).getContext('2d');
      // This will get the first returned node in the jQuery collection.
      var mutationInChart       = new Chart(mutationInChartCanvas);

      var mutationInChartData = {
        labels  : arrResult.label,
        datasets: [
          {
            label               : 'Digital Goods',
            fillColor           : 'rgba(21,101,192,0.9)',
            strokeColor         : 'rgba(60,141,188,0.8)',
            pointColor          : '#3b8bba',
            pointStrokeColor    : 'rgba(60,141,188,1)',
            pointHighlightFill  : '#fff',
            pointHighlightStroke: 'rgba(60,141,188,1)',
            data                : arrResult.value
          }
        ]
      };
      // Create the line chart
      mutationInChart.Line(mutationInChartData, dashboardChartOptions);
    }
  });
}

function loadGraph3(){
  Pace.restart();
  $("#grafik-mutation-out").html('<canvas id="mutationOutChart" style="height: 180px;"></canvas>');
  var periode = $("#reservation").val();
  var sArea   = $("#sArea").val();
  var sCabang = $("#sCabang").val();
  var arrPeriode = periode.split(" - ");
  $("#mutation-out-from").html(arrPeriode[0]);
  $("#mutation-out-to").html(arrPeriode[1]);

  $.ajax({
    method :"POST",
    url    : url + "dashboard-getdata-mutation-out",
    data   : {periode:periode,area:sArea,cabang:sCabang},
    success: function(res){

      var arrResult = jQuery.parseJSON(res);
      // Get context with jQuery - using jQuery's .get() method.
      var mutationOutChartCanvas = $('#mutationOutChart').get(0).getContext('2d');
      // This will get the first returned node in the jQuery collection.
      var mutationOutChart       = new Chart(mutationOutChartCanvas);

      var mutationOutChartData = {
        labels  : arrResult.label,
        datasets: [
          {
            label               : 'Digital Goods',
            fillColor           : 'rgba(21,101,192,0.9)',
            strokeColor         : 'rgba(60,141,188,0.8)',
            pointColor          : '#3b8bba',
            pointStrokeColor    : 'rgba(60,141,188,1)',
            pointHighlightFill  : '#fff',
            pointHighlightStroke: 'rgba(60,141,188,1)',
            data                : arrResult.value
          }
        ]
      };
      // Create the line chart
      mutationOutChart.Line(mutationOutChartData, dashboardChartOptions);
    }
  });
}

function loadLocationSummary(){
  Pace.restart();
  var area = $("#sArea1").val();
  var cabang = $("#sCabang1").val();
  $.ajax({
    method  : "POST",
    url     : url + "dashboard-load-location-review",
    data    : {area,cabang},
    success : function(res){
      var obj = $.parseJSON(res);
      $("#location-review").html(obj);

    }
  });
}

paceOptions = {
  ajax: true, // disabled
  document: false, // disabled
  eventLag: false, // disabled
  elements: {
    selectors: ['.my-page']
  }
};
