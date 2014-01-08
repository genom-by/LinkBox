<?php
namespace LinkBox;

include_once 'utils.inc.php';
include_once 'settings.inc.php';
include_once 'database.class.php';
include_once 'entities.class.php';

$error_msg='';
$succ_msg='';

if (isset($_POST['btn_new_user']))
{
	
	$regdate = time(); // UNIXTIME
	$name = Utils::cleanInput($_POST['user_name']);
	$email = Utils::cleanInput($_POST['user_email']);
	$pass = Utils::cleanInput($_POST['user_pwd']);
	try{
		$user = new User($regdate, $name, $email, $pass);
		$succ_msg = $user->PreviewSQL();
		if(empty($succ_msg)) $error_msg = 'not working';
		$cnt = intval($user->Count());
		$succ_msg = "Users count is {$cnt} items.";
		if(empty($succ_msg)) $error_msg = 'not count working';
		/*
		$succ_msg='User validated';
		$db = DataBase::getDB();
		$db->saveUser($user);
		*/
	}catch(\Exception $e){
		$error_msg = $e->getMessage();
		Logger::log('Error handling User - '.$error_msg);
		//header('Location: '.SITE_ROOT.'/'.'test.php');
		//header('Location: '.$_SERVER['PHP_SELF']);
		//exit;	
	}
	/*echo '<pre>';
	var_dump($user);
	echo '</pre>';*/
	if(empty($user)){
		//Logger::log('Empty User');
	}
	//echo 'here';
	
/*	
	echo 'addate- '.$adddate;
	echo '<br/>';
	echo 'name- '.$_POST['user_name'];
	echo '<br/>';
	echo 'email- '.$_POST['user_email'];
	echo '<br/>';
	echo 'pwd- '.$_POST['user_pwd'];
	echo '<br/>';
	echo 'work w/ email';
	$emUP = strtoupper($_POST['user_email']);
	echo 'pwdUP- '.$emUP;
	echo '<br/>';
	echo 'work w/ digts';
	$emUP = intval($_POST['user_name']);
	echo 'intval*2- '.$emUP*2;
*/


	//$link = Utils::cleanInput($_POST['new_ahref']);
	/*$email = Utils::cleanInput($_POST['reg_email']);
	$pass = Utils::cleanInput($_POST['reg_pass']);
	$user = new User($regdate, $name, $email, $pass);
	$logsucc = false;
	if (is_null($user)){
		$logsucc = Logger::log('user not created');
		}else{
			$logsucc = Logger::log($user);
		}
	echo '<pre>';
	//var_dump($user);
	if (! $logsucc) { 
		echo Logger::$last_error;
		echo Logger::$logfile;
	}
	echo '</pre>';
	die();*/
/*	if(false === strpos($url, 'http'))
		$url = 'http://'.$url;

	$db = DataBase::getDB();
	
	if (! $db->saveLink($lnk) ){
	echo "<p>some error: {$db->errormsg}</p>";
	}
	// if(!headers_sent())
	// {
		// header('Location: '.$_SERVER['SCRIPT_NAME']);
		// exit;
	// }
	header('Location: '.SITE_ROOT.'/'.'register.html');
	exit;
*/
}
?>
<head>
<meta charset="utf-8"/>
<title>LinkBox - Test page</title>
<!--<link type="text/css" rel="stylesheet" href="../css/common.css"/>-->
<script type="text/javascript" src="../js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="../js/main.js"></script>
<style type="text/css">
.msg{
padding:5px;
border:2px solid #8B4513;
font:13px tahoma, verdana, geneva, sans-serif;
}
.succ{
background:#3CB371;
}
.err{
background:#FFC0CB;
}
</style>
</head>
<body>
<?
if(!empty($error_msg)){?>
<div class='err msg'>
<?=$error_msg?>
</div>
<?}if(!empty($succ_msg)){?>
<div class='succ msg'>
<?=$succ_msg?>
</div>
<?}
?>
<form id="form_test" method="post" action="">
	<div>New User:</div><br/>
	<div class="sick-input small">
		<label for="user_name" class="small2">Input name</label>
		<input id="user_name" name="user_name" autocomplete="off">
	</div>
	<div class="sick-input small">
		<label for="user_email" class="small2">Input email</label>
		<input id="user_email" name="user_email" type = "email" autocomplete="off">
	</div>
	<div class="sick-input small">
		<label for="user_pwd" class="small2">Input password</label>
		<input id="user_pwd" name="user_pwd" type="password" autocomplete="off">
	</div>
<input id="addnew_submit" class="btn_submit" name="btn_new_user" type="submit" value="Add User">			
		</form>
</body>