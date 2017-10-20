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
<title>Customize personal account</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
<script>
function getSelectedText(elementId) {
    var elt = document.getElementById(elementId);

    if (elt.selectedIndex == -1)
        return null;

    return elt.options[elt.selectedIndex].text;
}

/*
function itinerarySelect_onChange(){
	$('#submitNewPitstop').prop('disabled', false);	
}*/
function btn_showtrans_onClick(){
	var transblock = document.getElementById("folderblock");
	//if(transblock)
}
function onTestPOSToperation(){

}
function btnDelFromTable(table_, id_entry){
	console.info("delete from table: "+table_+" entry id:"+id_entry);
	action_ = 'delete';
	console.info('data to send:', {action:action_ , id:id_entry, table:table_});
	
	$.post(
		"cgi/post.routines.php",
		{action:action_ ,id:id_entry, table:table_},
		function(data){
		console.log("post returned: "+data.result);
		//alert(data.result);
		if (data.result == 'ok' ){
			var domID = '#'+table_+'_id_'+id_entry;
			$(domID).toggle( "highlight" );
		}else{
			console.log('error message: ',data.message);
			alert(data.message);
		}
		}
		,"json"
	);	
}
/* save edited row
*/
function btnSaveTableItem(table_, id_entry){
	action_ = 'objectUpdate';
	console.info('data to send:', {action:action_ , id:id_entry, table:table_});
var rowid = table_ + '_id_' + id_entry;
var cssFtxt = '#' + rowid + ' input';
	var els = $(cssFtxt);
	//console.log(els);
var objArray = {};
	//toggleEditControls(rowid, true);
	
	$.each($(els),function(ind,el){

var orm = el.getAttribute("orm")+'2';

	objArray[orm]=el.value;
				//newInput = mountNewInput($(el));
                //$(el).html("");
                //$(el).append(newInput);
		//console.log(objArray['name']);
	})

	data_ = JSON.stringify(objArray);

	$.post(
		"cgi/post.routines.php",
		{action:action_ ,id:id_entry, table:table_, data: data_},
		function(data){
		console.log("post returned: "+data.result);
		alert(data.result);
		if (data.result == 'ok' ){
			var domID_td = '#'+table_+'_id_'+id_entry+' td.rowtxt';
			var els2 = $(domID_td);
				$.each($(els2),function(ind,el3){
					$(el3).html( objArray[el3.getAttribute("orm")+'2'] );
				})
			//$(domID_td).html( objArray['name2'] );
			toggleEditControls(rowid, false);
		}else{
			console.log('error message: ',data.message);
			alert(data.message);
		}
		}
		,"json"
	);	
}
/* edit entry
*/
function btnEditTableItem(table_, id_entry){
	//console.info("edit into table: "+table_+" entry id:"+id_entry);
	action_ = 'edit';
	console.info('data to send:', {action:action_ , id:id_entry, table:table_});
var rowid = table_ + '_id_' + id_entry;
var cssFtxt = '#' + rowid + ' td.rowtxt';
	var els = $(cssFtxt);
	//console.log(els);

	toggleEditControls(rowid, true);
	
	$.each($(els),function(ind,el){
	//console.log(ind, el);
				newInput = mountNewInput($(el));
                $(el).html("");
                $(el).append(newInput);
	})
}
/* cancel editing entry
*/
function btnCancelTableItem(table_, id_entry){
	
	action_ = 'cancel';
	//console.info('data to send:', {action:action_ , id:id_entry, table:table_});
var rowid = table_ + '_id_' + id_entry;
var cssFtxt = '#' + rowid + ' td.rowtxt input';
	var els = $(cssFtxt);

	toggleEditControls(rowid, false);
	
	$.each($(els),function(ind,el){
		var oldVal = el.getAttribute("valInit");
                $(el).parent().html(oldVal);
                //$(el).append(newInput);
	})
}

// sample edit grid
function mountNewInput(cell) {
	
	var element = document.createElement("input");
	//get string in attribute ref
	/*var attrsString = $(cell).attr("ref");
	if(attrsString != null){
		//split attributes
		var attrsArray = attrsString.split(",");

		var currentObj;
		for(n=0; n < attrsArray.length; n++){
			//separate name of attribute and value attribute
			currentObj = attrsArray[n].split(":");
			$(element).attr($.trim(currentObj[0]), $.trim(currentObj[1]));
		}
	}else{
		indexCell = $(cell).parent().children().index($(cell));
		element.setAttribute("name", "column_"+indexCell);
		element.setAttribute("type", "text");
	}*/
	var DB_column_name = $(cell).attr("orm");
	
	element.setAttribute("orm", DB_column_name);
	element.setAttribute("value", $(cell).text());
	element.setAttribute("valInit", $(cell).text());
	//element.setAttribute("style", "width:" + $(cell).width() + "px");
	element.setAttribute("size", "8");
	$(element).addClass("edit_from_te");
	return element;
}

