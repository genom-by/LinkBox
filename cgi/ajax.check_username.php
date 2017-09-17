<?php
namespace lbx;

//==========================================
// ajax checkings vor frontend validations
// ver 1.0
// © genom_by
// last updated 3 dec 2015
//==========================================


include_once 'utils.inc.php';
include_once 'dbObjects.class.php';

use LinkBox\Logger as Logger;
use LinkBox\Utils as Utils;
//print_r($_GET);
//parse_str($_POST["lbx_registerForm"], $ajax);
//print_r($ajax);
/*
Logger::log('POSTname:'.$_POST['inputName']);
Logger::log('POSTemail:'.$_POST['inputEmail']);
Logger::log('val_type:'.$_GET['val_type']);
*/


ob_start();
Logger::log('ajax:'.$_GET['val_type'].' for user '.$_POST['userName'].' and email '.$_POST['userEmail']);
$goodForRegister = null; // true - good for using. false - validation will fail
$ret_val = false;

switch( Utils::cleanInput($_GET['val_type']) ){

	case "name":
	try{
		$ret_val = User::isThereSameUser(Utils::cleanInput($_POST['userName']),"");
		}catch(\Exception $e){$ret_val = null;
			Logger::log('exception getting userdata:'.$e->getMessage());}
		if ( $ret_val === false ) {
			$goodForRegister = true;
		}else{
			$goodForRegister = false;		
		}
		break;
	case "email":
	try{	
		$ret_val = User::isThereSameUser("", Utils::cleanInput($_POST['userEmail']));
		}catch(\Exception $e){$ret_val = null;
			Logger::log('exception getting userdata:'.$e->getMessage());}		
		if ( $ret_val === false ) {
			$goodForRegister = true;
		}else{
			$goodForRegister = false;	
		}
		break;
	default:
		$goodForRegister = false;
}
if(is_null($goodForRegister)){
	Logger::log('Error attempting the same user:'.User::$errormsg);	
	$goodForRegister = false;
}
sleep(1);

ob_end_clean();
//Logger::log('$goodForRegister = '.$goodForRegister ? 'true':'false');
header('Content-type: application/json');

//=========


//Logger::log("reply sent for user [{$_POST['inputName']}] and mail [{$_POST['inputEmail']}]:".$goodForRegister);	
echo(json_encode($goodForRegister)); // true - good for using. false - validation will fail
//	echo(json_encode(true)); // testing always true


//User::isThereSameUser("user12","1v@v.v");

die();
//=========