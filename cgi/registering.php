<?php
<<<<<<< HEAD
namespace LinkBox;

include_once 'utils.inc.php';
include_once 'settings.inc.php';
include_once 'database.class.php';
include_once 'entities.class.php';

if (isset($_POST['reg_submit']))
{
	
	$regdate = time(); // UNIXTIME
	$name = Utils::cleanInput($_POST['reg_name']);
	$email = Utils::cleanInput($_POST['reg_email']);
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
	die();
/*	if(false === strpos($url, 'http'))
		$url = 'http://'.$url;
*/
	$db = DataBase::getDB();
=======
include_once 'settings.inc.php';
include 'database.class.php';

if (isset($_POST['reg_submit']))
{

	$curdate = date('Y-m-d');
	$url = $_POST['link'];
	
	if(false === strpos($url, 'http'))
		$url = 'http://'.$url;

	$db = DataBase::getDB();
	$lnk = new Link();
	$lnk->fields['IP'] = $_SERVER['REMOTE_ADDR'];
	$lnk->fields['link'] = $_POST['link'];
	$lnk->fields['name'] = $_POST['descr'];
>>>>>>> 6b0274c5dfa49ba30bca5e71fad81824d6d837a6
	
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
<<<<<<< HEAD
	echo'<PRE>';
	//print_r($_SERVER);
	print_r($_POST);
	header('Location: register.html');
=======
	//echo'<PRE>';
	//print_r($_SERVER);
	//header('Location: register.html');
>>>>>>> 6b0274c5dfa49ba30bca5e71fad81824d6d837a6
	//echo SITE_ROOT;
	header('Location: http://'.$_SERVER['HTTP_HOST'].'/'.SITE_ROOT.'/'.'register.html');	
	//header('Location: '.'register.html');	
	die();
}
?>