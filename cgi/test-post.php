<?php
namespace obus;

include_once 'auth.inc.php';

include_once 'utils.inc.php';
include_once 'dbObjects.class.php';
include_once 'HTMLroutines.class.php';

\LinkBox\Logger::log('Start logging');

if(!empty($_POST['action'])){
		
switch ($_POST['action']){
	case 'obus':
		//echo 'obus';		
		if(!empty($_POST['obusName'])){
			$obus= new Obus($_POST['obusName']);
			$retval = $obus->save();
			if(!$retval)
			print_r("result: ".obus::$errormsg);
		}
		break;
	case 'station':
		//echo 'staaation';
		if(!empty($_POST['stationName'])){
			$station= new Station($_POST['stationName'], $_POST['statShortName']);
			$retval = $station->save();
			if(!$retval)
			print_r("result: ".station::$errormsg);
		}		
		break;
	case 'itinerary':
		//echo 'itinerary';
		//print_r($_POST);
		if(!empty($_POST['itineraryName'])){
			// [itineraryName] => a47c_Зел_ [obus] => 1 [station] => 1 [startTime] => 07:20 [action] => itinerary 
			$iName = $_POST['itineraryName'];
			$itiner = new Itinerary($iName, $_POST['obus'], $_POST['station'], $_POST['startTime']);
			$retval = $itiner->save();
			if(!$retval)
				print_r("result: ".itinerary::$errormsg);			
		}			
	break;
	case 'pitstops':
				//print_r($_POST);
			if( !empty($_POST['itinerarySelect']) ){
				$way = new Way($_POST);
				$retval = $way->save($_POST);
				if(!$retval)
					print_r("result: ".way::$errormsg);				
			}
	break;
	}
}
//echo( 'action:'.$_POST['action'] );
//parse_str($_POST["lbx_form_addlink"], $ajax);
//print_r($ajax);

?>
<html>
<head>
<title>Obus scheduling</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<script type="text/javascript" src="../js/jquery.min.js"></script>
<script type="text/javascript" src="../js/bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
<script>
$(function(){

$('#search').keyup(function() {
var $rows = $('#table tr');
    var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();
    console.log('val:'+val);
    $rows.show().filter(function() {
        var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
console.log('text:'+text);    
    //return !~text.indexOf(val);
        return !(1+text.indexOf(val));
    }).hide();
});
});

function postTest(){
	console.log('there');
	$.post( "post.routines.php", { id: "test" }, function( data ) {
		console.log('here');
	  console.log( data.result ); // John
	  console.log( data.time ); // 2pm
	},"json");
}

function tilda(){
var massive= new Array();
massive[1]="слово1";
massive[3]="слово2";
massive[5]="слово3";

for(var k in massive ) {
        console.log( 'k:'+ k  );
    if ( ~~k == k ) {
        console.log( massive[ k ] );
    }
}

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
			<div class="obus_header">
<input type="text" id="search" placeholder="Type to search">
<table id="table">
   <tr>
      <td>Apple</td>
      <td>Green</td>
   </tr>
   <tr>
      <td>Grapes</td>
      <td>Green</td>
   </tr>
   <tr>
      <td>Orange</td>
      <td>Orange</td>
   </tr>
</table>
			</div>
		</div>	
	</div>
</div>	
<button onclick="postTest();">test post</button>
<button onclick="postTest2();">test post 2</button>
<button onclick="tilda();">tilda</button>
<pre>
<?php
var_dump( User::getUserbyNameOrEmail('genom1','ge@ge.ge') );
//var_dump($_SERVER);
echo App::currentPage();
?>
</pre>
</body>
</html>