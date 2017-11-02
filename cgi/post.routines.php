<?php
namespace lbx;

include_once 'utils.inc.php';
include_once 'dbObjects.class.php';
include_once 'HTMLroutines.class.php';

function returnPOSTError($err_msg="unpredicted error"){
	//http://localhost/tt/obus/cgi/post.routines.php
	\LinkBox\Logger::log('POST Routines returned error');
	\LinkBox\Logger::log('POST array: '.serialize($_POST) );
	
	ob_end_clean();
	echo json_encode(array('result'=>'failed', 'message'=>$err_msg) );
	die();
}

function dispatchDelete($table, $id){

	$err = ''; //\LinkBox\Logger::log("table: {$table} id: {$id}") ;
	$res = false;
	
	switch($table){
		case 'folder':
		$res = DBObject::deleteEntry('folder', $id);
		break;
		case 'tag':
		$res = DBObject::deleteEntry('tags', $id, 'id_tag');
		break;
		case 'link':
		$res = DBObject::deleteEntry('link', $id, 'id_link');
		break;

	default:
		$res = false;
		$err = 'No table provided';
	}
	if($res === false){
	$err = 'DispatchDelete error: '.$err.'. '.DBObject::$errormsg;
	returnPOSTError($err);
	}
	return $res;
}
/* HTML / js requests processing
*/
function dispatchPageUpdate($table, $id=null){

	$err = ''; //\LinkBox\Logger::log("table: {$table} id: {$id}") ;

	switch($table){
		case 'link_folder':
			//$seqstats = sequencesStations::getSeqStatNamesBySequenceID($_POST['id']);
			//$links = HTML::getTableItems($_POST['id']);
			if($id=='all'){
				$links = HTML::getTableItems('linkMainPage');
			}else{
				$links = HTML::getTableItems('linkMainPage', $id);
			}
			if(false === $links){returnPOSTError('could not obtain links');die();}
			else{
				//$seqstats = HTML::getPitStopsEditRows($seqstats);
				echo json_encode(array('result'=>'ok', 'payload'=>$links) );
				die();
				}
		break;
	default:
		$res = false;
		$err = 'No table provided';
	}
	if($res === false){
	$err = 'DispatchPageUpdate error: '.$err.'. '.DBObject::$errormsg;
	returnPOSTError($err);
	}
	return $res;
}
/* update entities into DB
*/
function dispatchObjectUpdate($table, $id, $data){

	$err = ''; //\LinkBox\Logger::log("table: {$table} id: {$id}") ;
	$dataArr = json_decode($data, true);
	$des = serialize($dataArr);
	\LinkBox\Logger::log("json_decode: {$des}") ;
	switch($table){
		
		case 'folder':
			$folder = Folder::load( $id); //\LinkBox\Logger::log(serialize($obus) );
			$res = $folder->update(array('folderName'=>$dataArr['folderName2'], 'id_parentFolder'=>$dataArr['id_parentFolder2']));		
		break;
		case 'tag':
			$tag = Tag::load( $id); //\LinkBox\Logger::log(serialize($obus) );
			$res = $tag->update(array('tagName'=>$dataArr['tagName2']));		
		break;
		case 'seq_SeqEdit_table':
			//$seqstats = sequencesStations::getSeqStatNamesBySequenceID($_POST['id']);
			$seqstats = HTML::getSeqEditRows($_POST['id']);
			if(false === $seqstats){returnPOSTError('could not obtain sequences');die();}
			else{
				//$seqstats = HTML::getPitStopsEditRows($seqstats);
				echo json_encode(array('result'=>'ok', 'payload'=>$seqstats) );
				die();
				}
		break;
		case 'seq_SeqView_table':
			//$seqstats = sequencesStations::getSeqStatNamesBySequenceID($_POST['id']);
			$seqstats = HTML::getSeqViewRows($_POST['id']);
			if(false === $seqstats){returnPOSTError('could not obtain sequences');die();}
			else{
				//$seqstats = HTML::getPitStopsEditRows($seqstats);
				echo json_encode(array('result'=>'ok', 'payload'=>$seqstats) );
				die();
				}
		break;
		/*case 'way_pitstops':
			$way = Way::load( $id);
			if(false === $seqstats){returnPOSTError('could not obtain sequences');die();}
			else{
				//$seqstats = HTML::getPitStopsEditRows($seqstats);
				echo json_encode(array('result'=>'ok', 'payload'=>$seqstats) );
				die();
				}
		break;*/
	default:
		$res = false;
		$err = 'No table provided';
	}
	if($res === false){
	$err = 'DispatchObjectUpdate error: '.$err.'. '.DBObject::$errormsg;
	returnPOSTError($err);
	}
	return $res;
}

