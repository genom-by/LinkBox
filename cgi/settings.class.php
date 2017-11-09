<?php
//==========================================
// Settings class
// ver 1.0
// © genom_by
// last updated 09 nov 2017
//==========================================

namespace lbx;
session_start();

use PDO;
use PDOException;
use LinkBox;
use LinkBox\Logger as Logger;
use LinkBox\Utils as Utils;

//include_once 'settings.inc.php';
include_once 'utils.inc.php';
include_once 'settings.inc.php';
include_once 'database.class.php';
include_once 'dbObjects.class.php';

class Settings{
	public static $errormsg;	//error(s) when executing
	
	protected static $uid;	// database id of registered user 
	
	private $db;	// database connection
	
	private static $objectType; // what kind of table is the class
	private static $sqlGetAll;	// SQL query for getting all records without WHERE clause
	private static $sqlGetAllOrdered;	// SQL query for getting all records ordered (if applicable)

	public function __construct($name_, $uid_){
		$this->name = Utils::cleanInput($name_);
		$this->uid = Utils::cleanInput($uid_);
	}

/* ========================= html customizing =======================
*/	
	public static function HTMLStyle($element){
		if( empty($element) ){
			return false;
		}
		switch($element){
			case 'tagsInputStyle':
				$default = 'pillbox';
				if( ! empty($_COOKIE["tagsInputStyle"]) ){
					if( $_COOKIE["tagsInputStyle"] == 'simple' ){
						return 'simple';
					}elseif( $_COOKIE["tagsInputStyle"] == 'pillbox'){
						return 'pillbox';
					}else{
						return $default;					
					}
				}else{
					return $default;
				}				
			break;
			default:
				return false;
		}
	}
/* == set setting lol
*/	
	public static function Set($setting, $value){
		if( empty($setting) OR empty($value) ){
			self::$errormsg = 'Settings[Set]: could not save setting - empty data.';
			Logger::log(self::$errormsg);
			return false;
		}
		switch($setting){
			case 'tagsInputStyle':
				setcookie ("tagsInputStyle",$value,time()+ (30 * 24 * 60 * 60));
				return true;
				//setcookie ("member_password",$user->pwdHash,time()+ (30 * 24 * 60 * 60));			
			break;
			default:
				return false;
		}		
	}
/* == check and return setting
*/	
	public static function Val($setting){
		if( empty($setting) ){
			self::$errormsg = 'Settings[Val]: could not return setting - empty data.';
			Logger::log(self::$errormsg);
			return false;
		}
		switch($setting){
			case 'tagsInputStyle':
				if( ! empty($_COOKIE["tagsInputStyle"]) ){
					if( $_COOKIE["tagsInputStyle"] == 'simple' ){
						return 'simple';
					}elseif( $_COOKIE["tagsInputStyle"] == 'pillbox'){
						return 'pillbox';
					}else{
						return false;					
					}
				}else{
					return false;
				}			
			break;
			default:
				return false;
		}		
	}
	
/* == UNset setting lol
*/	
	public static function deSet($setting){
		if( empty($setting) ){
			self::$errormsg = 'Settings[deSet]: could not unset setting - empty data.';
			Logger::log(self::$errormsg);
			return false;
		}
		switch($setting){
			case 'tagsInputStyle':
				setcookie ("tagsInputStyle","", time() - 3600);
				//setcookie ("member_password",$user->pwdHash,time()+ (30 * 24 * 60 * 60));
				return true;				
			break;
			default:
				return false;
		}		
	}
	
	
		
}//class Settings

/* ========================================================================================================================
settings orm class
*/
class SettingsORM extends DBObject{

	protected static $orm = array('table'=>'settings', 'table_id'=>'id_token', 'where_uid'=>'id_user', 'is_uid'=>true);
	protected static $sqlGetAll = 'SELECT id_token, user_id, token, expDate from authtokens';
	
	public $token;
	public $uid;
	public $expDate;
	
	public function __construct($id='',$expDate=''){
		$this->token = Utils::generateToken(Auth::whoLoggedID());
		$this->expDate = time()+ (30 * 24 * 60 * 60);
		$this->uid = Auth::whoLoggedID();
		$this->sqlPDOSave = "INSERT INTO authtokens(user_id, token, expDate) VALUES(:uid:, ':tt:', :expdt:)";
	}
	public function save(){
		$pdosql = str_replace(':tt:', $this->token, $this->sqlPDOSave);
		$pdosql = str_replace(':uid:', $this->uid, $pdosql);
		$pdosql = str_replace(':expdt:', $this->expDate, $pdosql);
		return $this->saveObject($pdosql);
	}
	public static function getAuthByToken($token){
	
		if( empty($token)){
			return null;
		}
		$token = Utils::cleanInput($token);
		
		$where = "WHERE token='{$token}'";	

//echo $where;
		$authtoken = self::getAllWhere($where);
//var_dump($user[0]);die();
		if($authtoken[0]['id_token'] > 0) {return $authtoken[0];} else {return false;}
		
	}
	
	public static function load($id){
		$load = self::getFromDB($id);
		if(empty($load)){return false;}else{
		$me = new AuthORM();
		$me->id = $load['id_token'];
		$me->token = $load['token'];
		$me->uid = $load['user_id'];
		$me->expDate = $load['expDate'];
		return $me;}
	}

}