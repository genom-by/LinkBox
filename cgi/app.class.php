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
	protected static $urlHead = 'http://';	//http
	
	//all available pages:: pageName => path_to_file
	protected static $availablePages = array(
	'login'		=> 'loginpage.php',
	'logout' 	=> 'loginpage.php?action=logout',
	'register'	=> 'registerpage.php',
	'linkbox' 	=> 'index.php',
	'customize' => 'customize.php',
	'options' 	=> 'options.php',
	'profile' 	=> 'cgi/profile.php',
	'settings' 	=> 'cgi/settings.php',
	'howto' 	=> 'cgi/howto.php'
	);	
	
	//top menu pages (except Index):: pageName => menuCaption
	protected static $topMenuPages = array(
	'customize'	=>'Customize',
	'options'	=>'Options',
	'profile'	=>'Profile',
	'howto'		=>'How to use'
	);

	public static function getTopMenuPagesArr(){
		return self::$topMenuPages;
	}
	/* SETTINGS:				http://".$_SERVER['HTTP_HOST'].'/'.SITE_ROOT."/".SITE_STARTPAGE
	define(SITE_ROOT, 'tt/obus');		define(SITE_STARTPAGE, 'cgi/hchartLine.php');	define(SITE_DIR, 'D:\Denwer3.5\home\localhost\www\tt\obus'); */
		
	public static function link($page){
		$p404 = self::$urlHead.$_SERVER['HTTP_HOST'].'/'.SITE_ROOT.'/cgi/404.php';
		if( empty($page) ) return $p404;

		if( ! array_key_exists($page, self::$availablePages ) ){
			Logger::log('required page doesn\'t exists: '.$page);
			return $p404;
		}else{
			$mainBody = self::$urlHead.$_SERVER['HTTP_HOST'].'/'.SITE_ROOT.'/';
			$link = $mainBody.self::$availablePages[$page];		##refactored 01-1 	//Logger::log('pointed to page '.$link);
			return $link;
		}
	}
	
	public static function currentPage(){

		$pos2 = strpos($_SERVER['SCRIPT_NAME'], SITE_ROOT);
		$filename2 = substr( $_SERVER['SCRIPT_NAME'], $pos2+strlen(SITE_ROOT)+1 );
		$link = array_search($filename2, self::$availablePages);	## refactored 01-2

		if( $link === false ){
			return '404';
		}else{
			return $link;			
		}		
	}

}//class App ==============================================================================================================

// refactored code
## 01-1
		//$pos = strrpos($_SERVER['SCRIPT_NAME'], '/');
		//$filename = substr( $_SERVER['SCRIPT_NAME'], ++$pos );
//if( ! in_array($page, self::$availablePages ) ){
	/*switch($page){
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
	}*/	
## end

##01-2
		/*switch ( $filename ){
				
			case 'loginpage.php': $link = 'login'; break;
			case 'registerpage.php': $link = 'register'; break;
			case 'index.php': $link = 'linkbox';	break;
			case 'customize.php': $link = 'customize'; break;
			case 'profile.php': $link = 'profile'; break;
			case 'settings.php': $link = 'settings'; break;
			case 'howto.php': $link = 'howto'; break;
		default:
			$link = '404';			
		}*/
##end