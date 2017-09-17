<?php
namespace obus;

include_once 'auth.inc.php';

include_once 'utils.inc.php';
include_once 'dbObjects.class.php';
include_once 'HTMLroutines.class.php';

?>
<!DOCTYPE html>
<html>
<head>
<title>Obus How to use page</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<script type="text/javascript" src="../js/jquery.min.js"></script>

<script type="text/javascript" src="../js/bootstrap.min.js"></script>

<link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
<script>
/* use: var obus_text = getSelectedText('obusSel');
*/
function getSelectedText(elementId) {
    var elt = document.getElementById(elementId);

    if (elt.selectedIndex == -1)
        return null;

    return elt.options[elt.selectedIndex].text;
}

</script>
<style>
.hided{
	display:none;
}
.table-condensed .btn_del{
    padding: 2px 5px;
    font-size: 10px;
	font-weight:bold;
    line-height: 1.1;
    border-radius: 3px;
	margin:-2px 0;
}
.obus_header{
	margin-bottom:20px;
}
.oror{
	margin:auto;
	width:50px;
}
img{
    max-width: 100%;
    max-height: 100%;
	outline:1px solid orange;
}
img.schema {
    width:80%; /* you can use % */
    height: auto;
}
p{
 margin:20px 0;
 font-weight:bold;
 font-size:16px;
}
</style>
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
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<!-- error messages -->
	<div class="clearfix"></div>
<?php
if(! empty($retval)){$b_class='alert alert-danger';$b_hidden=null;}
if(isset($_GET['result']) AND $_GET['result'] == 0) {$b_class='alert alert-danger';$b_hidden=null;}
if(isset($_GET['result']) AND $_GET['result'] == 1) {$b_class='alert alert-success';$b_hidden=null;
$_GET['msg'] = $_GET['msg']."Please move to <a href='/tt/obus/'>main page</a>.";}
if( ! isset($_GET['result']) ) {$b_class='alert alert-success';$b_hidden='hidden="true"';}
?>
	<div id="obus_formErrors" class="<?=$b_class;?>" <?=$b_hidden;?>">
	<a class="close" href="#" onclick="$('#obus_formErrors').prop('hidden', true);">×</a>
	<p id="obus_register_baloon"><?= unserialize($_GET['msg']);?></p>
	</div>
	<!-- /error messages -->
	<p>Assume we have two alternative routes from point 1 to point 2 of public city transport</p><p>
		<img class='schema' src="http://<?=$_SERVER['HTTP_HOST'].'/'.SITE_ROOT.'/img/MMapObus.png';?>"/>
	</p>
	<p>So Obus chart allows to see which one is better for travel</p><p>
		<img class='schema' src="http://<?=$_SERVER['HTTP_HOST'].'/'.SITE_ROOT.'/img/obusHow2.png';?>"/>
			</p>
	<p>At once one should setup all preliminary data (routes, bus numbers, bus stations etc.)</p><p>
		<img src="http://<?=$_SERVER['HTTP_HOST'].'/'.SITE_ROOT.'/img/obusHow3.png';?>"/>
			</p>
	<p>Then create for every itinerary station-to-station timeline as well as create one or some sequence of stations for every routes</p><p>
		<img src="http://<?=$_SERVER['HTTP_HOST'].'/'.SITE_ROOT.'/img/obusHow4.png';?>"/>
			</p>
	<p>Schedule for city transport (e.g. Minsk city) may be taken from websites</p><p>
		<img src="http://<?=$_SERVER['HTTP_HOST'].'/'.SITE_ROOT.'/img/obusHow5.png';?>"/>
			</p>
	<p>After that one can look at schedule of all transport itineraries with interchanges</p><p>
		<img class='schema' src="http://<?=$_SERVER['HTTP_HOST'].'/'.SITE_ROOT.'/img/Obus_howto1.png';?>"/>
			</p>
	<p>And pick up better time for making travel with public city transport</p><p>
		<img class='schema' src="http://<?=$_SERVER['HTTP_HOST'].'/'.SITE_ROOT.'/img/Obus_howto2.png';?>"/>
		</p>
		</div>	
	</div>
</div>	
<pre>
<?php
//$pitstops = Way::GetPitsCountForItinerary(13);
//var_dump($pitstops);
/*
$pitstops = Way::getPitstopsByDestination(1);
$seqstats = sequencesStations::getSeqStatNamesBySequenceID(1); echo "[".HTML::arrayLineChartCategories($seqstats)."]";

$pitstops = Way::getPitstopsBySequence(1); 

echo HTML::arrayLineChart($pitstops, 1); 
*/
//if((1 === false)OR(false===false)){echo 'bre';}
//\LinkBox\Logger::log(serialize($pitstops));
//
//echo json_encode($pitstops);
//echo HTML::normalizeWays2JSON($pitstops);
//<button onClick="obusUPD();">obusUpd(8)</button>
//<button onClick="postTest();">postTest</button>
?>
</pre>

<span id="testSpan"></span>
</body>
</html>