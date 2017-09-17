<?php
namespace obus;

include_once 'auth.inc.php';

include_once 'utils.inc.php';
include_once 'dbObjects.class.php';
include_once 'HTMLroutines.class.php';
if(! empty($_GET['seq'])){$seq = $_GET['seq'];}else $seq=-1;
//echo 'seq: '.Sequence::load($seq)->name;
//var_dump(Sequence::load($seq)->name);
?>
<html>
<head>
<title>Chart Line scheduling</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

<script type="text/javascript" src="../js/jquery.min.js"></script>

<script type="text/javascript" src="../js/highcharts.js"></script>
<script type="text/javascript" src="../js/highcharts-more.js"></script>
<script type="text/javascript" src="../js/exporting.js"></script>

<script type="text/javascript" src="../js/bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
<script>
		
	$(function() {	
var chart1 = new Highcharts.chart('container', {
    chart:		{	type: 'line'   },
    title:		{	text: 'Transport timeline'    },
    subtitle:	{	text: '<?php echo Sequence::load($seq)->name; ?> '   },	
    xAxis: {
        categories: <?php $seqstats = sequencesStations::getSeqStatNamesBySequenceID($seq); echo "[".HTML::arrayLineChartCategories($seqstats)."]";?>
    },
    yAxis: {
        title: {          text: 'Time (normalized)'        }
    },
    plotOptions: {
        line: {
            dataLabels: {
                enabled: true
            },
            enableMouseTracking: true,
	point: {
		events: {
			click: function (e) {
				/*hs.htmlExpand(null, {
					pageOrigin: {
						x: e.pageX || e.clientX,
						y: e.pageY || e.clientY
					},
					headingText: this.series.name,
					maincontentText: Highcharts.dateFormat('%A, %b %e, %Y', this.x) + ':<br/> ' +
						this.y + ' visits',
					width: 200
				});*/
			}
		}
	}
        }
    },
	tooltip: {  formatter: pit_formatter 
        /*formatter: function () {            return 'Time at <b>' + this.x + '</b> is <b>' + time2HHMM(this.y) + '</b><br/> of '+this.series.name;   }*/
    },
    series: <?php $pitstops = Way::getPitstopsBySequence($seq); echo HTML::arrayLineChartFlex($pitstops, $seq);?>
});
})
function pit_formatter(){
return 'Time at <b>' + this.x +
                '</b> is <b>' + time2HHMM(this.y) + '</b><br/> of '+this.series.name;
}
function time2HHMM(time){
		var m = time % 60;
		var mstr = '';
		var h = Math.floor(time / 60);
		if(m < 10){ mstr = '0'+m}else{mstr = m}
		return h+':'+mstr;
}
function redraw2(){
	var id_sequence = $('#sequencesSelect').val();
	window.location.replace('hchartLine.php?seq='+id_sequence);
}
function redrawUP(){
	var id_sequence = $('#sequencesSelectUP').val();
	window.location.replace('hchartLine.php?seq='+id_sequence);
}
function redraw(){
// TODO
//figure out how to pass array_as_string to js and parse it
	var id_sequence = $('#sequencesSelect').val();
	console.info('data to send:', {id:id_sequence, table:'chart_redraw_seq'});
	
	var newSequences = '';
	
	$.post(
		"post.routines.php",
		{id:id_sequence, table:'chart_redraw_seq'},
		function(data){
			if (data.result == 'ok' ){
				afterResponce(data.payload, data.seqpits );
			}else{	console.log('error message: ',data.message);}
		}
		,"json"
	);
//TODO categories values depending on destination REF
function afterResponce(newSequences, newPits){
console.log("newSequences: "+newSequences);	
console.log("newPits: "+newPits.slice(1,-1));	

//var categoriesArr = newSequences.split(',');
var categoriesArr = JSON.parse("[" + newSequences + "]"); 
var seriesArr = JSON.parse("[" + newPits.slice(1,-1) + "]"); 

//var seriesArr = (newPits.slice(1,-1)).split('|');
//var seriesArr = eval( "(" + newPits.slice(1,-1) + ")" );
console.log(seriesArr);
var chart1 = $('#container').highcharts();
chart1.destroy();

var chart1 = new Highcharts.chart('container', {
    chart:		{	type: 'line'   },
    title:		{	text: 'Transport timeline'    },
    subtitle:	{	text: 'Source: minsktrans'    },	
    xAxis: {
        //categories: ['zel0', 'kol1', 'nem2', 'mas3', 'akd4', 'spu5', 'kaz6', 'tra7']
		categories: categoriesArr
    },
    yAxis: {
        title: {          text: 'Time (normalized)'        }
    },
    plotOptions: {
        line: {
            dataLabels: {
                enabled: true
            },
            enableMouseTracking: true,
	point: {
		events: {
			click: function (e) {
				/*hs.htmlExpand(null, {
					pageOrigin: {
						x: e.pageX || e.clientX,
						y: e.pageY || e.clientY
					},
					headingText: this.series.name,
					maincontentText: Highcharts.dateFormat('%A, %b %e, %Y', this.x) + ':<br/> ' +
						this.y + ' visits',
					width: 200
				});*/
			}
		}
	}
        }
    },
	tooltip: {  formatter: pit_formatter 
        /*formatter: function () {            return 'Time at <b>' + this.x + '</b> is <b>' + time2HHMM(this.y) + '</b><br/> of '+this.series.name;   }*/
    },
    series: seriesArr
});
//chart1.xAxis[0].update({        categories: newSequences    });
}//after responce
}
</script>
<!--<script type="text/javascript" src="../js/hchart.js"></script> -->
<link rel="stylesheet" type="text/css" href="../css/hchart.css">
</head>
<body>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="obus_header">
			<?php include_once '../tmplt/topmenu.inc.php' ?> 
			</div>
		</div>	
	</div>
</div>	
<div id="container" style="width:1000px;height:600px;margin:.5em;"></div>
<fieldset>
<select name="sequencesSelect" id="sequencesSelect">
<?php echo HTML::getSelectItems('sequences');?>
</select>
<button onClick="redraw();" style='display:none'>Redraw</button>
<button onClick="redraw2();">Redraw</button>
</fieldset>
<?php //$seqstats = sequencesStations::getSeqStatNamesBySequenceID(1); echo "[".HTML::arrayLineChartCategories($seqstats)."]";
?>
<pre>
<?php
//echo HTML::arrayLineChart($pitstops);
?></pre>
</body>
</html>