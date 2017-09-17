<?php
namespace lbx;

include_once 'utils.inc.php';
include_once 'auth.class.php';
include_once 'dbObjects.class.php';
include_once 'app.class.php';
include_once 'HTMLroutines.class.php';

//\LinkBox\Logger::log('Start logging');
//var_dump($_POST);
$retval = '';
if(!empty($_POST['action'])){
		
		//TODO
		/*
		https://github.com/delight-im/PHP-Auth
		http://phppot.com/php/php-login-script-with-remember-me/
		https://github.com/PHPAuth/PHPAuth/blob/master/Auth.php
		*/
		
switch ($_POST['action']){
	case 'login':
		//echo 'obus';		
		if( ( empty($_POST['userName']) ) AND ( empty($_POST['userEmail']) ) ){
			$actionStatus = 'error';
			$message = 'User name and/or email not provided.';	
		}else{
			if(User::isThereSameUser($_POST['userName'], $_POST['userEmail'])){
				$user = User::getUserbyNameOrEmail($_POST['userName'], $_POST['userEmail']);
				if(!$user){
				//print_r("result: ".$user::$errormsg);
					$actionStatus = 'error';
					$message = 'Error obtaining user: '.User::$errormsg;
				}else{
				/*session_start();
				$_SESSION["user_id"] = $user->id;
				$_SESSION["user_name"] = $user->name;*/
					$res = Auth::loginUser($user, $_POST['inputPWD'], $_POST['inputRemember']);
					if($res == true){
						$locate = App::link('linkbox');
						header("Location: {$locate}");
						die();
					}else{
						$actionStatus = 'error';
						$message = 'Password not valid';					
					}
				}
			}else{
				$actionStatus = 'error';
				$message = 'No such user';			
			}
//TODO sql not unique password field		
		}
		break;
	case 'register':
		//echo 'staaation';
		if(!empty($_POST['userName'])){
			$newuser = new User($_POST['userName'], $_POST['userEmail'], $_POST['inputPWD']);
			$retval = $newuser->save();
			if(!$retval){
				//print_r("result: ".User::$errormsg);
				//\LinkBox\Logger::log("{$_POST['action']} error: ".$station::$errormsg);
				$actionStatus = 'error';
				$message = 'Could not register new user: '.$newuser->errormsg;
			}else{
				$actionStatus = 'success';
				$message = "Congratulations! You were registered. Now you can <a class='notifylink' href='".App::link('login')."'>log in</a> to proceed with app.";			
			}
		}else{
			$actionStatus = 'error';
			$message = 'User credentials not provided.';		
		}
		break;
	case 'logout':
		//echo 'itinerary';
		session_destroy();
		Auth::logout();
		/*if(!empty($_POST['itineraryName'])){
			session_destroy();
		}*/			
	break;
	case 'remember':
				//print_r($_POST);
			if( !empty($_POST['itinerarySelect']) ){
				$way = new Way($_POST);
				$retval = $way->save($_POST);
				if(!$retval)
					//print_r("result: ".way::$errormsg);	
					$actionStatus = 'error';
					$message = $way->errormsg;				
			}
	break;
	default:
		$retval = 'not dispatched user action';
	}
}
//echo( 'action:'.$_POST['action'] );
//parse_str($_POST["lbx_form_addlink"], $ajax);
//print_r($ajax);
if(!empty($_GET['action'])){
	switch ($_GET['action']){
		case 'logout':
			Auth::logout();
			$locate = App::link('login');
			header("Location: {$locate}");
		break;
	}
}