<?php
namespace LinkBox;

include_once 'utils.inc.php';
include_once 'settings.inc.php';
include_once 'database.class.php';
include_once 'entities.class.php';

$error_msg='';
$succ_msg='';

if(isset($_POST['btn_load'])){

	$to_edit = Utils::cleanInput($_POST['test_id_opt']);
	echo 'test opt='.$to_edit;
try{	
	$test = Test::LoadByID($to_edit);
	
		if($test === null){
			$error_msg = 'user not found: ';
		}else{
			$succ_msg = 'user found';

			//var_dump($test);
		}
	}catch(\Exception $e){
		$error_msg = $e->getMessage();
		Logger::log('Error finding User - '.$error_msg);
		//header('Location: '.SITE_ROOT.'/'.'test.php');
		//header('Location: '.$_SERVER['PHP_SELF']);
		//exit;	
	}	
}elseif(isset($_POST['btn_edit'])){
	$to_edit = Utils::cleanInput($_POST['test_id_opt']);
	echo 'test opt='.$to_edit;
try{	
	//$test = new Test(null,null,null,null);
	$test = Test::LoadByID($to_edit);
if(is_null($test))
	$error_msg = 'Not loaded'.$test->errormsg;
	else{
	$test->name = Utils::cleanInput($_POST['user_name']);
	$test->email = Utils::cleanInput($_POST['user_email']);
	$test->number = Utils::cleanInput($_POST['user_num']);
//var_dump($test);
	$res = $test->Save();
		if($res === -1) $error_msg = 'Not saved'.$test->errormsg;
	}
	}catch(\Exception $e){
		$error_msg = $e->getMessage();
		Logger::log('Error saving User - '.$error_msg);
		//header('Location: '.SITE_ROOT.'/'.'test.php');
		//header('Location: '.$_SERVER['PHP_SELF']);
		//exit;	
	}	
}
else{
//main GET query

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
<form id="form_test_edit" method="post" action="" name="frm_edit">
   
   <p><select name="test_id_opt">
    <option disabled>Выберите героя</option>
    <option value="20">Чебурашка</option>
    <option value="21">Крокодил Гена</option>
    <option value="22">Шапокляк</option>
    <option value="23">Крыса Лариса</option>
   </select></p>
   
	<div>Edit Test model:</div><br/>
	<div class="sick-input small">
	<input id="user_id" name="user_id" hidden value="<?=$test->id?>">
		<label for="user_name" class="small2">Input name</label>
		<input id="user_name" name="user_name" autocomplete="off" value="<?=$test->name?>">
	</div>
	<div class="sick-input small">
		<label for="user_email" class="small2">Input email</label>
		<input id="user_email" name="user_email" type = "email" autocomplete="off" value="<?=$test->email?>">
	</div>
	<div class="sick-input small">
		<label for="user_num" class="small2">Input number</label>
		<input id="user_num" name="user_num" type="text" autocomplete="off" value="<?=$test->number?>">
	</div>
<input id="load_submit" class="btn_submit" name="btn_load" type="submit" value="Load Test">			
<input id="edit_submit" class="btn_submit" name="btn_edit" type="submit" value="Edit Test">			
	</form>
</body>