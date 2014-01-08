<?php
namespace LinkBox;

include_once 'utils.inc.php';
include_once 'settings.inc.php';
include_once 'database.class.php';
include_once 'entities.class.php';

$ar=array('email'=>'r@r.r', 'name'=>'nnn', 'num'=>1);
$db = DataBase::getDB();
//echo 'saving.. '.$db->Insert('test', $ar);

$ar2=array('name'=>'ZZZ');
$ar2cond=array('id_test'=>array('19'=>'<='), 'num'=>array('123'=>'='));
echo 'updating.. '.$db->Update('test', $ar2, $ar2cond);

?>