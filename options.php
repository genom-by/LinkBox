<?php
namespace lbx;

include_once 'cgi/auth.inc.php';

include_once 'cgi/utils.inc.php';
include_once 'cgi/dbObjects.class.php';
include_once 'cgi/settings.class.php';
include_once 'cgi/HTMLroutines.class.php';
include_once 'cgi/linkHandler.class.php';


if(!empty($_POST['action'])){
	if( Auth::notLogged() ){
		break;
	}	
	switch ($_POST['action']){
		case 'folder':
			//var_dump($_POST);
			if(!empty($_POST['folderName']) OR !empty($_POST['subfolderName']) ){
				if( $_POST['action'] == 'folder'){
					if( !empty($_POST['folderName']) ){
						$folder = new Folder($_POST['folderName']);}
				}elseif($_POST['action'] == 'subfolder'){
					$parentID = intval($_POST['folderParentName']);
					if($parentID > 0){
						$folder = new Folder($_POST['subfolderName'],$_POST['folderParentName'] );					
					}else{
						$message = 'Parent folder not selected!';			
						\LinkBox\Logger::log("{$_POST['action']} error: ".$message);
						$actionStatus = 'error';
						break;
					}

				}else{break;}
				$retval = $folder->save();
				if(!$retval){
					$message = $folder->errormsg;			
					\LinkBox\Logger::log("{$_POST['action']} error: ".$message);
					$actionStatus = 'error';
				}
			}else{
				$message = 'Fill in Folder Name';
				$actionStatus = 'error';
			}
			break;
	case 'tagsInputStyle':
		//echo 'tag';
		if(!empty($_POST['tagsInputStyleRad'])){
			$retval = Settings::Set('tagsInputStyle',$_POST['tagsInputStyleRad']);
			//$retval = $tag->save();
			if(!$retval){
				$message = Settings::$errormsg;			
				\LinkBox\Logger::log("{$_POST['action']} error: ".$message);
				$actionStatus = 'error';
			}
		}else{
			$message = 'Select tags type';
			$actionStatus = 'error';
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
<title>Options and editing</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<?=HTML::favicon();?>
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/linkbox.ev.js"></script>
<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="css/linkbox.css">
<link rel="stylesheet" type="text/css" href="css/options.css">
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
			<span class="glyphicon glyphicons-folder-open"></span> Folders</a></li>
        <li><a data-toggle="tab" href="#sectionB">
			<span class="glyphicon glyphicons-tags"></span> Tags</a></li>
        <li><a data-toggle="tab" href="#sectionC">
			<span class="glyphicon glyphicons-link"></span> Links</a></li>
        <li><a data-toggle="tab" href="#sectionD">
			<span class="glyphicon glyphicons-link"></span> Settings</a></li>
    </ul>		
		</div>
		<div class="col-sm-9">
    <div class="tab-content">
        <div id="sectionA" class="tab-pane fade in active">
            <div id='foldersPanel'>
				<ul class="nav nav-tabs">
				<li class="active"><a data-toggle="tab" href="#secPitNew">New Folder</a></li>
				<li><a data-toggle="tab" href="#secPitView">View & Edit Folders</a></li>
				</ul>
					<div class="tab-content">
				<!-- ---------------- New Folder ------------------------ -->
				<div id="secPitNew" class="tab-pane fade in active">
				<fieldset>

					<!-- accordion parent | sub-->
					<div id="accordion" class="panel-group">
        <div class="panel panel-default">
<div class="panel-heading"><h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Create parent folder</a></h4></div>
            <div id="collapseOne" class="panel-collapse collapse">
                <div class="panel-body">
                    <p>
				<form name="formPF" method="post">					
<input type="checkbox" name="isParent" id="isParent" hidden checked/>
<label for="folderName">Folder Name</label>
<input type="text" name="folderName" id="folderName" autocomplete="off"/>
<input type="submit" value="Send"/>
<input type="hidden" name="action" value="folder">
				</form>
					</p>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
<div class="panel-heading"><h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">Create subfolder</a></h4></div>
            <div id="collapseTwo" class="panel-collapse collapse in">
                <div class="panel-body">
                    <p>
				<form name="formSF" method="post">						
<label for="folderParentName">Parent Folder</label>					
<select name="folderParentName" id="folderParentName">
<?php echo HTML::getSelectItems('parentfolder');?>
</select>
<br/>
<label for="subfolderName">Subfolder Name</label>
<input type="text" name="subfolderName" id="subfolderName" autocomplete="off"/>
<input type="submit" value="Send"/>
<input type="hidden" name="action" value="subfolder">	
				</form>				
					</p>
                </div>
            </div>
        </div>
    </div>
					<!-- accordion parent | sub-->

				</fieldset>	
				</div>
				<!-- ---------------- View Pitstop ------------------------ -->
						<div id="secPitView" class="tab-pane fade">
				<fieldset>
				<form name="formPitView" method="post">
<table class="table table-striped table-hover table-condensed small">
<?php echo HTML::getTableItems('folderGroupEdit');?>
</table>
				</form>
				</fieldset>	
						</div>

					</div>
			</div>
        </div>
        <div id="sectionB" class="tab-pane fade">
            <p>
			<table class="table table-striped table-hover table-condensed small">
<?php echo HTML::getTableItems('tag');?>
</table>
			</p>
        </div>
        <div id="sectionC" class="tab-pane fade">
            <div id='linksPanel'>
				<ul class="nav nav-tabs">
				<li class="active"><a data-toggle="tab" href="#llinkNew">Add new Link</a></li>
				<li><a data-toggle="tab" href="#llinkView">View & Edit Links</a></li>
				</ul>
					<div class="tab-content">
				<!-- ---------------- New Folder ------------------------ -->
				<div id="llinkNew" class="tab-pane fade in active">
				<fieldset>
				<p>
				</p>
				<form name="formLinks" method="post">
				<div class='inp_wrapper'>
				<label for="linkLink">Link Url</label>
				<input type="text" name="linkLink" id="linkLink" autocomplete="off"/></div>
				<div class='inp_wrapper'>
				<label for="linkName">Link Name</label>
				<input type="text" name="linkName" id="linkName" autocomplete="off"/>
				</div>
				<div class='inp_wrapper'>				
				<label for="linkFolder">Select Folder</label>
				<select name="linkFolder" id="linkFolder">
				<?php echo HTML::getSelectItems('folderGroupped');?>
				</select>
				</div>				
				<div class='inp_wrapper'>
				<label for="tagsSimple">Tags (comma-separated)</label>		
				<input name="tagsSimple" id="tagsSimple" type="text" autocomplete="off" />
				</div>
				<br/>
				<input type="hidden" name="action" value="link">
				<input id="link_submit" type="submit" value="Send"/>

				</form>	

				</fieldset>	
				</div>
				<!-- ---------------- View Pitstop ------------------------ -->
						<div id="llinkView" class="tab-pane fade">
				<fieldset>
				<form name="formLinkView" method="post">

				</form>
				</fieldset>	
						</div>

					</div>
			</div>
        </div>
		<div id="sectionD" class="tab-pane fade">
            <p>
			<table class="table table-striped table-hover table-condensed small">
			</table>
			<?php $tagStyle = Settings::Val('tagsInputStyle');
				$pill = ''; $simpl = '';
				if($tagStyle=='pillbox'){$pill='checked';}elseif($tagStyle=='simple'){$simpl='checked';}
			?>
				<form name="formSettings" method="post">					
				<div class="form-group">
				<label class="control-label col-xs-3">tagsInputStyle:</label>
				<div class="col-xs-2">
				<label class="radio-inline">
				<input type="radio" name="tagsInputStyleRad" value="pillbox" <?=$pill?>> Pillbox
				</label>
				</div>
				<div class="col-xs-2">
				<label class="radio-inline">
				<input type="radio" name="tagsInputStyleRad" value="simple" <?=$simpl?>> Simple<br/>(for old browsers)
				</label>
				</div>
				</div>
				<input type="submit" value="Send"/>
				<input type="hidden" name="action" value="tagsInputStyle">
				</form>
			</p>
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