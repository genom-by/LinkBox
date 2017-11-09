<?php
//==========================================
// Auth class
// ver 1.0
// © genom_by
// last updated 24 aug 2017
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

class Auth{
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
	
	public static function whoLoggedID(){
		if( ! empty($_SESSION["user_id"]) ){
			return $_SESSION["user_id"];
		}else{
			return false;
		}
	}
	public static function whoLoggedName(){
		if( ! empty($_SESSION["user_name"]) ){
			return $_SESSION["user_name"];
		}else{
			return false;
		}
	}
	public static function notLogged(){
		if( empty($_SESSION["user_id"]) ){
			//REMEMBER ME
			if( !empty($_COOKIE["token"]) ){
				return ! self::loginRememberedUserToken($_COOKIE["token"]);
			}else{
				return true;
			}
			return true;
		}else{
			return false;
		}
	}
	
	public static function logout(){
		if(isset(self::$uid)){unset(self::$uid);}
		self::rememberMeToken('user', false);
		session_destroy();
	}
	
	public static function loginUser($user, $pwd, $remember=false){
		if( ! empty($user) ){
			if( empty($pwd) ){
				return false;
			}else{
			
				if(self::userKnowPassword($user, $pwd)){
					session_start();
					$_SESSION["user_id"] = $user->id;
					$_SESSION["user_name"] = $user->name;
					self::$uid = $user->id;
					
					if( isset($remember) ){
						self::rememberMeToken($user, true);
					}
					
					return true;
				}else{
					return false;
				}
			}
		}else{
			return false;
		}	
	}
	public static function loginRememberedUser($userName, $pwdHash){
		if( empty($userName) ){
			return false;			
		}
		if( empty($pwdHash) ){
			return false;
		}else{
			$user = User::getUserbyNameOrEmail($userName, '');
			if($user !== false){
				if($user->pwdHash == $pwdHash){
					session_start();
					$_SESSION["user_id"] = $user->id;
					$_SESSION["user_name"] = $user->name;
					self::$uid = $user->id;	

					return true;						
				}
			}
		}
		return false;
	}
	/* =====================================loginRememberedUser 2 - with tokens=====================================================================
	obtains token from cookie and compares with stored in database
	*/
	public static function loginRememberedUserToken($userToken){
		if( empty($userToken) ){
			return false;			
		}else{
		
Logger::log('try to restore user from token'.$userToken);		
		$user_id = Auth::getUserIDbyToken($userToken);
		if($user_id === false){return false; }
		$user = User::load($user_id);
		
		if($user !== false){
			session_start();
			$_SESSION["user_id"] = $user->id;
			$_SESSION["user_name"] = $user->name;
			self::$uid = $user->id;	
Logger::log('user restored from token'.$user->name);
			return true;						
		}
		return false;	
		}
		
	}
	
	public static function userKnowPassword($user, $pwd){
		
		$res = Utils::compareStringHash($pwd, $user->pwdHash);

		Logger::log('user'.$user->pwdHash);
		if($res){return true;}else{return false;}
	}
	//TODO clearance
	public static function rememberMe( $user, $re ){
		
		if( $re ) {
			setcookie ("member_login",$user->name,time()+ (30 * 24 * 60 * 60));
			setcookie ("member_password",$user->pwdHash,time()+ (30 * 24 * 60 * 60));
		} else {
			if(isset($_COOKIE["member_login"])) {
				setcookie ("member_login","", time() - 3600);
			}
			if(isset($_COOKIE["member_password"])) {
				setcookie ("member_password","", time() - 3600);
			}
		}
	}
	/* ==================================== rememberMe 2 - tokens =========================
	*/
	public static function rememberMeToken( $user, $re ){
		
		if(empty($user)){
			return false;
		}	
		$aurm = new AuthORM();
		$res = $aurm->save();
		if($res === false){Logger::log('Auth[rememberMeToken]: could not save token '.$aurm->errormsg);return false;}
		//var_dump($aurm);die();
		if( $re ) {
			if($res === true){
				setcookie ("token",$aurm->token,$aurm->expDate);
				//setcookie ("member_login",$user->name,time()+ (30 * 24 * 60 * 60));
				//setcookie ("member_password",$user->pwdHash,time()+ (30 * 24 * 60 * 60));
				return true;
			}else{
				Logger::log('Auth[rememberMeToken]: could not remember user '.$aurm->errormsg);return false;
			}
		} else {
			if(isset($_COOKIE["token"])) {
				$token = $_COOKIE["token"];
				$db = LinkBox\DataBase::connect(); //get raw connection
				//$conn = $db::getPDO(); //get raw connection
				if (! $db->executeDelete("DELETE FROM authTokens WHERE token='{$token}'") ){
					Logger::log('Auth[rememberMeToken]: could not delete token from DB '.$aurm->errormsg);
				}
				setcookie ("token","", time() - 3600);
			}
		}
		return true;
	}
	/*
	*/
	public static function saveUserToken($user){
		if( empty($user)){
			return false;
		}	
		$aurm = new AuthORM();
		$res = $aurm->save();
		if($res){return true;}else{Logger::log('Auth[saveUserToken]: could not save auth '.$aurm->errormsg);return false;}
	}
	
	/* ======================================= getUserIDbyToken ======================================= 
	*/
	public static function getUserIDbyToken($token){
		if( empty($token)){
			return null;
		}
		$authtoken = AuthORM::getAuthByToken($token);
		if( empty($authtoken)){
			return null;
		}
		if( $authtoken == false){
			return false;
		}
		return $authtoken['user_id'];
		
	}
		
}//class Auth

/* ========================================================================================================================
auth orm class
*/
class AuthORM extends DBObject{

	protected static $orm = array('table'=>'authtokens', 'table_id'=>'id_token', 'is_uid'=>false);
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