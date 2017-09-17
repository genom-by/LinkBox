<?php
//==========================================
// Application class
// ver 1.0
// © genom_by
// last updated 28 jun 2017
//==========================================

namespace lbx;

use PDOException;
use LinkBox\Logger as Logger;

include_once 'auth.inc.php';
include_once 'settings.inc.php';

class App{
	public static $errormsg;	//error(s) when executing
	
	private $db;	// database connection
	protected static $availablePages = array('login', 'logout', 'register',
						'linkbox', 'customize', 'profile', 'settings', 'howto');
	protected static $urlHead = 'http://';
	
	
	/* SETTINGS:
	http://".$_SERVER['HTTP_HOST'].'/'.SITE_ROOT."/".SITE_STARTPAGE
	define(SITE_ROOT, 'tt/obus');
	define(SITE_STARTPAGE, 'cgi/hchartLine.php');
	define(SITE_DIR, 'D:\Denwer3.5\home\localhost\www\tt\obus'); */
	
	public static function link($page){
		$p404 = self::$urlHead.$_SERVER['HTTP_HOST'].'/'.SITE_ROOT.'/cgi/404.php';
		if( empty($page) ) return $p404;
		if( ! in_array($page, self::$availablePages ) ){
			return $p404;
		}else{
			$mainBody = self::$urlHead.$_SERVER['HTTP_HOST'].'/'.SITE_ROOT.'/';
			switch($page){
				case 'login': $link = $mainBody.'loginpage.php';	break;
				case 'logout': $link = $mainBody.'loginpage.php?action=logout';	break;
				case 'register': $link = $mainBody.'registerpage.php';	break;
				case 'linkbox': $link = $mainBody.'index.php';	break;
				case 'customize': $link = $mainBody.'customize.php';	break;
				case 'profile': $link = $mainBody.'cgi/profile.php';	break;
				case 'settings': $link = $mainBody.'cgi/settings.php';	break;
				case 'howto': $link = $mainBody.'cgi/howto.php';	break;
			default:
				$link = $p404;
			}
			return $link;
		}
	}
	
	public static function currentPage(){
		
		$pos = strrpos($_SERVER['SCRIPT_NAME'], '/');
		$filename = substr( $_SERVER['SCRIPT_NAME'], ++$pos );
		//echo $filename;
		switch ( $filename ){
				
			case 'loginpage.php': $link = 'login'; break;
			case 'registerpage.php': $link = 'register'; break;
			case 'index.php': $link = 'linkbox';	break;
			case 'customize.php': $link = 'customize'; break;
			case 'profile.php': $link = 'profile'; break;
			case 'settings.php': $link = 'settings'; break;
			case 'howto.php': $link = 'howto'; break;
		default:
			$link = '404';			
		}
		return $link;		
	}

}//class App