// oleg_
function toggleEditControls(rowid, to_dsc) {
/*var rowid = table + '_id_' + id;
var cssFtxt = '#' + rowid + ' td.txt';*/
var cssFed = '#' + rowid + ' .btnEditBl';
var cssFdsc = '#' + rowid + ' .btnDSCBl';
//console.log(cssFdsc, cssFed);

	if (true === to_dsc) {
//console.log('to_dsc:'+to_dsc);	
		$(cssFed).hide();
		$(cssFdsc).show();
        //$(".addnewbtn").hide();
	} else {
		$(cssFed).show();
		$(cssFdsc).hide();
        //$(".addnewbtn").show();
	}	
}
/*	delete pitstops for selected itinerary
*/
function btn_del_pits_stats_onClick(){
	
	if (! confirm('Are you sure to delete all stations for itinerary?') ) {return;}
	//console.info("delete from table: "+table_+" entry id:"+id_entry);
	var id_entry = $('#itinerarySelectEdit').val();
	console.info('data to send:', {id:id_entry, table:'itin_pitstops_delete'});
	
	$.post(
		"post.routines.php",
		{action:'delete', id:id_entry, table:'itin_pitstops_delete'},
		function(data){
		console.log("post returned: "+data.result);
		alert(data.result);
		if (data.result == 'ok' ){
			var domID = '#pitEditContent';
			$(domID).html( "" );
		}else{
			console.log('error message: ',data.message);
		}
		}
		,"json"
	);	
}

function selPitSeqEdit_onChange(action, itin_id){
	console.log("selected id"+itin_id+" and action is "+action);
	if(action=='pitstops'){
	table_='pits_PitEdit_table';
	divID='#pitEditContent';
	}else if(action=='pitstopsView'){
	table_='pits_PitView_table';
	divID='#pitViewContent';
	}else if(action=='sequencesEdit'){
	table_='seq_SeqEdit_table';
	divID='#seqEditContent';
	}else if(action=='sequencesView'){
	table_='seq_SeqView_table';
	divID='#seqViewContent';
	}else{table:'no_table'; return false;}

	$.post(
		"post.routines.php",
		{action: 'pageUpdate', id:itin_id, table:table_},
		function(data){
			console.log("post returned: "+data.result);
		if (data.result == 'ok' ){
			//console.log(data.payload);
			$(divID).html(data.payload);
		}else{
			console.log('error message: ',data.message);
			$(divID).html(data.message);			
		}
		}
		,"json"
	);
}

