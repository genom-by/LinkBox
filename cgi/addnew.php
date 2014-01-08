<?php
namespace LinkBox;

include_once 'utils.inc.php';
include_once 'settings.inc.php';
include_once 'database.class.php';
include_once 'entities.class.php';

if (isset($_POST['new_ahref_btn']))
{
	
	$adddate = time(); // UNIXTIME
	$link = Utils::cleanInput($_POST['new_ahref']);
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
*/
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
}
else{
	header("Expires: 0");
	echo'<PRE>';
	//print_r($_SERVER);
	print_r($_POST);
	header('Location: register.html');
	//echo SITE_ROOT;
	header('Location: http://'.$_SERVER['HTTP_HOST'].'/'.SITE_ROOT.'/'.'register.html');	
	//header('Location: '.'register.html');	
	die();
}
?>