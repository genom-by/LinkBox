<?php
namespace lbx;

include_once 'auth.inc.php';
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

if( !empty($_POST['action']) ){
	if( Auth::notLogged() ){
		break;
	}
	switch ($_POST['action']){
		
		case 'addLinkMP':
		
			if( empty($_POST['link_to_save']) OR empty($_POST['link_description']) ){
				$err = 'Empty url/description provided';			
				\LinkBox\Logger::log("create:linkMP error: ".$err);
				$res = false;
				break;
			}
			if( empty($_POST['link_Folder']) ){
				$err = 'No folder provided';			
				\LinkBox\Logger::log("create:linkMP error: ".$err);
				$res = false;
				break;
			}
			if(!empty($_POST['link_to_save'])){
				$link = new Link($_POST['link_to_save'], $_POST['link_description'], $_POST['link_Folder'], $_POST['lbx_tagsSelected'] );
				$res = $link->save();
				if(!$res){
					$err = 'Could not save link: '.$link->errormsg;			
					\LinkBox\Logger::log("create:linkMP error: ".$err);
					$res = false;
				}else{
					$res = true;
					$err = 'Link was saved successfully!';
				}
			}	
		break;
		
		default:
		$res = false;
		$err = 'No object to create was provided';
	}	// - switch ($_POST['action'])
	
	$message = $err;
	if($res === false){
		$actionStatus = 'error';		
	}
	else{
		$actionStatus = 'success';	
	}
	//\LinkBox\Logger::log("status: {$actionStatus} and msg: {$message}");
}
?>