/* answer to inquires about entities in DB
*/
function dispatchInquire($table, $id, $question){

	$err = ''; //\LinkBox\Logger::log("table: {$table} id: {$id}") ;
	$res = true;
	//$dataArr = json_decode($data, true);
	//$des = serialize($dataArr);	\LinkBox\Logger::log("json_decode: {$des}") ;
	switch($question){
		
		case 'is_exists':
			$count = 0;
			if($table == 'pitstop'){
				$count = Way::GetPitsCountForItinerary($id);
			}elseif($table == 'sequence'){
				$count = sequencesStations::GetPitsCountForSequence($id);
			}
			if($count > 0){
				$res = true;
				echo json_encode(array('result'=>'ok', 'payload'=>$count ) );
				die();				
			}else{
				$res = true;
				echo json_encode(array('result'=>'false', 'message'=>"No records for id {$id}") );
				die();
			}
		break;
		case 'seq_SeqEdit_table':
			//$seqstats = sequencesStations::getSeqStatNamesBySequenceID($_POST['id']);
			$seqstats = HTML::getSeqEditRows($_POST['id']);
			if(false === $seqstats){returnPOSTError('could not obtain sequences');die();}
			else{
				//$seqstats = HTML::getPitStopsEditRows($seqstats);
				echo json_encode(array('result'=>'ok', 'payload'=>$seqstats) );
				die();
				}
		break;
		case 'pageTitle':
			//$seqstats = sequencesStations::getSeqStatNamesBySequenceID($_POST['id']);
			$title = LinkHandler::getSiteTitle($table);
			if(false === $title){returnPOSTError('no page title');die();}
			else{
				//$seqstats = HTML::getPitStopsEditRows($seqstats);
				echo json_encode(array('result'=>'ok', 'payload'=>$title) );
				die();
				}
		break;
	default:
		$res = false;
		$err = 'No inquire provided';
	}
	if($res === false){
	$err = 'DispatchInquire error: '.$err.'. '.DBObject::$errormsg;
	returnPOSTError($err);
	}
	return $res;
}

ob_start();
//\LinkBox\Logger::log('POST Routines file running');
//\LinkBox\Logger::log('POST : '.serialize($_POST) );

if(!empty($_POST['id'])){
	$res = false;
	$err = "unpredicted error";
	
if(! is_numeric($_POST['id'])){
	if($_POST['id'] != 'all'){
		$res = false;
		$err = "id should be numeric";
		returnPOSTError($err);
	}
}

	switch($_POST['action']){
		case 'objectUpdate':;
			$res = dispatchObjectUpdate($_POST['table'], $_POST['id'], $_POST['data']);
		break;
		
		case 'delete':
			$res = dispatchDelete($_POST['table'], $_POST['id']);
		break;
		
		case 'pageUpdate':
			$res = dispatchPageUpdate($_POST['table'], $_POST['id']);
		break;
		
		case 'inquire':
			$res = dispatchInquire($_POST['table'], $_POST['id'], $_POST['question']);
		break;
		
		case 'test':
			$res = true; //\LinkBox\Logger::log('here' );			
		break;
		
		default:
		$res=false;
		$err = "action not provided";
		returnPOSTError($err);		
	}

}else{
	$res = false;
	$err = "no entry id";
	returnPOSTError($err);
}
//	\LinkBox\Logger::log('json : '.json_encode(array('result'=>$res?'ok':'failed') ) );
ob_end_clean();
	echo json_encode(array('result'=>$res?'ok':'failed:'.DBObject::$errormsg) );


?>