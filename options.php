<?php
namespace lbx;

include_once 'cgi/auth.inc.php';

include_once 'cgi/utils.inc.php';
include_once 'cgi/dbObjects.class.php';
include_once 'cgi/HTMLroutines.class.php';
include_once 'cgi/linkHandler.class.php';


if(!empty($_POST['action'])){
	if( Auth::notLogged() ){
		break;
	}	
	switch ($_POST['action']){
		case 'folder':
			//var_dump($_POST);
			if(!empty($_POST['folderName'])){
				if(isset($_POST['isParent'])){
					$folder = new Folder($_POST['folderName']);
				}else{
					$parentID = intval($_POST['folderParentName']);
					if($parentID > 0){
						$folder = new Folder($_POST['folderName'],$_POST['folderParentName'] );					
					}else{
						$message = 'Parent folder not selected!';			
						\LinkBox\Logger::log("{$_POST['action']} error: ".$message);
						$actionStatus = 'error';
						break;
					}

				}
				$retval = $folder->save();
				if(!$retval){
					$message = $folder->errormsg;			
					\LinkBox\Logger::log("{$_POST['action']} error: ".$message);
					$actionStatus = 'error';
				}
			}
			break;
	case 'tag':
		//echo 'tag';
		if(!empty($_POST['tagName'])){
			$tag = new Tag($_POST['tagName']);
			$retval = $tag->save();
			if(!$retval){
				$message = $tag->errormsg;			
				\LinkBox\Logger::log("{$_POST['action']} error: ".$message);
				$actionStatus = 'error';
			}
		}		
	break;
	case 'link':
		//print_r($_POST);
		if(!empty($_POST['linkLink'])){
			$link = new Link($_POST['linkLink'], $_POST['linkName'], $_POST['linkFolder'], $_POST['tagsSimple'] );
			$retval = $link->save();
			if(!$retval){
				$message = $link->errormsg;			
				\LinkBox\Logger::log("{$_POST['action']} error: ".$message);
				$actionStatus = 'error';
			}
		}			
	break;
	case 'link2':
				//print_r($_POST);
		if(!empty($_POST['linkLink2'])){
			$link = new Link($_POST['linkLink2'], $_POST['linkName2'], $_POST['linkFolder2'], $_POST['tagsCSV'] );
			$retval = $link->save();
			if(!$retval){
				$message = $link->errormsg;			
				\LinkBox\Logger::log("{$_POST['action']} error: ".$message);
				$actionStatus = 'error';
			}
		}				
	break;
	case 'urlfavicon1':
	
			$fff = LinkHandler::getFaviconHref($_POST['urlfavicon']);
			if(false===$fff){
				$message = LinkHandler::$errormsg;			
				\LinkBox\Logger::log("{$_POST['action']} error: ".$message);
				$actionStatus = 'error';
			}
			$ttt = LinkHandler::getSiteTitle($_POST['urlfavicon']);
	break;
	case 'sequencesStationsEdit':
				//print_r($_POST);
	break;
	case 'destination':
				//print_r($_POST);
	break;
	case 'sequence':
				//print_r($_POST);
	break;
	case 'sequencesStations':
	break;
	}
}
//echo( 'action:'.$_POST['action'] );
//parse_str($_POST["lbx_form_addlink"], $ajax);
//print_r($ajax);
?>
<html>
<head>
<title>Options and editing</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<?=HTML::favicon();?>
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="css/linkbox.css">
<script>
function getSelectedText(elementId) {
    var elt = document.getElementById(elementId);

    if (elt.selectedIndex == -1)
        return null;

    return elt.options[elt.selectedIndex].text;
}

<?php if( Auth::notLogged()){ ?>
$(function(){
$('fieldset').prop('disabled',true);
})
<?}else{?>
$('fieldset').prop('disabled',false);
<?}?>
</script>
<style>
.opt_menu{
display:inline-block;
position:relative;
position:left;
}
.opt_content{
display:inline-block;
position:relative;
}
</style>
</head>
<body>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="obus_header">
			<?php include_once 'tmplt/topmenu.inc.php' ?> 
			</div>
		</div>	
	</div>
</div>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<?php include_once 'tmplt/errorBlock.inc.php' ?> 
		</div>	
	</div>
</div>
<div class="container">
	<div class="row">
		<div class="col-sm-12">
		<div class="col-sm-3">
    <ul class="nav nav-pills nav-stacked">
        <li class="active"><a data-toggle="tab" href="#sectionA">
			<span class="glyphicon glyphicon-home"></span> Section A</a></li>
        <li><a data-toggle="tab" href="#sectionB">
			<span class="glyphicon glyphicon-user"></span> Section B</a></li>
        <li><a data-toggle="tab" href="#sectionC">
			<span class="glyphicon glyphicon-envelope"></span> Section C</a></li>
    </ul>		
		</div>
		<div class="col-sm-9">
    <div class="tab-content">
        <div id="sectionA" class="tab-pane fade in active">
            <p>Section A content…</p>
            <p>Section A content…</p>
            <p>Section A content…</p>
            <p>Section A content…</p>
            <p>Section A content…</p>
            <p>Section A content…</p>
        </div>
        <div id="sectionB" class="tab-pane fade">
            <p>Section B content…</p>
        </div>
        <div id="sectionC" class="tab-pane fade">
            <p>Section C content…</p>
        </div>
    </div> 		
		</div>


 			

		</div>	
	</div>
</div>
<!-- -------------------------------------------- links --------------------------------------------  -->
<div class="container"> <!-- div zzz-->
	<div class="row">
		<div class="col-md-8">
			<div class="linkbox_linkSimple">
		
			</div>
		</div>
		<div class="col-md-4">
			<div class="linkbox_linkTest">
			
			</div>
		</div>	
	</div>
</div><!-- zzz end -->

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