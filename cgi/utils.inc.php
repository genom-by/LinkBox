<?php

//==========================================
// utilitary classes and functions
// ver 1.3
// © genom_by
// last updated 23 aug 2017
//==========================================

namespace LinkBox;

include_once 'settings.inc.php';

class Utils{

	public static function cleanInput($str)
	{
		if( is_null($str)) return '';
		
		$str = trim($str);
		$str = strip_tags($str);
		$str = stripslashes($str);
		$str = trim($str);		
		return $str;
	}
	
	public static function getHash($string){
		//bcrypt = new \Zend\Crypt\Password\Bcrypt();
		//$hash = $bcrypt->create($password);
		//$hash = $bcrypt->create($string);
		//echo $hash;
	//TEMPORARY SOLUTION!!!
		$hash = crypt($string);
		return $hash;
	}
	public static function compareStringHash($string, $hash){

	//TEMPORARY SOLUTION!!!
		if (crypt($string, $hash) == $hash) {
			$result = true;
		}else{ $result = false;}
		return $result;
	}
	
	//very simple validation for correct spelled email
	//
	public static function isValidEmail($email){
		if (preg_match('/[\w\.-]+@\w+\.+\w+/', $email) ) return true;
		else return false;
		
	}
	
	//convert string HH:mm to integer HH*60 + mm
	public static function HHmm2Int($hhmm){
		list($h,$m) = explode(':', $hhmm);
		return $h*60+$m;
	}
	//convert nteger HH*60 + mm to string HH:mm
	public static function Int2HHmm($intgr){
		$m = $intgr % 60;
		$m_str = "";
		if($m < 10){$m_str='0'.$m;}else{$m_str=$m;}
		$h = floor($intgr / 60);
		return $h.':'.$m_str;
	}
	
	public static function generateToken($uid){
		if(empty($uid)){return false;}
//TODO random salt
		return  md5($uid);
	}
} // Utils end

/* ======================================== LOGGER ===================================
*/
class Logger{

	public static $last_error = '';
	public static $logfile = '';
	
	private static function initLogFile(){
		//self::$logfile = SITE_ROOT.DIRECTORY_SEPARATOR.'log'.DIRECTORY_SEPARATOR.LOG_FILE;
		self::$logfile = SITE_DIR.'/'.'log'.'/'.LOG_FILE;
	}
	
	public static function log($msg){
		self::initLogFile();
		$msg = date("M,d H:i:s").' : '.$msg;
		try{
			//file_get_contents(SITE_ROOT.DIRECTORY_SEPARATOR.'log'.DIRECTORY_SEPARATOR.LOG_FILE);
//file_put_contents('PDOErrors.txt', $e->getMessage(), FILE_APPEND);  
			$hndl = fopen(self::$logfile, 'ab+');
			if($hndl !== false){
				$retstr = fwrite($hndl, $msg.PHP_EOL);
				if($retstr === false){throw new \Exception("Can not write to file - $msg");}
				fclose($hndl);
				return true;
			}else{
				throw new \Exception("File not opened. Trying to write: {$msg}");
				return false;
			}
		}catch(\Exception $e){
			self::$last_error = "Error while logging: ".$e->getMessage();
			if(! empty($hndl)) fclose($hndl);	
			return false;
		}
	}
}
?>