<?php

//==========================================
// linkhandler class
// ver 1.0
// © genom_by
// last updated 15 sep 2017
//==========================================

namespace lbx;

use LinkBox\Utils as Utils;
use LinkBox\Logger as Logger;

include_once 'auth.inc.php';
include_once 'settings.inc.php';
include_once 'utils.inc.php';

class LinkHandler{
	public static $errormsg;	//error(s) when executing
	
/* =========================================================================================================================
*/	
	public static function wrapUrl($url){
		if(empty($url)){return "";}
		$url = trim($url);		
		$proto = self::getProto($url);
		if( empty($proto) ){$wrapped='http://'.$url;}else{$wrapped=$url;}
		return $wrapped;
	}
	public static function getFavicon($url){
		//$fav = file_get
	}
	public static function getFaviconHref($url){
		if(empty($url)){return false;}
		$url = Utils::cleanInput($url);
		$domain = self::getDomain($url);
		$proto = self::getProto($url);
		if( empty($domain) OR empty($proto) ){
			self::$errormsg = 'empty proto/path';
			Logger::log(self::$errormsg);
			return false;}
		$favPath = $proto."://".$domain."/favicon.ico";
		return $favPath;
	}
	public static function getDomain($url){
		if(empty($url)){return false;}
		$url = trim($url);
		$pp = strpos($url,'://');
		if( $pp !== false ){
			$plainUrl = substr($url, $pp+3);
		}else{
			$plainUrl = $url;
		}
		$fslp = strpos($plainUrl,'/');
		if( $fslp !== false ){
			$domain = substr($plainUrl, 0, $fslp);
		}else{
			$domain = $plainUrl;
		}

		return $domain;
	}
	
	public static function getProto($url){
		if(empty($url)){return false;}
		$url = trim($url);
		$proto = strstr($url,'://',true);
		if( empty($proto) ){return '';}else return $proto;
	}
	
	public static function getSiteTitle($url){
		if(empty($url)){return false;}
		$title = '';
		$dom = new \DOMDocument();
		
		@$pageContent = file_get_contents($url);
		if($pageContent == false ){return false;}
		if($dom->loadHTML($pageContent)) {
			$list = $dom->getElementsByTagName("title");
			if ($list->length > 0) {
				$title = $list->item(0)->textContent;
			}
		}
		if( ! empty($title) ){
			return $title;
		}else{
			self::$errormsg = 'error getting page title';
			Logger::log(self::$errormsg);
			return false;
		}
	}

}//class App