<?php
namespace obus;
include_once 'utils.inc.php';
include_once 'dbObjects.class.php';
include_once 'HTMLroutines.class.php';

?>
<html>
<head>
<title>Chart Simple Line scheduling</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

<script type="text/javascript" src="../js/jquery.min.js"></script>

<script type="text/javascript" src="../js/highcharts.js"></script>
<script type="text/javascript" src="../js/highcharts-more.js"></script>
<script type="text/javascript" src="../js/exporting.js"></script>
<script>
		
	$(function() {	
Highcharts.chart('container', {

    title: {
        text: 'Solar Employment Growth by Sector, 2010-2016'
    },

    subtitle: {
        text: 'Source: thesolarfoundation.com'
    },

    yAxis: {
        title: {
            text: 'Number of Employees'
        }
    },
    legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'middle'
    },

    plotOptions: {
        series: {
            pointStart: 1
        }
    },

    series: <?php $pitstops = Way::getPitstopsByItinerary(); echo HTML::arrayLineChart($pitstops);?>

});

})
series: <?php $pitstops = Way::getPitstopsByItinerary(); echo HTML::arrayLineChart($pitstops);?>

</script><pre>
<?php
//echo HTML::arrayLineChart($pitstops);
?></pre>
<!--<script type="text/javascript" src="../js/hchart.js"></script> -->
<link rel="stylesheet" type="text/css" href="../css/hchart.css">
</head>
<body>
<div id="container" style="width:1000px;height:600px;margin:.5em;"></div>
<a href="obus-test.php" >settings</a>
</body>
</html>