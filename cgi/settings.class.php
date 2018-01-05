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
			case 'faviconShowMain':
				$default = 'favShow';	//alternate - favNotShow
				if( ! empty($_COOKIE["faviconShowMain"]) ){
					if( $_COOKIE["faviconShowMain"] == 'favShow' ){
						return 'favShow';
					}elseif( $_COOKIE["faviconShowMain"] == 'favNotShow'){
						return 'favNotShow';
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
/* == create html block for user == */
	public static function HTMLsettingsBlock($uid){
		if( empty($uid) ){
			return false;
		}
		$html = '';
		if(Settings::HasUserSettings($uid)){
		$allSets = Settings::getSettingsForUser( $uid );
			foreach( $allSets as $setsA){
				$html = $html.PHP_EOL.Settings::buildHTMLsetting($setsA);
			}
		return $html;
		}else{
			Settings::InitUserSettings(Auth::whoLoggedID());
			return self::HTMLsettingsBlock($uid);
		}
	}
	
	public static function buildHTMLsetting($setA){

		$set_labelAr = array('tagsInputStyle'=>'Tags input style','showFawIcons'=>'Show favicons for links','language'=>'Preferred language','fetchSiteTitle'=>'Try to obtain site title','pagerLimit'=>'Links per page (0 - all)');
		
		$setName = $setA['name']; 		$setCaption = $set_labelAr[$setName];
		$variants = explode('|',$setA['variants']);
		
		$varHTML = ''; 		$checked = '';	$currentValue = '';
		if($setA['storeCookie']){
			$currentValue = Settings::Val($setName);}else{
			$currentValue = $setA['value'];
		}
		foreach($variants as $varOne){	
								//Logger::log("{$setName}={$varOne};currentVal={$currentValue}");
			$setVal = $varOne;	//$setVal = $setName.'_'.$varOne;
			if($currentValue == $varOne){$checked = 'checked';}else{$checked = '';}
			$adiv = "<div><label class='radio-inline'>".
			"<input type='radio' name='{$setName}' value='{$setVal}' {$checked}> {$varOne}
			</label></div>";
			$varHTML = $varHTML.PHP_EOL.$adiv;
		}
		$tmplt = "<fieldset class='optsimple'><label class='control-label col-xs-3'>{$setCaption}:</label>".PHP_EOL."<div class='col-xs-6'>{$varHTML}".PHP_EOL."</div></fieldset>";
		return $tmplt;
	}
	
/*	==	initialize new settings set for new user	==	*/
	public static function InitUserSettings($uid){
		if(empty($uid) ) {return false;}
		if( Settings::HasUserSettings($uid) ){
			self::$errormsg = 'Settings[InitUserSettings]: user has setting.';
			return true;
		}else{
			$res = SettingsORM::CreateUserSettingsTable($uid);
			if($res === true){
				return true;
			}else{
				self::$errormsg = "Could not create user's settings";
	Logger::log("Settings[InitUserSettings]: could not create user with id={$uid} settings table.");
				return false;
			}
		}	
	}
/*	==	Save all settings	==	*/
	public static function SaveSettings($post){
		$defSetList = SettingsORM::GetSettingsList();

		foreach($post as $setName => $setValue){
			foreach($defSetList as $setting){	//Logger::log("setName:{$setName},setVal:{$setValue},defsetName:{$setting['name']}");
				if($setting['name'] == $setName){
					if($setting['storeCookie']){
						$res = self::Set($setName, $setValue);
					}else{
						$res = SettingsORM::SaveSetDB($setting['id_set'], $setValue);
					}
					if ($res === false){
						self::$errormsg = "Could not save user's settings";
Logger::log("Settings[SaveSettings]: could not save user's settings. Name:{$setName},value:{$setValue}");
						return false;
					}					
				}
			}			
		}
		return true;
	}

/*	==	are there settings set for user?	==	*/
	public static function HasUserSettings($uid){
		if(empty($uid) ) {return false;}
		$count = SettingsORM::countFrom('user_settings', 'setid', 'uid='.$uid);
		if($count < 1){return false;}else{return true;}
	}

/* == set setting lol
*/	
	public static function Set($settingName, $value){
		if( empty($settingName) OR ( ! isset($value) ) ){
			self::$errormsg = 'Settings[Set]: could not save setting - empty data.';
			Logger::log(self::$errormsg);
			return false;
		}

		
		switch($settingName){
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
					//return false;
					return 'pillbox';	//default
				}			
			break;
			case 'language':
			case 'showFawIcons':
			case 'fetchSiteTitle':
			case 'pagerLimit':
				return SettingsORM::getSetting($setting);
			break;
			case '':
			break;
			default:
				return false;
		}		
	}
	public static function getSettingsForUser($uid){
		if(empty($uid)){return false;}	
		$dbsets = SettingsORM::getSettingsForUser($uid);
		if(count($dbsets) < 1 ){
			self::$errormsg = 'Settings list not obtained from db';
			LiLogger::log( 'Settings::getSettingsForUser failed: '.self::$errormsg );
			return false;
		}
		foreach($dbsets as $set){
			/*if(isset( $set['tagsInputStyle']) )
				$set['tagsInputStyle'] = self::Val('tagsInputStyle');
			}*/
			if( $set['storeCookie'] )
				$set['name'] = self::Val($set['name']);
			}
		return $dbsets;
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

	protected static $orm = array('table'=>'user_settings', 'table_id'=>'-', 'where_uid'=>'uid', 'is_uid'=>true);
	protected static $sqlGetAll = '';
		
	public function __construct($setitem){
	parent::__construct();
		$this->name = $setitem['name'];
		$this->svalue = $setitem['svalue'];
		$this->defvalue = $setitem['defvalue'];
		$this->variants = $setitem['variants'];
		$this->storeCookies = $setitem['storeCookies'];
		$this->uid = Auth::whoLoggedID();
		$this->sqlPDOSave = "INSERT INTO settings(id_user, name, svalue, defvalue, variants, storeCookies) VALUES(:id_user:, ':name:', ':svalue:', ':defvalue:', ':variants:', :storeCookies:)";
		$this->pdoPDOSave = "INSERT INTO settings(id_user, name, svalue, defvalue, variants, storeCookies) VALUES(:id_user, :name, :svalue, :defvalue, :variants, :storeCookies)";
	}
	public function save(){
			
		if( empty($this->name) OR empty($this->svalue) ){
			$this->errormsg = 'Empty setting is not allowed.';
			Logger::log( 'Settings::save failed: '.$this->errormsg );
			return false;			
		}
		
		$stmt = $this->PDO->prepare($this->pdoPDOSave);
		$stmt->bindValue(':name',$this->name,PDO::PARAM_STR);
		$stmt->bindValue(':uid',$this->uid,PDO::PARAM_INT);
		$stmt->bindValue(':svalue',$this->svalue,PDO::PARAM_STR);
		$stmt->bindValue(':defvalue',$this->defvalue,PDO::PARAM_STR);
		$stmt->bindValue(':variants',$this->variants,PDO::PARAM_STR);
		$stmt->bindValue(':storeCookies',$this->storeCookies,PDO::PARAM_INT);

		return $this->savePDOStatement($stmt);
		
	}
	public static function SaveSetDB($setid, $val){
		if( ( ! isset($val) ) OR empty($setid) ){
			self::$errormsg = 'empty value';
			$er = 'SettingsORM[SaveSetDB]: could not save setting - empty data.';
			Logger::log($er);
			return false;
		}
		$uid = Auth::whoLoggedID();
		$sql = "UPDATE user_settings SET `value` = :value WHERE setid = :setid AND uid = :uid";
		
		$db = LinkBox\DataBase::connect();
		try{
		$stmt = $db->connection->prepare($sql);	
		
		$stmt->bindValue(':uid',$uid,PDO::PARAM_INT);
		$stmt->bindValue(':setid',$setid,PDO::PARAM_INT);
		$stmt->bindValue(':value',$val,PDO::PARAM_STR);
		
		$stmt->execute();
		return true;
		}catch(PDOException $e) {
			self::$errormsg = 'Settings could not be saved.';
			$err = 'SettingsORM::SaveSetDB :: error performing saving setting: '.$e->getMessage();
			Logger::log( $err );
			return false;
		}
		
	}
/*  get one setting */	
	public static function getSetting($name){
		if(empty($name)){return false;}
		$uid = Auth::whoLoggedID();
		if($uid === false){
			$err = "SettingsORM::getSetting :: user not logged";
			Logger::log( $err );
			self::$errormsg = 'Setting could not be obtained.';			
			return false;		
		}
		$sql = "select `value` from user_settings LEFT JOIN settings ON user_settings.setid=settings.id_set where user_settings.uid = {$uid} AND name = '{$name}'";
		$res = self::getEntriesArrayBySQL($sql);
		if($res === false){
			//try to get default val
			$res = self::getDefaultSetting($name);
			if($res === false){
				$err = "SettingsORM::getSetting :: error obtaining setting {$name}: ".self::$errormsg;
				Logger::log( $err );
				self::$errormsg = 'Setting could not be obtained.';			
				return false;
			}else{
				return $res;				
			}
		}
		return $res[0]['value'];
	}
	
	/*  get one default value for setting */	
	public static function getDefaultSetting($name){
		if(empty($name)){return false;}
		
		$sql = "select `defvalue` from settings WHERE name = '{$name}'";
		$res = self::getEntriesArrayBySQL($sql);
		if($res === false){
			//try to set default val
			
			$err = "SettingsORM::getDefaultSetting :: error obtaining default setting {$name}: ".self::$errormsg;
			Logger::log( $err );
			self::$errormsg = 'Default setting could not be obtained.';			
			return false;}
		return $res[0]['defvalue'];
	}
	
	public static function getSettingsForUser($uid){
		if(empty($uid)){return false;}		
		$sql = "select id_set, name, `value`, variants, storeCookie from user_settings LEFT JOIN settings ON user_settings.setid=settings.id_set where user_settings.uid = {$uid}";
		return self::getEntriesArrayBySQL($sql);
	}
	
	public static function CreateUserSettingsTable($uid){
		if(empty($uid)){return false;}	
		$sql = "INSERT INTO user_settings(uid,setid,value) VALUES(:uid, :setid, :value)";
		$paramsAr = self::GetSettingsList();
		if(count($paramsAr) < 1 ){
			self::$errormsg = 'Settings list not obtained';
			Logger::log( 'SettingsORM::CreateUserSettingsTable failed: '.self::$errormsg );
			return false;
		}
		$db = LinkBox\DataBase::connect();
		$stmt = $db->connection->prepare($sql);
		
		try{
			$db->connection->beginTransaction();
			foreach( $paramsAr as $param){
				$stmt->bindValue(':uid',$uid,PDO::PARAM_INT);
				$stmt->bindValue(':setid',$param['id_set'],PDO::PARAM_INT);
				$stmt->bindValue(':value',$param['defvalue'],PDO::PARAM_STR);
				
				$stmt->execute();
			}
			$db->connection->commit();
			return true;
		} catch(PDOException $e) {
			if(stripos($e->getMessage(), 'DATABASE IS LOCKED') !== false) {
				// This should be specific to SQLite, sleep for 0.25 seconds
				// and try again.  We do have to commit the open transaction first though
				$db->connection->commit();
				usleep(250000);
			} else {
				$db->connection->rollBack();
				self::$errormsg = 'Settings could not be saved.';
				$err = 'SettingsORM::CreateUserSettingsTable :: error performing transaction: '.$e->getMessage();
				Logger::log( $err );
				return false;
			}
		}
		
	}
	
	public static function GetSettingsList(){
			
		$sql = "select id_set, name, defvalue, variants, storeCookie from settings";
		return self::getEntriesArrayBySQL($sql);
		
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