//SERIALIZE
function serialize(_obj)
{
   // Let Gecko browsers do this the easy way
   if (typeof _obj.toSource !== 'undefined' && typeof _obj.callee === 'undefined')
   {
      return _obj.toSource();
   }
   // Other browsers must do it the hard way
   switch (typeof _obj)
   {
      // numbers, booleans, and functions are trivial:
      // just return the object itself since its default .toString()
      // gives us exactly what we want
      case 'number':
      case 'boolean':
      case 'function':
         return _obj;
         break;

      // for JSON format, strings need to be wrapped in quotes
      case 'string':
         return '\'' + _obj + '\'';
         break;

      case 'object':
         var str;
         if (_obj.constructor === Array || typeof _obj.callee !== 'undefined')
         {
            str = '[';
            var i, len = _obj.length;
            for (i = 0; i < len-1; i++) { str += serialize(_obj[i]) + ','; }
            str += serialize(_obj[i]) + ']';
         }
         else
         {
            str = '{';
            var key;
            for (key in _obj) { str += key + ':' + serialize(_obj[key]) + ','; }
            str = str.replace(/\,$/, '') + '}';
         }
         return str;
         break;

      default:
         return 'UNKNOWN';
         break;
   }
}
//SERIALIZE
$(function(){
	$('form[name=formLinks]').submit(function(event){
		console.info('onSubmit link simple');
		var name = $('#linkName').val();
		var url = $('#linkLink').val();
		var folder = $('#linkFolder').val();
		var tag = $('#linkTag').val();
		console.info('data: ' + name + ' ' + url + ' ' + folder + ' ' + tag);
		var tmpstr = name+url;
		if( (tmpstr == '') || (folder == -1) ){
			event.preventDefault();
			return false;
		}
		console.log('sending..');
	});
})
<?php if( Auth::notLogged()){ ?>
$(function(){
$('fieldset').prop('disabled',true);
})
<?}else{?>
$('fieldset').prop('disabled',false);
<?}?>
</script>
<style>
.hided{
	display:none;
}
.hided2{
	visibility:hidden;
}
.table-condensed button {
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
.statName_send{
position:relative;
float:right;
width:20%;
}
.clearfix{
content:"";
display:block;
clear:both;
}
td.inp{
display:none;
}
.hid_inp{
	display:none;
}
.simpleUrl{
	text-decoration: underline;
}
img.simpleFav{
	width:24px;
	height:24px;
}
/* ... loading ...
.loading{
 margin: 10% auto;
 border-bottom: 6px solid #fff;
 border-left: 6px solid #fff;
 border-right: 6px solid #c30;
 border-top: 6px solid #c30;
 border-radius: 100%;
 height: 100px;
 width: 100px;
 -webkit-animation: spin .6s infinite linear;
 -moz-animation: spin .6s infinite linear;
 -ms-animation: spin .6s infinite linear;
 -o-animation: spin .6s infinite linear;
 animation: spin .6s infinite linear;
}
*/
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
		<div class="col-md-5">
			<div class="obus_destination">
<fieldset>
<legend>Tags</legend>
<form name="formTags" method="post">
<label for="tagName">Tag name</label>
<input type="text" name="tagName" id="tagName" autocomplete="off"/>
<input type="submit" value="Send"/>
<input type="hidden" name="action" value="tag"/>
<p>
</p>
<!--show existing-->
<button type="button" id="btn_showdest" data-toggle="collapse" data-target="#tagblock">Show Tags</button>
<div id="tagblock" class="collapse">
<table class="table table-striped table-hover table-condensed small">
<?php echo HTML::getTableItems('tag');?>
</table>
</div>
<!--end show existing-->
</form>
</fieldset>				
			</div>
		</div>	
		<div class="col-md-5"><div class="obus_numbers">
<fieldset>
<legend>Folders</legend>
<form name="form1" method="post">
<label for="folderParentName">Parent Folder Name</label>
<select name="folderParentName" id="folderParentName">
<?php echo HTML::getSelectItems('parentfolder');?>
</select>
<label for="isParent">Parent Folder</label>
<input type="checkbox" name="isParent" id="isParent" />
<label for="folderName">Folder Name</label>
<input type="text" name="folderName" id="folderName" autocomplete="off"/>
<input type="submit" value="Send"/>
<input type="hidden" name="action" value="folder">
<p>
</p>
<!--show existing-->
<button type="button" id="btn_showtrans" onclick="btn_showtrans_onClick();" data-toggle="collapse" data-target="#folderblock">Show folders</button>
<div id="folderblock" class="collapse">
<table class="table table-striped table-hover table-condensed small">
<?php echo HTML::getTableItems('folder');?>
</table>
</div>
<!--end show existing-->
</form>
</fieldset>	
		</div></div>
		<div class="col-md-2"><div class="obus_stations">
<!-- stations -->
<fieldset>
<legend>Favicon</legend>
<form name="form2" method="post" accept-charset="UTF-8">
<input type='text' name='urlfavicon'/>
<div><p><? echo('p:'.$fff) ?></p></div>
<div><p><? echo('title:'.$ttt) ?></p></div>
<div><img src='<?=$fff ?>'></img></div>
<input type='submit' value='favpath'/>
<input type="hidden" name="action" value="urlfavicon1">
</form>
</fieldset>		
		</div></div>
	</div>
</div>
<!-- -------------------------------------------- links --------------------------------------------  -->
<div class="container">
	<div class="row">
		<div class="col-md-8">
			<div class="linkbox_linkSimple">
<fieldset>
<legend>Simple Links</legend>
<form name="formLinks" method="post">
<label for="linkLink">Link Url</label>
<input type="text" name="linkLink" id="linkLink" autocomplete="off"/>
<label for="linkName">Link Name</label>
<input type="text" name="linkName" id="linkName" autocomplete="off"/>
<br/><p></p>
<label for="linkFolder">Select Folder</label>
<select name="linkFolder" id="linkFolder">
<?php echo HTML::getSelectItems('folder');?>
</select>
<label for="tagsSimple">Tags comma-separated</label>		
<input name="tagsSimple" id="tagsSimple" type="text" autocomplete="off" />
<input type="hidden" name="action" value="link">
<input id="link_submit" type="submit" value="Send"/>
<p>
</p>
</form>
<!--show existing-->
<button type="button" data-toggle="collapse" data-target="#linkblock">Show Links</button>
<div id="linkblock" class="collapse">
<table id="tbl_itineraries" class="table table-striped table-hover table-condensed small">
<?php echo HTML::getTableItems('link');?>
</table>
</div>
<!--end show existing-->
</fieldset>			
			</div>
		</div><!-- -----------------------------------sequences ------------------------------------ -->
		<div class="col-md-4">
			<div class="linkbox_linkTest">
<fieldset>
<legend>LinkTest</legend>
		<form name="LinkTest" method="post">
		<label for="linkName2">Link Name</label>
		<input type="text" name="linkName2" id="linkName2" autocomplete="off"/>
		<label for="linkLink2">Link Url</label>
		<input type="text" name="linkLink2" id="linkLink2" autocomplete="off"/>
		<br/><p></p>
		<select name="linkFolder2" id="linkFolder2">
		<?php echo HTML::getSelectItems('folder');?>
		</select>
		<label for="tagsCSV">Tags comma-separated</label>		
		<input name="tagsCSV" id="tagsCSV" type="text" autocomplete="off" />
		<input type="hidden" name="action" value="link2">
		<input id="link_submit2" type="submit" value="Send"/>
		<p>
		</p>
		</form>
</fieldset>				
			</div>
		</div>	
	</div>
</div><!-- itineraries end -->
<!-- ---------------------------------------------- pitstops ----------------   |   ----------------------------- sequenses -------------------------------------------    -->
	
<!-- ---------------------------------------------- pitstops -- end ---------   |   ----------------------------- sequenses --- end -----------------------------------    -->
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