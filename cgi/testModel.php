<?php
namespace LinkBox;

include_once 'utils.inc.php';
include_once 'settings.inc.php';
include_once 'database.class.php';
include_once 'entities.class.php';

$error_msg='';
$succ_msg='';

if (isset($_POST['btn_test']))
{
	
	$regdate = time(); // UNIXTIME
	$name = Utils::cleanInput($_POST['user_name']);
	$email = Utils::cleanInput($_POST['user_email']);
	$num = Utils::cleanInput($_POST['user_num']);
	try{
		$test = new Test($regdate, $name, $email, $num);
		//var_dump($test);
		//$succ_msg='User validated';
		//$db = DataBase::getDB();
		//$db->saveUser($user);
		$succ_msg = $test->PreviewSQL();
		if(empty($succ_msg)) $error_msg = 'not working';
		//echo "status:".$test->GetDataBaseStatus();
		//$cnt = $test->Count();
		$cnt = $test->Save();
		//echo"<br/>".var_dump($cnt);
		if($cnt === -1) $error_msg = 'Not saved'.$test->errormsg;
		echo $test->__toString();

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
	

}elseif(isset($_POST['btn_edit'])){
	$name = Utils::cleanInput($_POST['user_name']);
	$email = Utils::cleanInput($_POST['user_email']);
	$num = Utils::cleanInput($_POST['user_num']);
	
	$test = Test::LoadByID(20);
	
}
?>
<head>
<meta charset="utf-8"/>
<title>LinkBox - Test Model page</title>
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
	<div>New Test model:</div><br/>
	<div class="sick-input small">
		<label for="user_name" class="small2">Input name</label>
		<input id="user_name" name="user_name" autocomplete="off" required>
	</div>
	<div class="sick-input small">
		<label for="user_email" class="small2">Input email</label>
		<input id="user_email" name="user_email" type = "email" autocomplete="off">
	</div>
	<div class="sick-input small">
		<label for="user_num" class="small2">Input number</label>
		<input id="user_num" name="user_num" type="text" autocomplete="off">
	</div>
<input id="test_submit" class="btn_submit" name="btn_test" type="submit" value="Add Test">			
		</form>
<?if(!empty($test->id)){?>
<form id="form_test_edit" method="post" action="" name="frm_edit">
	<div>Edit Test model:</div><br/>
	<div class="sick-input small">
	<input id="user_id" name="user_id" hidden value="<?=$test->id?>">
		<label for="user_name" class="small2">Input name</label>
		<input id="user_name" name="user_name" autocomplete="off" required value="<?=$test->name?>">
	</div>
	<div class="sick-input small">
		<label for="user_email" class="small2">Input email</label>
		<input id="user_email" name="user_email" type = "email" autocomplete="off" value="<?=$test->email?>">
	</div>
	<div class="sick-input small">
		<label for="user_num" class="small2">Input number</label>
		<input id="user_num" name="user_num" type="text" autocomplete="off" value="<?=$test->number?>">
	</div>
<input id="edit_submit" class="btn_submit" name="btn_edit" type="submit" value="Edit Test">			
	</form>
	<?}?>
</body>