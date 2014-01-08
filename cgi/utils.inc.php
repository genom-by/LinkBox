<?php
namespace LinkBox;

include_once 'settings.inc.php';

class Utils{

	public static function cleanInput($str)
	{
		if( is_null($str)) return '';
		
		$str = trim($str);
		$str = strip_tags($str);
		$str = stripslashes($str);
		return $str;
	}
}

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