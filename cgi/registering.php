<?php
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
	//echo'<PRE>';
	//print_r($_SERVER);
	//header('Location: register.html');
	//echo SITE_ROOT;
	header('Location: http://'.$_SERVER['HTTP_HOST'].'/'.SITE_ROOT.'/'.'register.html');	
	//header('Location: '.'register.html');	
	die();
}
?>