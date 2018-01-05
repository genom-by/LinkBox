<?php
//==========================================
// DB objecs classes
// ver 1.8
// © genom_by
// last updated 09 nov 2017
//==========================================

namespace lbx;

use PDO;
use PDOException;
use LinkBox;
use LinkBox\Logger as LiLogger;
use LinkBox\Utils as Utils;

include_once 'auth.inc.php';

include_once 'utils.inc.php';
include_once 'settings.inc.php';
include_once 'database.class.php';

Interface IDBObjects{
	function save();
	function update();
	function delete();
	static function getByID($id);
	static function getBy($field, $value);
	static function get();
}

Interface ObjectEntity{
	function isThereSameObject();
	function validateSave();
	function validateUpdate();
}

class DBObject{
	public static $errormsg;	//error(s) when executing
	public $name;
	
	protected $id;	// database table id of object
	protected $uid;	// database id of user to whom odject belongs (if applicable)
	
	private $db;	// database connection
	protected $PDO;	// database connection PDO object
	private $sqlPDOSave;
	## ORM ## //protected static $orm = array('table'=>'obus', 'table_id'=>'id_obus'); 
	
	private static $objectType; // what kind of table is the class
	private static $sqlGetAll;	// SQL query for getting all records without WHERE clause
	private static $sqlGetAllOrdered;	// SQL query for getting all records ordered (if applicable)

/* ### REFACTORED ###
@sql
%array or %fale+%errormsg
*/
	public function __construct(){
		$db = LinkBox\DataBase::connect(); //get raw connection
		$this->PDO = $db::getPDO(); //get PDO
		
	}
	
public static function getEntriesArrayBySQL($sql, $ref=-1){
	if (empty($sql)) {
		self::$errormsg="DBObject[getEntriesBySQL]: No SQL provided";
		return false;
	}
## uid
	$userWhere = static::buildUserWhereClause();

	if( ! empty($userWhere) ){
		$where_position = strpos($sql, 'WHERE');
		if(false !== $where_position){
			$where = substr($sql, $where_position+strlen('WHERE'));
			
			$user_sql = $sql.' '.$userWhere.' AND '.$where;
		}else{
			$user_sql = $sql.' '.$userWhere;		
		}
	}else{
		$user_sql = $sql;
	}
	//LiLogger::log('usersql where: '.$user_sql);

## uid

	$objects = LinkBox\DataBase::getArrayBySQL($sql, $ref);
	if($objects === false){
		self::$errormsg = "DBObject[getEntriesBySQL]: No data from DB: ".LinkBox\DataBase::$errormsg;
		LiLogger::log( self::$errormsg );
		return false;
		}
	else return $objects;		
}//getEntriesArrayBySQL

	public static function deleteEntry($table, $id, $id_column=""){
		if (empty($table)) return false;
		if (empty($id)) return false;
		if (empty($id_column) ){$id_column = "id_{$table}";}
		
		
		/* protection from GUEST deleting */
		if(Auth::whoLoggedName() === 'guest'){
			self::$errormsg = 'Guests can not delete entries.';
			LiLogger::log( self::$errormsg );
			return false;	
		}
		
		$db = LinkBox\DataBase::connect(); //get raw connection
		$conn = $db::getPDO(); //get raw connection
		//$conn->beginTransaction();
		if ($db->executeDelete("DELETE FROM {$table} WHERE {$id_column}={$id}") ){
			if ($table == 'itinerary'){
				// delete all pitstops for this itinerary
				if (false !== $db->executeDelete("DELETE FROM pitstop WHERE `id_itinerary`={$id}") )
					{return true;}else{
				self::$errormsg = 'error while deleting pitstops: '.LinkBox\DataBase::$errormsg;
				LiLogger::log( self::$errormsg );
				return false;}
			}
			
			return true;}
		else{
			self::$errormsg = 'error while deleting DB: '.LinkBox\DataBase::$errormsg;
			LiLogger::log( '[DBO]deleteEntry:'.self::$errormsg );
			return false;		
		}
	}
	public function saveObject($pdosql){
		$res = LinkBox\DataBase::executeInsert($pdosql);
		if($res === false){
			$this->errormsg = 'error while saving into DB: '.LinkBox\DataBase::$errormsg;
			LiLogger::log( $this->errormsg );
			return false;
		}
		else return true;
	}
	/* REFACTORED - save prepared PDO statements instead of pure sql string
	*/
	public function savePDOStatement($pdoSTMT){
		
		if( empty($pdoSTMT) ){			self::$errormsg = 'DB[savePDOStatement]: no statement to save.';
			LiLogger::log( self::$errormsg );
			return false;
		}
		try{		
		if(false === $pdoSTMT->execute() ){
			$this->errormsg = 'error while saving into DB: '.implode(' ', $statement->errorInfo() );
			LiLogger::log( 'DBObject[savePDOStatement]:: '.$this->errormsg );			
			return false;
		}else return true;
		}catch(PDOException $e){
			$this->errormsg = 'error executing pdo statement: '.$e->getMessage();//LinkBox\DataBase::$errormsg;
			LiLogger::log( 'DBObject[savePDOStatement]:: '.$this->errormsg );
			return false;
		}
		
	}
	## NEW ##
	/*input @array(field=>value)
	*/
	public function update($fields_values=null){
		if( empty($this->id) ){			$this->errormsg = 'DB[updateObject]: no id for update.';
			LiLogger::log( $this->errormsg );
			return false;
		}
		if( ! $this->validateParams($fields_values) ){
				$this->errormsg = 'Updating values are empty:'.static::$errormsg;
			LiLogger::log( 'DB[updateObject]: '.$this->errormsg );
			return false;
		}
		//$values = self::buildUpdateValues($fields_values);
		$keyValuesArray = array();
		$valuesPDO = self::buildUpdateValuesPDO($fields_values, $keyValuesArray);	//id_folder = :id_folder,url = :url,isShared = :isShared,title = :title
		
		if( empty($valuesPDO) ){			$this->errormsg = 'DB[updateObject]: values are empty.';
			LiLogger::log( $this->errormsg );
			return false;
		}
		
		$tableName = static::$orm['table'];
		$tableId = static::$orm['table_id'];
		
		$whereClause = " WHERE {$tableId} = {$this->id}";
		//$sql = "UPDATE {$tableName} SET {$values} {$whereClause}";
		$sqlPDO = "UPDATE {$tableName} SET {$valuesPDO} {$whereClause}"; //UPDATE link SET id_folder = :id_folder,url = :url,isShared = :isShared,title = :title  WHERE id_link = 40
		try{
			$stmt = $this->PDO->prepare($sqlPDO);
			$resPDO = $stmt->execute($keyValuesArray);
		}catch(PDOException $e){
			$this->errormsg = 'Could not run pdo query into DB.';
			$logError = 'DBO[update]: Error while running PDO query into DB:'.$e->getMessage();//LinkBox\DataBase::$errormsg;
			LiLogger::log( $logError );
			LiLogger::log( 'compiled sql: '.$sqlPDO);
			return false;
		}
		if($resPDO === false){
			$this->errormsg = 'Could not update pdo query into DB.';
			$logError = 'DBO[update]: Error while updating into DB: '.LinkBox\DataBase::$errormsg;
			LiLogger::log( $logError );
			return false;
		}
		else return true;
		/*
		$res = LinkBox\DataBase::executeUpdate($sql);
		if($res === false){
			$this->errormsg = 'Could not run query into DB.';
			$logError = 'DBO[update]: Error while updating into DB: '.LinkBox\DataBase::$errormsg;
			LiLogger::log( $logError );
			return false;
		}
		else return true;*/
	}
	private static function buildUpdateValues($fields_values){
		if( empty($fields_values) ){	self::$errormsg = 'DB[buildUpdateValues]: no values.';
			LiLogger::log( self::$errormsg );
			return false;
		}
		$str = "";
		foreach($fields_values as $key=>$value){
			if(gettype($value)=='string'){$valstr="'{$value}'";}else{$valstr=$value;}
			$str = $str."{$key} = {$valstr},";
		}
		$str = rtrim($str,',');
		return $str;
	}
	private static function buildUpdateValuesPDO($fields_values, & $keyValuesArray){
		if( empty($fields_values) ){	self::$errormsg = 'DB[buildUpdateValuesPDO]: no values.';
			LiLogger::log( self::$errormsg );
			return false;
		}
		$str = "";
		foreach($fields_values as $key=>$value){
			if(gettype($value)=='string'){$valstr="'{$value}'";}else{$valstr=$value;}
			$keyTag = ":{$key}";
			$keyValuesArray[$keyTag] = $value;
			$str = $str."{$key} = :{$key},";
		}
		$str = rtrim($str,',');
		return $str;
	}
	
	/* 
	returns all records for inherited class based on static sql query
	*/
	public static function getAll($limitSQL=''){
	## REFACTOR - user
		if(!empty(static::$sqlGetAllOrdered)){$sql = static::$sqlGetAllOrdered;
		}else{$sql = static::$sqlGetAll;}
		/*
		if( static::$orm['is_uid'] ){
			$uidcolumname = static::$orm['where_uid']?static::$orm['where_uid']:'uid';
			if( ! Auth::notLogged() ){
				$uid = Auth::whoLoggedID();
				$userWhere = " WHERE {$uidcolumname} = {$uid}";}
			else{
				$userWhere = " WHERE {$uidcolumname} = -1";//protect from unauthorised access
				}	
			}else{$userWhere='';}*/
		$userWhere = static::buildUserWhereClause();
		$orderby = static::buildORDERBYClause();
		
		$user_sql = static::$sqlGetAll.' '.$userWhere.' '.$orderby.' '.$limitSQL;
		/*$order_position = strpos($sql, 'ORDER BY');
		if(false !== $order_position){
			$order = substr($sql, $order_position);
			
			$user_sql = static::$sqlGetAll.' '.$userWhere.' '.$order;
		}else{
			$user_sql = $sql.$userWhere;		
		}	*/
		//LiLogger::log('usersql order: '.$user_sql);
		return self::getEntriesArrayBySQL($user_sql);		
	}
	/*
	returns filtered records for inherited class based on static sql query and provided filter
	*/
	public static function getAllWhere($whereSQL, $limitSQL=''){
		if(empty($whereSQL)){self::$errormsg = "DBObject[getAllWhere] no where clause"; return false;}
		$where_position = strpos($whereSQL, 'WHERE');
		if(false === $where_position){
			$whereSQL = 'WHERE '.$whereSQL;
		}
		$sql = static::$sqlGetAll.' '.$whereSQL;		//LiLogger::log( $sql);		

		$userWhere = static::buildUserWhereClause();
		
		$order_position = strpos($whereSQL, 'ORDER BY');
		if(false === $order_position){
			$orderby = static::buildORDERBYClause();		
		}else{$orderby = '';}
		


		if( ! empty($userWhere) ){
			$where_position = strpos($whereSQL, 'WHERE');
			if(false !== $where_position){
				$where = substr($whereSQL, $where_position+strlen('WHERE'));
				
				$user_sql = static::$sqlGetAll.' '.$userWhere.' AND '.$where;
			}else{
				$user_sql = $userWhere.' AND '.$whereSQL;		
			}
		}else{
			$user_sql = $sql;
		}
		//LiLogger::log('usersql where: '.$user_sql);
		$user_sql = $user_sql.' '.$orderby.' '.$limitSQL;
		
		return self::getEntriesArrayBySQL($user_sql);		
	}
	/*
	builds where clause for filtering users
	*/
	protected static function buildUserWhereClause(){
		
		if( static::$orm['is_uid'] ){
			$uidcolumname = static::$orm['where_uid']?static::$orm['where_uid']:'uid';
			if( ! Auth::notLogged() ){
				$uid = Auth::whoLoggedID();
				$userWhere = " WHERE {$uidcolumname} = {$uid}";}
			else{
				$userWhere = " WHERE {$uidcolumname} = -1";//protect from unauthorised access
				}	
			}else{$userWhere = '';}
		return $userWhere;
	}
	/*
	builds ORDER BY part if presented
	returns ' ORDER BY column_name' or empty string
	*/
	protected static function buildORDERBYClause(){
		
		if(!empty(static::$sqlGetAllOrdered)){$sql = static::$sqlGetAllOrdered;
		}else{return '';}
		
		$order_position = strpos($sql, 'ORDER BY');
		if(false !== $order_position){
			$order = substr($sql, $order_position);
			
			return ' '.$order;
		}else{
			return '';		
		}
		
	}
	/*
	returns class objext based on provided id
	*/
	public static function getFromDB($id){
		if(empty($id)){self::$errormsg = "DBObject[getFromDB] no id to load"; return false;}
		
		$tableId = static::$orm['table_id'];
		$whereClause = " WHERE {$tableId} = {$id}";
		$sql = static::$sqlGetAll.' '.$whereClause;
				//LiLogger::log( $sql);
		$result = self::getEntriesArrayBySQL($sql);
		if(false === result){return false;}else{return $result[0];}
	}

	public static function countFrom($table, $table_col_id, $whereSQL=""){
		if(empty($table)) return false;
		if(empty($table_col_id)) return false;

		//$tableName = static::$orm['table'];
		//$tableId = static::$orm['table_id'];
## uid
		$userWhere = static::buildUserWhereClause();

		if( ! empty($userWhere) ){
			$where_position = strpos($whereSQL, 'WHERE');
			if(false !== $where_position){
				$where = substr($whereSQL, $where_position+strlen('WHERE'));
				
				$user_sql_where = $userWhere.' AND '.$where;
			}else{
				$user_sql_where = $userWhere.' AND '.$whereSQL;		
			}
		}else{
			$user_sql_where = $whereSQL;
		}
		//LiLogger::log('usersql where: '.$user_sql_where);
## uid		
		$table_idcolumn_id = Utils::cleanInput($table_idcolumn_id);
		$sql = "SELECT COUNT ({$table_col_id}) FROM {$table} {$user_sql_where}";

		//LiLogger::log('count sql: '.$sql);		
		$db = LinkBox\DataBase::connect(); //get raw connection		
		$conn = $db::getPDO(); //get raw connection
		$count = $conn->query($sql)->fetchColumn();
//var_dump($count);
		if($count > 0){return $count;}else{return 0;}
	}

	/*with where word*/
	public static function countWhere($where){
		if(empty($where)) return false;

		$tableName = static::$orm['table'];
		$tableId = static::$orm['table_id'];
		
		return self::countFrom($tableName, $tableId, $where);
		//$table_idcolumn_id = Utils::cleanInput($table_idcolumn_id);
		/*
		$sql = "SELECT COUNT ({$tableId}) FROM {$tableName} {$where}";
	LiLogger::log($sql);
		$db = LinkBox\DataBase::connect(); //get raw connection		
		$conn = $db::getPDO(); //get raw connection
		$count = $conn->query($sql)->fetchColumn();
//var_dump($count);
		if($count > 0){return $count;}else{return 0;}
		*/
	}
	
}//class DBObject
/* ================================================= Folder ===========================================================
*/
class Folder extends DBObject{

	protected static $orm = array('table'=>'folder', 'table_id'=>'id_folder', 'where_uid'=>'id_user', 'is_uid'=>true);
	protected static $sqlGetAll = 'SELECT id_folder, id_parentfolder, folderName, id_user from folder';
	protected static $sqlGetAllOrdered = 'SELECT id_folder, id_parentfolder, folderName, id_user from folder ORDER BY folderName';
	
	public static $totalLinksCount;
	
	public function __construct($name_, $parent_ = null){
		parent::__construct();
		$this->name = Utils::cleanInput($name_);
		$this->parentfolder = Utils::cleanInput($parent_);
		$this->parentfolder = intval($this->parentfolder);
		$this->uid = Auth::whoLoggedID();
		
		if(is_int($this->parentfolder) AND ($this->parentfolder > 0) ){
			$this->sqlPDOSave = "INSERT INTO folder(folderName, id_user, id_parentFolder) VALUES(':1:', :2:, :3:)";
			$this->pdoPDOSave = "INSERT INTO folder(folderName, id_user, id_parentFolder) VALUES(:name, :uid, :parent_id)";
			$this->isSubfolder = true;
		}else{
			$this->sqlPDOSave = "INSERT INTO folder(folderName, id_user) VALUES(':1:', :2:)";
			$this->pdoPDOSave = "INSERT INTO folder(folderName, id_user) VALUES(:name, :uid)";
			$this->isSubfolder = false;
		}
	}
	public function save(){
	
		if( ! $this->validate() ){
			LiLogger::log( 'Folder::save failed. Validation error: '.$this->errormsg );		
			return false;			
		}
		
		$stmt = $this->PDO->prepare($this->pdoPDOSave);
		$stmt->bindValue(':name',$this->name,PDO::PARAM_STR);
		$stmt->bindValue(':uid',$this->uid,PDO::PARAM_INT);
		if( $this->isSubfolder){
		$stmt->bindValue(':parent_id',$this->parentfolder,PDO::PARAM_INT);
		}
		/*
		$pdosql = str_replace(':1:', $this->name, $this->sqlPDOSave);
		$pdosql = str_replace(':2:', $this->uid, $pdosql);
		if(is_int($this->parentfolder) AND ($this->parentfolder > 0) ){
			$pdosql = str_replace(':3:', $this->parentfolder, $pdosql);
		}		
		return $this->saveObject($pdosql);
		*/
		return $this->savePDOStatement($stmt);
	}
	public static function load($id){
		$load = self::getFromDB($id);
		if(empty($load)){return false;}else{
		$me = new Folder($load['folderName']);
		$me->id = $load['id_folder'];
		$me->uid = $load['id_user'];
		if(is_int($load['id_parentfolder'])){
			$this->parentfolder = $load['id_parentfolder'];
		}
		return $me;}
	}
	public function validate(){
		if( empty($this->name) ){
			$this->errormsg = 'Empty folder name is not allowed.';
			LiLogger::log( 'Folder::validation: '.$this->errormsg );
			return false;			
		}
		return true;
	}
	
	public static function validateParams($params){
		$name_ = Utils::cleanInput($params['folderName']);
		if( empty( $name_ ) ){
			self::$errormsg = 'Empty folder name is not allowed.';
			return false;			
		}
		
		return true;
	}
	
	public static function getFoldersNames(){
		$folders = self::getAll();//'SELECT id_folder, folderName, id_user from folder'
		if($folders === false){return false;}
		$foldArr = array();
		foreach($folders as $fld){
			$foldArr[$fld['id_folder']] = $fld['folderName'];
		}
		return $foldArr;
	}
	
	public static function getParentFoldersNames(){
		$folders = self::getAllWhere("WHERE id_parentFolder IS NULL");//'SELECT id_folder, folderName, id_user from folder'
		if($folders === false){return false;}
		$foldArr = array();
		foreach($folders as $fld){
			$foldArr[$fld['id_folder']] = $fld['folderName'];
		}
		return $foldArr;
	}
	public static function getSubFoldersNames($parentID){
		$folders = self::getAllWhere("WHERE id_parentFolder = {$parentID}");//'SELECT id_folder, folderName, id_user from folder'
		if($folders === false){return false;}
		$foldArr = array();
		foreach($folders as $fld){
			$foldArr[$fld['id_folder']] = $fld['folderName'];
		}
		return $foldArr;
	}
/*	============== instead standard getall - for parent folders only ==========================================
*/
	public static function getAllParents(){
		
		return self::getAllWhere("WHERE id_parentFolder IS NULL");		
	}
/*	================ only for parent folders ==========================
*/	
	public static function getSubFoldersAndCounts($parentID = null){
		
		try{
		$db = LinkBox\DataBase::connect();
		if(is_null($parentID)){
		$sql = "SELECT folder.id_folder, folderName, COUNT(id_link) as folderCount FROM folder LEFT JOIN link ON folder.id_folder = link.id_folder WHERE folder.id_user=:uid AND folder.id_parentFolder IS NOT NULL GROUP BY folderName ";
		}else{
		$sql = "SELECT folder.id_folder, folderName, COUNT(id_link) as folderCount FROM folder LEFT JOIN link ON folder.id_folder = link.id_folder WHERE folder.id_user=:uid AND folder.id_parentFolder IS NOT NULL AND id_parentFolder = {$parentID} GROUP BY folderName";
		
		}
		$stmt = $db->connection->prepare($sql);
		$stmt->bindValue(':uid', Auth::whoLoggedID());
		$stmt->execute();

		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}catch(PDOException $e){
			self::$errormsg = 'error fetching folders count: '.$e->getMessage();//LinkBox\DataBase::$errormsg;
			LiLogger::log( self::$errormsg );
			return false;			
		}
		return $rows;
		
	}

/*	================  count for parent folders ==========================
instead fn getAllParents()
*/	
	public static function getParentsFoldersAndCounts(){
		
		if( ! Auth::whoLoggedID() ){
			self::$errormsg = 'User not logged';
			//LiLogger::log( self::$errormsg );
			return false;			
		}
		
		try{
		$db = LinkBox\DataBase::connect();

		$sql = "SELECT folder.id_folder, folderName, COUNT(id_link) as folderParentCount FROM folder LEFT JOIN link ON folder.id_folder = link.id_folder WHERE folder.id_user=:uid AND folder.id_parentFolder IS NULL GROUP BY folderName";
		
		$stmt = $db->connection->prepare($sql);
		$stmt->bindValue(':uid', Auth::whoLoggedID());
		$stmt->execute();

		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}catch(PDOException $e){
			self::$errormsg = 'error fetching parent folders count: '.$e->getMessage();//LinkBox\DataBase::$errormsg;
			LiLogger::log( self::$errormsg );
			return false;			
		}
		return $rows;
		
	}
/*	================ forms array of folders ==========================
*/	
	public static function getFoldersArray(){
		
		if( ! Auth::whoLoggedID() ){
			self::$errormsg = 'User not logged';
			LiLogger::log( "[Folder]getFoldersArray::".self::$errormsg );
			return false;			
		}
		
		$rows = array();
		$onerow = array();
		$totalLinksCount = 0;
		
		//$parents = self::getAllParents();
		$parents = self::getParentsFoldersAndCounts();
		if(false === $parents OR count($parents) < 1){
			self::$errormsg = '[Folder]getFoldersArray::error fetching parent folders: '.self::$errormsg;//LinkBox\DataBase::$errormsg;
			LiLogger::log( "[Folder]getFoldersArray::".self::$errormsg );
			return false;		
		}

		foreach($parents as $parent){
			$sub = self::getSubFoldersAndCounts($parent['id_folder']);
		
			if(false === $sub){
				self::$errormsg = 'error fetching sub folders: '.self::$errormsg;//LinkBox\DataBase::$errormsg;
				LiLogger::log( "[Folder]getFoldersArray::".self::$errormsg );
				return false;		
			}		

			if(count($sub) < 1){
				$empty = array('parentID'=>$parent['id_folder'], 'parentName'=>$parent['folderName'], 'subfolders'=> array(), 'folderCount'=>$parent['folderParentCount']);	
				$rows[] = $empty;
				
				$totalLinksCount += $parent['folderParentCount'];
			}else{
				$onerow['parentID'] = $parent['id_folder'];
				$onerow['parentName'] = $parent['folderName'];
				$onerow['subfolders'] = $sub;
				$onerow['folderCount'] = self::countSubfolderedLinks($sub) + $parent['folderParentCount'];
				$rows[] = $onerow;
				
				$totalLinksCount += $onerow['folderCount'];
			}
		}
//echo'<pre>';var_dump(($rows));die();
		self::$totalLinksCount = $totalLinksCount;
		return $rows;
	}

	public static function countSubfolderedLinks($sub){
		$sum = 0;
		foreach($sub as $s){
			$sum += $s['folderCount'];
		}
		return $sum;
	}	

}
/* ================================================= Sub Folder ===========================================================
*/
class Subfolder extends DBObject{

	protected static $orm = array('table'=>'folder', 'table_id'=>'id_folder', 'where_uid'=>'id_user', 'is_uid'=>true);
	protected static $sqlGetAll = 'SELECT id_folder,  id_parentfolder, folderName, id_user from folder';
	protected static $sqlGetAllOrdered = 'SELECT id_folder,  id_parentfolder, folderName, id_user from folder ORDER BY folderName';
		
	public function __construct($name_, $parent_){
		$this->name = Utils::cleanInput($name_);
		$this->parentfolder = Utils::cleanInput($parent_);
		$this->uid = Auth::whoLoggedID();
		$this->sqlPDOSave = "INSERT INTO folder(folderName, id_parentfolder, id_user) VALUES(':1:', :2:, :3:)";
	}
	public function save(){
		$pdosql = str_replace(':1:', $this->name, $this->sqlPDOSave);
		$pdosql = str_replace(':2:', $this->parentfolder, $pdosql);
		$pdosql = str_replace(':3:', $this->uid, $pdosql);
		return $this->saveObject($pdosql);
	}
	public static function load($id){
		$load = self::getFromDB($id);
		if(empty($load)){return false;}else{
		$me = new Folder($load['folderName']);
		$me->id = $load['id_folder'];
		$me->parentfolder = $load['id_parentfolder'];
		$me->uid = $load['id_user'];
		return $me;}
	}
	
	public static function getFoldersNames($parent_id){
		$parent_id = Utils::cleanInput($parent_id);
		$folders = self::getAllWhere("WHERE id_parentFolder = {$parent_id}");//'SELECT id_folder, folderName, id_user from folder'
		if($folders === false){return false;}
		$foldArr = array();
		foreach($folders as $fld){
			$foldArr[$fld['id_folder']] = $fld['folderName'];
		}
		return $foldArr;
	}	

}
/* ================================================= Tag ===========================================================
*/
class Tag extends DBObject{

	protected static $orm = array('table'=>'tags', 'table_id'=>'id_tag', 'where_uid'=>'id_user', 'is_uid'=>true);
	protected static $sqlGetAll = 'SELECT id_tag, tagName, id_user from tags';
	protected static $sqlGetAllOrdered = 'SELECT id_tag, tagName, id_user from tags ORDER BY tagName';
		
	public function __construct($name_){
		parent::__construct();
		$this->name = Utils::cleanInput($name_);
		$this->uid = Auth::whoLoggedID();
		$this->sqlPDOSave = "INSERT INTO tags(tagName, id_user) VALUES(':1:', :2:)";
		$this->pdoPDOSave = "INSERT INTO tags(tagName, id_user) VALUES(:name, :uid)";
	}
	public function save(){
		
		if( ! $this->validate() ){
			LiLogger::log( 'Tag::save failed. Validation error: '.$this->errormsg );		
			return false;			
		}
		$stmt = $this->PDO->prepare($this->pdoPDOSave);
		$stmt->bindValue(':name',$this->name,PDO::PARAM_STR);
		$stmt->bindValue(':uid',$this->uid,PDO::PARAM_INT);
		return $this->savePDOStatement($stmt);
/*		
		$pdosql = str_replace(':1:', $this->name, $this->sqlPDOSave);
		$pdosql = str_replace(':2:', $this->uid, $pdosql);
		return $this->saveObject($pdosql);*/
	}
	public static function load($id){
		$load = self::getFromDB($id);
		if(empty($load)){return false;}else{
		$me = new Tag($load['tagName']);
		$me->id = $load['id_tag'];
		$me->uid = $load['id_user'];
		return $me;}
	}
	public static function getTagsAndCounts(){
		
		try{
		$db = LinkBox\DataBase::connect();
	
		$sql = "SELECT  tags.id_tag as tagID, tagName, COUNT(id_link) as tagCount FROM tags LEFT JOIN tags_link ON tags.id_tag = tags_link.id_tag WHERE tags.id_user=:uid GROUP BY tagName ";
		
		$stmt = $db->connection->prepare($sql);
		$stmt->bindValue(':uid', Auth::whoLoggedID());
		$stmt->execute();

		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}catch(PDOException $e){
			self::$errormsg = 'error fetching tags count: '.$e->getMessage();//LinkBox\DataBase::$errormsg;
			LiLogger::log( self::$errormsg );
			return false;			
		}
		return $rows;
	}
/*	==	search tags	==	*/
	public static function searchTags($needle){
		if( empty($needle) ){return false;}
		//get needless if there are some
		if( false !== strpos($needle, ',') ){
			$arrN = explode(',',$needle);
			$tcount = count( $arrN );
		}else{
			$tcount = 1;
		}
		try{
		$db = LinkBox\DataBase::connect();
	
		if($tcount == 1){
			$sql = "SELECT tags.id_tag as tagID, tagName, COUNT(id_link) as tagCount FROM tags LEFT JOIN tags_link ON tags.id_tag = tags_link.id_tag WHERE tags.id_user=:uid AND tagName LIKE :needle GROUP BY tagName ";
		}elseif($tcount == 2){
			$sql = "SELECT tags.id_tag as tagID, tagName, COUNT(id_link) as tagCount FROM tags LEFT JOIN tags_link ON tags.id_tag = tags_link.id_tag WHERE tags.id_user=:uid AND tagName LIKE :needle1 OR tagName LIKE :needle2 GROUP BY tagName ";
		}elseif($tcount == 3){
			$sql = "SELECT tags.id_tag as tagID, tagName, COUNT(id_link) as tagCount FROM tags LEFT JOIN tags_link ON tags.id_tag = tags_link.id_tag WHERE tags.id_user=:uid AND tagName LIKE :needle1 OR tagName LIKE :needle2 OR tagName LIKE :needle3 GROUP BY tagName ";
		}elseif($tcount >= 4){
			$sql = "SELECT tags.id_tag as tagID, tagName, COUNT(id_link) as tagCount FROM tags LEFT JOIN tags_link ON tags.id_tag = tags_link.id_tag WHERE tags.id_user=:uid AND tagName LIKE :needle1 OR tagName LIKE :needle2 OR tagName LIKE :needle3 OR tagName LIKE :needle4 GROUP BY tagName ";
		}
		
		$stmt = $db->connection->prepare($sql);
		$stmt->bindValue(':uid', Auth::whoLoggedID());
		if($tcount == 1){
			$stmt->bindValue(':needle', "%{$needle}%", PDO::PARAM_STR);
		}elseif($tcount == 2){
			$n1 = $arrN[0];	$n2 = $arrN[1];
			$stmt->bindValue(':needle1', "%{$n1}%", PDO::PARAM_STR);
			$stmt->bindValue(':needle2', "%{$n2}%", PDO::PARAM_STR);
		}elseif($tcount == 3){
			$n1 = $arrN[0];	$n2 = $arrN[1]; $n3 = $arrN[2];
			$stmt->bindValue(':needle1', "%{$n1}%", PDO::PARAM_STR);
			$stmt->bindValue(':needle2', "%{$n2}%", PDO::PARAM_STR);
			$stmt->bindValue(':needle3', "%{$n3}%", PDO::PARAM_STR);			
		}elseif($tcount >= 4){
			$n1 = $arrN[0];	$n2 = $arrN[1]; $n3 = $arrN[2];; $n4 = $arrN[3];
			$stmt->bindValue(':needle1', "%{$n1}%", PDO::PARAM_STR);
			$stmt->bindValue(':needle2', "%{$n2}%", PDO::PARAM_STR);
			$stmt->bindValue(':needle3', "%{$n3}%", PDO::PARAM_STR);
			$stmt->bindValue(':needle4', "%{$n4}%", PDO::PARAM_STR);
		}
		
		$stmt->execute();

		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}catch(PDOException $e){
			self::$errormsg = 'error fetching tags count: '.$e->getMessage();//LinkBox\DataBase::$errormsg;
			LiLogger::log( self::$errormsg );
			return false;			
		}
		return $rows;
	}
	/*get tags for selected link*/
	public static function getLinkTags($linkID, $retType='array'){
		if( empty($linkID) OR ( $linkID < 1) ){
			self::$errormsg = 'Link id not provided.';
			LiLogger::log( 'Tag::getLinkTags error: '.self::$errormsg );
			return false;			
		}
		$sql = "SELECT tagName, tags.id_tag FROM tags LEFT JOIN tags_link ON tags.id_tag = tags_link.id_tag WHERE tags_link.id_link = :linkID ORDER BY tagName";
		try{
		$db = LinkBox\DataBase::connect();
		$stmt = $db->connection->prepare($sql);
		//$stmt = $this->PDO->prepare($sql);
		$stmt->bindValue(':linkID', $linkID, PDO::PARAM_INT);
		
		if(false === $stmt->execute() ){
			self::$errormsg = "Could not get tags for link";
			$logError = "Tag[getLinkTags] error executing statement::".implode(' ', $stmt->errorInfo() );
			LiLogger::log($logError);	
			return false;			
		}
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$entries = $stmt->fetchAll();
		
		if($entries === false ){
			self::$errormsg = "Could not get tags for link";			
			$logError = "Tag[getLinkTags] error: No entries returned: ".implode(' ', $stmt->errorInfo() );
			LiLogger::log($logError);				
			return false;
		}else{
			if($retType == 'array'){
				return $entries;			
			}elseif($retType == 'csv'){
				$tagtags = array();
				foreach($entries as $en){
					$tagtags[] = $en['tagName'];
				}
				return implode(',',$tagtags);
			}
		}
		}catch(PDOException $e){
			self::$errormsg = "Could not get tags for link";
			$logError = "Tag[getLinkTags] exception executing statement::".$e->getMessage();
			LiLogger::log($logError);
			return false;	
		}		
		
	}
	public function validate(){
		$name_ = Utils::cleanInput($this->name);
		if( empty( $name_ ) ){
			$this->errormsg = 'Empty tag name is not allowed.';
			return false;			
		}
		
		return true;
	}
	public static function validateParams($params){
		$name_ = Utils::cleanInput($params['tagName']);
		if( empty( $name_ ) ){
			self::$errormsg = 'Empty tag name is not allowed.';
			return false;			
		}
		
		return true;
	}
} // Tags end
/* ================================================== LINK =================================================================
*/
class Link extends DBObject{
	
	protected static $orm = array('table'=>'link', 'table_id'=>'id_link', 'where_uid'=>'id_user', 'is_uid'=>true);
	protected static $sqlGetAll = 'SELECT id_link, id_folder, url, id_user, created, lastVisited, isShared, title from link';
	protected static $sqlGetAllOrdered = 'SELECT id_link, id_folder, url, id_user, created, lastVisited, isShared, title from link ORDER BY title COLLATE NOCASE';
		
	private $shortname="-";
	
	public function __construct($url_, $title_='No title', $folderid, $linktags=0){
		parent::__construct();
		
		$datestamp = date_timestamp_get(date_create());
		$this->name = Utils::cleanInput($title_);
		$this->url = Utils::cleanInput($url_);
		$this->folderid = Utils::cleanInput($folderid);
		$this->uid = Auth::whoLoggedID();		
		$this->created = $datestamp;//Date();
		$this->lastVisited = $datestamp;//Date();		
		$this->linktagsCSV = Utils::cleanInput($linktags);	// comma separated string
		$this->sqlPDOSave = "INSERT INTO link( url, id_user, id_folder, created, lastVisited, isShared, title) VALUES(':url:', :uid:, :folderid:, :created:, :lastVis:, :shared:, ':title:')";
		$this->pdoPDOSave = "INSERT INTO link( url, id_user, id_folder, created, lastVisited, isShared, title) VALUES(:url, :uid, :folderid, :created, :lastVis, :shared, :title)";
	}
	public function save(){

		if( ! $this->validate() ){
			LiLogger::log( 'Link::save failed. Validation error: '.$this->errormsg );		
			return false;			
		}

		$stmt = $this->PDO->prepare($this->pdoPDOSave);
		$stmt->bindValue(':title', $this->name, PDO::PARAM_STR);
		$stmt->bindValue(':url', $this->url, PDO::PARAM_STR);
		$stmt->bindValue(':uid', $this->uid, PDO::PARAM_INT);
		$stmt->bindValue(':created', $this->created, PDO::PARAM_INT);
		$stmt->bindValue(':folderid', $this->folderid, PDO::PARAM_INT);
		$stmt->bindValue(':lastVis', $datestamp, PDO::PARAM_INT);
		$stmt->bindValue(':shared', 0, PDO::PARAM_INT);		
		
		$conn = $this->PDO;
		if( false === $this->savePDOStatement($stmt) ){
			$this->errormsg = 'Could not save link itself: '.$this->errormsg;//LinkBox\DataBase::$errormsg;
			LiLogger::log( $this->errormsg );				
			return false;
		}		
		$link_id = $conn->lastInsertId();			//LiLogger::log( "inserted link id == {$link_id}" );
			
		if( $link_id <= 0 ){
			$this->errormsg = 'Error retrieving saved link';
			LiLogger::log( $this->errormsg );				
			return false;
		}
		$tagsTrimmed = trim($this->linktagsCSV);
		if( empty( $tagsTrimmed ) ){
			return true;	//no tags to save - exiting
		}
		
		try {
			$conn->beginTransaction();
			
		$stmt = $conn->prepare("INSERT INTO tags( id_user, tagName) VALUES(:uid, :tagname)");
		$tagsarray = array();
		$tagsarray = array_map('trim',explode(",",$this->linktagsCSV));
		//$tagsarray = explode(',',$this->linktagsCSV);
		
		foreach( $tagsarray as $tag) {				//LiLogger::log( "tag string ={$tag} uid ={$this->uid} " );
		
			//check if current tag does persist yet
			$tagAr = Tag::getAllWhere("tagName = '{$tag}'");
		
			if($tagAr[0]['id_tag'] > 0){
				$tag_id = $tagAr[0]['id_tag'];
			}else{
			
				$stmt->bindValue(':uid', $this->uid, PDO::PARAM_INT);
				$stmt->bindValue(':tagname', $tag, PDO::PARAM_STR);
				$stmt->execute();
				sleep(1);
					
				$tag_id = $conn->lastInsertId();	//LiLogger::log( "inserted tag id == {$tag_id}" );
			}
				//assign to link this tag
					$stmtTL = $conn->prepare("INSERT INTO tags_link( id_tag, id_link) VALUES(:idtag, :idlink)");
					$stmtTL->bindValue(':idtag', $tag_id, PDO::PARAM_INT);
					$stmtTL->bindValue(':idlink', $link_id, PDO::PARAM_INT);
					$stmtTL->execute();
					sleep(1);			
		}
		
		$conn->commit();
		return true;
	
		} catch(PDOException $e) {
			if(stripos($e->getMessage(), 'DATABASE IS LOCKED') !== false) {
				// This should be specific to SQLite, sleep for 0.25 seconds
				// and try again.  We do have to commit the open transaction first though
				$conn->commit();
				usleep(250000);
			} else {
				$conn->rollBack();
				//Link::load($link_id)->delete();	//delete whole link -- 
				DBObject::deleteEntry('link', $link_id, 'id_link');
				//throw $e;
				$this->errormsg = 'error performing tags transaction: '.$e->getMessage();//LinkBox\DataBase::$errormsg;
				LiLogger::log( $this->errormsg );
				return false;
			}
		}
		
	}
	public static function load($id){
	//TODO make long one query?
		$load = self::getFromDB($id);
		if(empty($load)){return false;}else{
		$me = new Link($load['url'], $load['title'], $load['id_folder'] );
		$me->id = $load['id_link'];
		$me->uid = $load['id_user'];		
		$me->created = $load['created'];
		$me->lastVisited = $load['lastVisited'];
		return $me;}
	}
	/* validate before save */
	public function validate(){

		if( empty($this->name) ){
			$this->errormsg = 'Empty link name is not allowed.';
			return false;			
		}
		if( empty($this->url) ){
			$this->errormsg = 'Empty link url is not allowed.';
			return false;			
		}
		if( empty($this->folderid) OR $this->folderid < 1){
			$this->errormsg = 'Every link should belong to some folder.';
			return false;			
		}
		return true;
	}
	/* validate before update */
	public static function validateParams($params){
		if( empty($params) ){
			self::$errormsg = 'Empty link params.';
			return false;			
		}
		$tt_ = Utils::cleanInput($params['title']);
		$url_ = Utils::cleanInput($params['url']);
		$fldi_ = Utils::cleanInput($params['id_folder']);
		if( empty($tt_) ){
			self::$errormsg = 'Empty link name is not allowed.';
			return false;			
		}
		if( empty($url_) ){
			self::$errormsg = 'Empty link url is not allowed.';
			return false;			
		}
		if( empty($fldi_) OR $fldi_ < 1){
			self::$errormsg = 'Every link should belong to some folder.';
			return false;			
		}
		return true;
	}
/* get links for desired folder(s), needed offset */
	public static function fetchLinks($folderIds=0, $offset=0, $sorting=''){
		
		$linksCount = Settings::Val('pagerLimit');	//offset == $offset		//LiLogger::log("got val {$linksCount}");
		if($linksCount === false){$linksCount = 0;}
		if(empty($offset) ){$offset = 0;}
		
		if($linksCount == 0){$limitSQL = '';
		}else{$limitSQL = "LIMIT {$linksCount} OFFSET {$offset}";}
		if($offset == -2){$limitSQL = '';}	//no limit		//LiLogger::log("limitSQL {$limitSQL} folderIds {$folderIds}");
		if( empty($folderIds) ){
			$list = Link::getAll($limitSQL);
		}else{
			if( is_int($folderIds) OR is_string($folderIds) ){
				$list = Link::getAllWhere("WHERE id_folder={$folderIds}", $limitSQL) ;
			}elseif( is_array($folderIds) ){
				$idsString = implode(",", $folderIds);
				$list = Link::getAllWhere("WHERE id_folder IN ( {$idsString} )", $limitSQL) ;
			}else{
				self::$errormsg = 'Could not fetch links.';
				return false;
			}
		}
		return $list;
		
	}
/* search links for given needle, needed offset 
$needle is a stringified csv array */
	public static function searchLinks($needle, $offset=0, $sorting=''){

		if( empty($needle) ){return false;}
		
		$linksCount = Settings::Val('pagerLimit');	//offset == $offset		//LiLogger::log("got val {$linksCount}");
		if($linksCount === false){$linksCount = 0;}
		if(empty($offset) ){$offset = 0;}
		
		if($linksCount == 0){$limitSQL = '';
		}else{$limitSQL = "LIMIT {$linksCount} OFFSET {$offset}";}
		if($offset == -2){$limitSQL = '';}	//no limit		//LiLogger::log("limitSQL {$limitSQL} folderIds {$folderIds}");
		
		//get needless if there are some
		if( false !== strpos($needle, ',') ){
			$arrN = explode(',',$needle);
			$tcount = count( $arrN );
		}else{
			$tcount = 1;
		}
		try{
		$db = LinkBox\DataBase::connect();
	
		if($tcount == 1){
			$sql = "SELECT DISTINCT(link.id_link), id_folder, url, id_user, created, lastVisited, isShared, title 
from link LEFT JOIN tags_link ON link.id_link = tags_link.id_link WHERE link.id_user=:uid
AND title LIKE :needle1 ORDER BY title COLLATE NOCASE".' '.$limitSQL;
		}elseif($tcount == 2){
			$sql = "SELECT DISTINCT(link.id_link), id_folder, url, id_user, created, lastVisited, isShared, title 
from link LEFT JOIN tags_link ON link.id_link = tags_link.id_link WHERE link.id_user=:uid
AND title LIKE :needle1 OR title LIKE :needle2 ORDER BY title COLLATE NOCASE".' '.$limitSQL;
		}elseif($tcount == 3){
			$sql = "SELECT DISTINCT(link.id_link), id_folder, url, id_user, created, lastVisited, isShared, title 
from link LEFT JOIN tags_link ON link.id_link = tags_link.id_link WHERE link.id_user=:uid
AND title LIKE :needle1 OR title LIKE :needle2 OR title LIKE :needle3 ORDER BY title COLLATE NOCASE".' '.$limitSQL;
		}elseif($tcount >= 4){
			$sql = "SELECT DISTINCT(link.id_link), id_folder, url, id_user, created, lastVisited, isShared, title 
from link LEFT JOIN tags_link ON link.id_link = tags_link.id_link WHERE link.id_user=:uid
AND title LIKE :needle1 OR title LIKE :needle2 OR title LIKE :needle3 OR title LIKE :needle4 ORDER BY title COLLATE NOCASE".' '.$limitSQL;
		}
		
		$stmt = $db->connection->prepare($sql);
		$stmt->bindValue(':uid', Auth::whoLoggedID());
		if($tcount == 1){
			$stmt->bindValue(':needle1', "%{$needle}%", PDO::PARAM_STR);
		}elseif($tcount == 2){
			$n1 = $arrN[0];	$n2 = $arrN[1];
			$stmt->bindValue(':needle1', "%{$n1}%", PDO::PARAM_STR);
			$stmt->bindValue(':needle2', "%{$n2}%", PDO::PARAM_STR);
		}elseif($tcount == 3){
			$n1 = $arrN[0];	$n2 = $arrN[1]; $n3 = $arrN[2];
			$stmt->bindValue(':needle1', "%{$n1}%", PDO::PARAM_STR);
			$stmt->bindValue(':needle2', "%{$n2}%", PDO::PARAM_STR);
			$stmt->bindValue(':needle3', "%{$n3}%", PDO::PARAM_STR);			
		}elseif($tcount >= 4){
			$n1 = $arrN[0];	$n2 = $arrN[1]; $n3 = $arrN[2];; $n4 = $arrN[3];
			$stmt->bindValue(':needle1', "%{$n1}%", PDO::PARAM_STR);
			$stmt->bindValue(':needle2', "%{$n2}%", PDO::PARAM_STR);
			$stmt->bindValue(':needle3', "%{$n3}%", PDO::PARAM_STR);
			$stmt->bindValue(':needle4', "%{$n4}%", PDO::PARAM_STR);
		}
		
		$stmt->execute();

		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}catch(PDOException $e){
			self::$errormsg = 'error searching link: '.$e->getMessage();//LinkBox\DataBase::$errormsg;
			LiLogger::log( self::$errormsg );
			return false;			
		}
		return $rows;
	
	}
	
/* get links for desired folder(s), needed offset 
$tags is a stringified csv array */
	public static function fetchTaggedLinks($tags, $offset=0, $sorting=''){
				if( empty($tags) ){return false;}
		$tags = Utils::cleanInput($tags);	
		$linksCount = Settings::Val('pagerLimit');	//offset == $offset		//LiLogger::log("got val {$linksCount}");
		if($linksCount === false){$linksCount = 0;}
		if(empty($offset) ){$offset = 0;}
		
		if($linksCount == 0){$limitSQL = '';
		}else{$limitSQL = "LIMIT {$linksCount} OFFSET {$offset}";}
		if($offset == -2){$limitSQL = '';}	//no limit		//LiLogger::log("limitSQL {$limitSQL} folderIds {$folderIds}");
		
		$arrP = array();
		//get tags if there are some
		if( false !== strpos($tags, ',') ){
			$arrN = explode(',',$tags);
			$tcount = count( $arrN );
			foreach($arrN as $t){
				$p = Utils::cleanInput($t);
				$p = intval($p);
				if( !empty( $p )){$arrP[] = $p;}
			}
			$tcount = count( $arrP );
			$tags = implode(',',$arrP);
		}else{
			$tcount = 1;
		}
		try{
		$db = LinkBox\DataBase::connect();

		if($tcount == 1){
			$sql = 'SELECT DISTINCT(link.id_link), id_folder, url, id_user, created, lastVisited, isShared, title 
from link LEFT JOIN tags_link ON link.id_link = tags_link.id_link WHERE link.id_user=:uid
AND id_tag = :tag ORDER BY title COLLATE NOCASE'.' '.$limitSQL;
		}elseif($tcount >= 2){
			$sql = "SELECT DISTINCT(link.id_link), id_folder, url, id_user, created, lastVisited, isShared, title 
from link LEFT JOIN tags_link ON link.id_link = tags_link.id_link WHERE link.id_user=:uid
AND id_tag IN( {$tags} ) ORDER BY title COLLATE NOCASE".' '.$limitSQL;
		}

$db->connection->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );

		$stmt = $db->connection->prepare($sql);
		if (!$stmt) {
		//echo "\PDO::errorInfo():\n";
		//print_r($db->connection->errorInfo());
			$err = $db->connection->errorInfo();
			$err = $err.PDO::errorInfo();
			self::$errormsg = 'error preparing statement: '.$err;//LinkBox\DataBase::$errormsg;
			LiLogger::log( self::$errormsg );
		}

		$stmt->bindValue(':uid', Auth::whoLoggedID(), PDO::PARAM_INT);
		if($tcount == 1){
			$stmt->bindValue(':tag', $tags, PDO::PARAM_INT);
		}		
		$stmt->execute();

		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}catch(PDOException $e){
			self::$errormsg = 'error fetching links: '.$e->getMessage();//LinkBox\DataBase::$errormsg;
			LiLogger::log( self::$errormsg );
			return false;			
		}
		
		if(count($rows) < 1){
			return false;
		}else{
			return $rows;
		}
	}
} //Link end



















/* dd==========================================================================================================================================
*/
class Way extends DBObject{
	
	protected static $orm = array('table'=>'pitstop', 'table_id'=>'id_pitstop', 'is_uid'=>false);
	protected static $sqlGetAll = 'SELECT id_pitstop, pitstop.id_station, station.shortName, station.name AS statName, id_itinerary, itinerary.name AS itinName, id_pittype, `time` FROM pitstop LEFT JOIN station ON pitstop.id_station = station.id_station LEFT JOIN itinerary ON pitstop.id_itinerary = itinerary.id_itin';
	protected static $sqlGetAllOrdered = 'SELECT id_pitstop, pitstop.id_station, station.shortName, station.name AS statName, id_itinerary, itinerary.name AS itinName, id_pittype, `time` FROM pitstop LEFT JOIN station ON pitstop.id_station = station.id_station LEFT JOIN itinerary ON pitstop.id_itinerary = itinerary.id_itin ORDER BY `time`';

	private $itinerary = 0; //main itinerary
	private $pitstops = null; //['pitstop_id'=>time]
	private $pitstopsTotal = 0;
	private $pitstopsMaxId = 0;
	
	public function __construct($post_){
		$this->name = "way";//Utils::cleanInput($name_);
		$this->sqlPDOSave = "";//"INSERT INTO station(name) VALUES(':1:')";
		$this->pitstopsTotal = $post_['totalstops'];
		$this->pitstopsMaxId = $post_['laststopID'];
		$this->itinerary = $post_['itinerarySelect'];
		/*for($i=1; $i <= $this->pitstopsTotal; $i++) {
			if(!empty($post_['stationTime'])){
			$this->pitstops[$i] = array(
				'station'=>$post_['station'], 
			'time'=>Utils::HHmm2Int( Utils::cleanInput( $post_['stationTime'])) ,
				'pitType'=>$post_['pitType'],
									);
									}
		}
		*/for($i=1; $i <= $this->pitstopsMaxId; $i++) {
			if(!empty($post_['stationTime'.$i])){
			$this->pitstops[$i] = array(
				'station'=>$post_['station'.$i], 
			'time'=>Utils::HHmm2Int( Utils::cleanInput( $post_['stationTime'.$i])) ,
				'pitType'=>$post_['pitType'.$i],
									);
									}
		}
	}
		
	public static function newEditable($post_){
		$eWay = new Way('dummy');
		$eWay->pitstopsTotal = $post_['totalstopsEDIT'];
		$eWay->pitstopsMaxId = $post_['laststopIDEDIT'];
		$eWay->itinerary = $post_['itinerarySelectEdit'];
		
		for($i=1; $i <= $eWay->pitstopsMaxId; $i++) {
			if(!empty($post_['stationTimeED'.$i])){
				$eWay->pitstops[$i] = array(
					'station'=>$post_['stationED'.$i], 
					'time'=>Utils::HHmm2Int( Utils::cleanInput( $post_['stationTimeED'.$i])) ,
					'pitType'=>$post_['pitTypeED'.$i],
										);
			}
		}
		
		return $eWay;
	}
	
	public static function load($id){
		$load = self::getFromDB($id);
		if(empty($load)){return false;}else{
			$load['laststopID'] = 0;
			$me = new Way($load);
			$me->id = $load['id_pitstop'];
		return $me;}
	}
	public function save($post_){
		//$pdosql = str_replace(':1:', $this->name, $this->sqlPDOSave);
		//phpnet//$conn = new PDO('sqlite:C:\path\to\file.sqlite');
		//phpnet//$stmt = $conn->prepare('INSERT INTO my_table(my_id, my_value) VALUES(?, ?)');
		//$conn = $this->getPDO(); //get raw connection
		$db = LinkBox\DataBase::connect(); //get raw connection
		$conn = $db::getPDO(); //get raw connection
		
		if($conn === false){
			$this->errormsg = 'error while saving pitstops into DB: '.LinkBox\DataBase::$errormsg;
			LiLogger::log( $this->errormsg );
			return false;
		}
			
		if( empty($this->pitstops) ){
			$this->errormsg = 'Could not save itinerary: no pitstops in itinerary.';
			LiLogger::log( $this->errormsg );
			return false;				
		}
			
		$stmt = $conn->prepare('INSERT INTO pitstop(id_station, time, id_pittype, id_itinerary) VALUES(:id_stat, :time, :id_pittyp, :id_itin)');
			
		try {
        $conn->beginTransaction();
			//var_dump($this->pitstops);

        foreach($this->pitstops as $pit_num => $pitstop) {
            $stmt->bindValue(':id_stat', $pitstop['station'], PDO::PARAM_INT);
            $stmt->bindValue(':time', $pitstop['time'], PDO::PARAM_INT);
            $stmt->bindValue(':id_pittyp', $pitstop['pitType'], PDO::PARAM_INT);
            $stmt->bindValue(':id_itin', $this->itinerary, PDO::PARAM_INT);
            $stmt->execute();
            sleep(1);
        }
        $conn->commit();
    } catch(PDOException $e) {
        if(stripos($e->getMessage(), 'DATABASE IS LOCKED') !== false) {
            // This should be specific to SQLite, sleep for 0.25 seconds
            // and try again.  We do have to commit the open transaction first though
            $conn->commit();
            usleep(250000);
        } else {
            $conn->rollBack();
            //throw $e;
			$this->errormsg = 'error performing pitstops transaction: '.$e->getMessage();//LinkBox\DataBase::$errormsg;
			LiLogger::log( $this->errormsg );
			return false;
        }
    }
		if ($stmt->rowCount()){
			$this->errormsg = 'Saved successfully.';
			return true;
		} else{
			$this->errormsg = 'Failure: not saved.';
			return false;
		}
		//return $this->saveObject($pdosql);
	}
	/* ====================================================== editPitstops ===============================================
	*/
	public function editPitstops($post_){
		
		$db = LinkBox\DataBase::connect(); //get raw connection
		$conn = $db::getPDO(); //get raw connection
		
		if($conn === false){
			$this->errormsg = 'error while editing pitstops: '.LinkBox\DataBase::$errormsg;
			LiLogger::log( $this->errormsg );
			return false;
		}
			
		if( empty($this->pitstops) ){
			$this->errormsg = 'Could not edit itinerary: no pitstops in new itinerary.';
			LiLogger::log( $this->errormsg );
			return false;				
		}
		$itin_id = $post_['itinerarySelectEdit'];
		if( empty($itin_id) ){
			$this->errormsg = 'Could not edit itinerary: no itinerary ID.';
			LiLogger::log( $this->errormsg );
			return false;				
		}
		
		$stmt = $conn->prepare('INSERT INTO pitstop(id_station, time, id_pittype, id_itinerary) VALUES(:id_stat, :time, :id_pittyp, :id_itin)');
			
		try {
        $conn->beginTransaction();
			//var_dump($this->pitstops);
		$db->executeDelete("DELETE FROM pitstop WHERE id_itinerary={$itin_id}") ;
		
        foreach($this->pitstops as $pit_num => $pitstop) {
            $stmt->bindValue(':id_stat', $pitstop['station'], PDO::PARAM_INT);
            $stmt->bindValue(':time', $pitstop['time'], PDO::PARAM_INT);
            $stmt->bindValue(':id_pittyp', $pitstop['pitType'], PDO::PARAM_INT);
            $stmt->bindValue(':id_itin', $this->itinerary, PDO::PARAM_INT);
            $stmt->execute();
            sleep(1);
        }
        $conn->commit();
    } catch(PDOException $e) {
        if(stripos($e->getMessage(), 'DATABASE IS LOCKED') !== false) {
            // This should be specific to SQLite, sleep for 0.25 seconds
            // and try again.  We do have to commit the open transaction first though
            $conn->commit();
            usleep(250000);
        } else {
            $conn->rollBack();
            //throw $e;
			$this->errormsg = 'error performing pitstops transaction: '.$e->getMessage();//LinkBox\DataBase::$errormsg;
			LiLogger::log( $this->errormsg );
			return false;
        }
    }
		if ($stmt->rowCount()){
			$this->errormsg = 'Saved successfully.';
			return true;
		} else{
			$this->errormsg = 'Failure: not saved.';
			return false;
		}
		//return $this->saveObject($pdosql);
	}
/*
all pitstops for desired itin. for compatibility default == -2 (all)
for expanding -3 :: select by destination
*/	
	public static function getPitstopsByItinerary($id_itin = -2, $id_destin = -2){
		
		if($id_itin == -2){
			$pitstops = self::getAll();
		}else if($id_itin == -3){
			$sql = "SELECT id_itin FROM itinerary WHERE destination = {$id_destin}";
			$itins = self::getEntriesArrayBySQL($sql);
			if($itins === false){
		self::$errormsg = 'Way[getPitstopsByItinerary]::error';	LiLogger::log( self::$errormsg );
			return false;}
			$itinsA = array();	
			foreach($itins as $it){array_push($itinsA,$it['id_itin']);}
			$id_itins = implode(',',$itinsA);
			$clause="WHERE id_itinerary IN ( {$id_itins} )";		//LiLogger::log($clause);die();

			$pitstops = self::getAllWhere($clause); 		
		}
		else{
			$clause="WHERE id_itinerary = {$id_itin}";
			$pitstops = self::getAllWhere($clause); 
		}			
		if( empty($pitstops) ) {
		self::$errormsg = 'Way[getPitstopsByItinerary]::error2';LiLogger::log( self::$errormsg );
		return false;}
//var_dump($pitstops);	//die();
		$ways = array();
		$id__it_name = array();
		$id__stat_name = array();
		
		foreach($pitstops as $stop){
			$id__it_name[$stop['id_itinerary']] = $stop['itinName']; //a:3:{i:1;N;i:5;N;i:6;N;}
			$id__stat_name[$stop['id_station']] = $stop['statName'];
		}		//LiLogger::log(serialize($id__it_name));
		foreach($id__it_name as $it_id => $val){
			$ways[(string)$it_id] = array();
			$ways[((string)$it_id)]['name'] = $val;
		}		//Logger::log(serialize($ways));
		foreach($pitstops as $stop){
			array_push($ways[(string)$stop['id_itinerary']], array($stop['shortName']=>$stop['time']) );
		}		//var_dump($ways); 
		return $ways;
	}
/*
all pitstops for desired destination. proxy for getPitstopsByItinerary
*/
	public static function getPitstopsByDestination($id_destin){
		
		if( empty($id_destin) ) {
			self::$errormsg = 'getPitstopsByDestination:no id_destin';
			LiLogger::log( self::$errormsg );
		return false;}
		
		return self::getPitstopsByItinerary(-3, $id_destin);
	}		
/*
all pitstops for desired sequence. proxy for getPitstopsByDestination
*/
	public static function getPitstopsBySequence($id_seq){
		if( empty($id_seq) ) {
			self::$errormsg = 'getPitstopsBySequence:no id_seq';
			LiLogger::log( self::$errormsg );
		return false;}
		//get id destination for this sequence
		$sql = "SELECT destination FROM sequences WHERE id_seq = {$id_seq}";
		$destA = self::getEntriesArrayBySQL($sql);
		if($dest === false){
			self::$errormsg = 'Way[getPitstopsBySequence]::error';	LiLogger::log( self::$errormsg );
			return false;}	
	//var_dump($destA[0]['destination']);
		$id_dest = $destA[0]['destination'];
		
		return self::getPitstopsByDestination($id_dest);	
	}

	
	public static function GetPitsCountForItinerary($itin_id){
		if(empty($itin_id)) return false;
		
		$itin_id = Utils::cleanInput($itin_id);
		$sql = "SELECT COUNT (id_pitstop) FROM pitstop WHERE id_itinerary = {$itin_id}";
	
		$db = LinkBox\DataBase::connect(); //get raw connection		
		$conn = $db::getPDO(); //get raw connection
		$count = $conn->query($sql)->fetchColumn();
//var_dump($count);
		if($count > 0){return $count;}else{return 0;}
	}
	
	public static function DeletePitstop($pit_id){
		if(empty($pit_id)) return false;
		
		$db = LinkBox\DataBase::connect(); //get raw connection
		$conn = $db::getPDO(); //get raw connection
		return ( $db->executeDelete("DELETE FROM pitstop WHERE id_pitstop={$pit_id}") );
	}
	
	public static function DeleteItinStations($itin_id){
		if(empty($itin_id)) return false;
		
		$db = LinkBox\DataBase::connect(); //get raw connection
		$conn = $db::getPDO(); //get raw connection
		return ( $db->executeDelete("DELETE FROM pitstop WHERE id_itinerary={$itin_id}") );
	}

}
	
/*===============================================================		sequencesStations		===============================================
* set of stations for X-axis of graph
*/
class sequencesStations extends DBObject{

	protected static $orm = array('table'=>'seq_stations', 'table_id'=>'id_ss', 'is_uid'=>false);
	protected static $sqlGetAll = 'SELECT id_ss, seq_stations.id_station, orderal, id_pitstoptype, station.shortName, station.name AS statName FROM seq_stations LEFT JOIN station ON seq_stations.id_station = station.id_station';
	protected static $sqlGetAllOrdered = 'SELECT id_ss, seq_stations.id_station, orderal, id_pitstoptype, station.shortName, station.name AS statName FROM seq_stations LEFT JOIN station ON seq_stations.id_station = station.id_station ORDER BY orderal';
	
	private $sequence = 0; //main sequence
	private $seq_stations = null; //['pitstop_id'=>time]
	private $seqTotal = 0;
	private $seqLastID = 0;
	
	public function __construct($post_){
		$this->name = "sequencesStations";//Utils::cleanInput($name_);
		$this->sqlPDOSave = "";//"INSERT INTO station(name) VALUES(':1:')";
		$this->sequence = $post_['sequencesSelect'];
		$this->seqTotal = $post_['totalsequences'];
		$this->seqLastID = $post_['lastseqID'];
			
		for($i=1; $i <= $this->seqLastID; $i++) {
			if( ! empty( $post_['station'.$i] )){
				if($post_['station'.$i] <> -1){
					$this->seq_stations[$i] = array(
						'station'=>Utils::cleanInput($post_['station'.$i]), 
						'orderal'=>Utils::cleanInput($post_['orderal'.$i]),
						'pitType'=>Utils::cleanInput($post_['pitType'.$i]),
											);	
					}		
			}
		}
	}
	public static function newEditable($post_){
		$eWay = new sequencesStations('dummy');
		$eWay->sequence = $post_['sequencesSelectEdit'];
		$eWay->seqTotal = $post_['totalseqEDIT'];
		$eWay->seqLastID = $post_['lastseqIDEDIT'];
		
		for($i=1; $i <= $eWay->seqLastID; $i++) {
			if( ! empty( $post_['stationSSED'.$i] )){
				if($post_['stationSSED'.$i] <> -1){
					$eWay->seq_stations[$i] = array(
						'station'=>Utils::cleanInput($post_['stationSSED'.$i]), 
						'orderal'=>Utils::cleanInput($post_['orderalED'.$i]),
						'pitType'=>Utils::cleanInput($post_['seqTypeED'.$i]),
											);	
					}			
			}
		}
		return $eWay;
	}
	
	public static function load($id){
		$load = self::getFromDB($id);
		if(empty($load)){return false;}else{
			$load['lastseqIDEDIT'] = 0;
			$me = new sequencesStations($load);
			$me->id = $load['id_ss'];
		return $me;}
	}
/*====================== save sequences =======================
*/	
	public function save($post_){

		$db = LinkBox\DataBase::connect(); //get raw connection
		$conn = $db::getPDO(); //get raw connection
		
		if($conn === false){
			self::$errormsg = 'error while saving seq-stations into DB: '.LinkBox\DataBase::$errormsg;
			LiLogger::log( self::$errormsg );
			return false;
		}
		$stmt = $conn->prepare('INSERT INTO seq_stations(id_seq, id_station, id_pitstoptype, orderal) VALUES(:id_seq, :id_stat, :id_pittyp, :orderal)');
		
		try {
        $conn->beginTransaction();
			//var_dump($this->pitstops);
        foreach($this->seq_stations as $stat_order => $station) {
            $stmt->bindValue(':id_stat', $station['station'], PDO::PARAM_INT);
            $stmt->bindValue(':orderal', $station['orderal'], PDO::PARAM_INT);
            $stmt->bindValue(':id_pittyp', $station['pitType'], PDO::PARAM_INT);
            $stmt->bindValue(':id_seq', $this->sequence, PDO::PARAM_INT);
            $stmt->execute();
            sleep(1);
        }
        $conn->commit();
    } catch(PDOException $e) {
        if(stripos($e->getMessage(), 'DATABASE IS LOCKED') !== false) {
            // This should be specific to SQLite, sleep for 0.25 seconds
            // and try again.  We do have to commit the open transaction first though
            $conn->commit();
            usleep(250000);
        } else {
            $conn->rollBack();
            //throw $e;
			self::$errormsg = 'error performing sequences transaction: '.$e->getMessage();//LinkBox\DataBase::$errormsg;
			LiLogger::log( self::$errormsg );
			return false;
        }
    }
		if ($stmt->rowCount()){
			$this->errormsg = 'Saved successfully.';
			return true;
		} else{
			$this->errormsg = 'Failure: not saved.';
			return false;
		}
		//return $this->saveObject($pdosql);
	}
	
	/*====================== edit sequences =======================
*/	
	public function editSequences($post_){

		$db = LinkBox\DataBase::connect(); //get raw connection
		$conn = $db::getPDO(); //get raw connection
		
		if($conn === false){
			self::$errormsg = 'error while editing seq-stations. '.LinkBox\DataBase::$errormsg;
			LiLogger::log( self::$errormsg );
			return false;
		}
		if( empty($this->seq_stations) ){
			$this->errormsg = 'Could not edit Sequence: no sequences in new object.';
			LiLogger::log( $this->errormsg );
			return false;				
		}
		$itin_id = $post_['sequencesSelectEdit'];
		if( empty($itin_id) ){
			$this->errormsg = 'Could not edit Sequence: no sequence ID.';
			LiLogger::log( $this->errormsg );
			return false;				
		}
		
		$stmt = $conn->prepare('INSERT INTO seq_stations(id_seq, id_station, id_pitstoptype, orderal) VALUES(:id_seq, :id_stat, :id_pittyp, :orderal)');
		
		try {
        $conn->beginTransaction();
			//var_dump($this->pitstops);
		$db->executeDelete("DELETE FROM seq_stations WHERE id_seq={$itin_id}") ;
			
        foreach($this->seq_stations as $stat_order => $station) {
            $stmt->bindValue(':id_stat', $station['station'], PDO::PARAM_INT);
            $stmt->bindValue(':orderal', $station['orderal'], PDO::PARAM_INT);
            $stmt->bindValue(':id_pittyp', $station['pitType'], PDO::PARAM_INT);
            $stmt->bindValue(':id_seq', $this->sequence, PDO::PARAM_INT);
            $stmt->execute();
            sleep(1);
        }
        $conn->commit();
    } catch(PDOException $e) {
        if(stripos($e->getMessage(), 'DATABASE IS LOCKED') !== false) {
            // This should be specific to SQLite, sleep for 0.25 seconds
            // and try again.  We do have to commit the open transaction first though
            $conn->commit();
            usleep(250000);
        } else {
            $conn->rollBack();
            //throw $e;
			self::$errormsg = 'error performing sequences transaction: '.$e->getMessage();
			$this->errormsg = self::$errormsg;
			//LinkBox\DataBase::$errormsg;
			LiLogger::log( self::$errormsg );
			return false;
        }
    }
		if ($stmt->rowCount()){
			$this->errormsg = 'Saved successfully.';
			LiLogger::log( "editSequences Saved successfully. Count: ".$stmt->rowCount() );
			return true;
		} else{
			$this->errormsg = 'Failure: not saved.';
			return false;
		}
		//return $this->saveObject($pdosql);
	}
		
	
	public static function getSeqStatNamesBySequenceID($seq_id){
		
/* SELECT id_ss, seq_stations.id_station, orderal, station.shortName, station.name AS statName 
FROM seq_stations LEFT JOIN station ON seq_stations.id_station = station.id_station 
WHERE seq_stations.id_seq = 1	ORDER BY orderal ; */
		$clause = "WHERE seq_stations.id_seq = {$seq_id} ORDER BY orderal";
		$seqstats = self::getAllWhere($clause );
//echo'<pre>'; 	var_dump($seqstats);	echo'</pre>';
		if( $seqstats === false ) {self::$errormsg='SNames: Could not obtain sequences by id';
		return false;}
		
		$statNames = array();

		foreach($seqstats as $t=>$seq){
			array_push($statNames, $seq['shortName']);
			//$statNames[] = $seq['shortName'];
		}
//LiLogger::log( "seqstat".serialize($statNames) );		
		return $statNames;
	}
	
	public static function getSeqStationsBySequenceID($seq_id){
		
/* SELECT id_ss, seq_stations.id_station, orderal, station.shortName, station.name AS statName 
FROM seq_stations LEFT JOIN station ON seq_stations.id_station = station.id_station 
WHERE seq_stations.id_seq = 1	ORDER BY orderal ; */
		$clause = "WHERE seq_stations.id_seq = {$seq_id} ORDER BY orderal";
		$seqstats = self::getAllWhere($clause );
//echo'<pre>'; 	var_dump($seqstats);	echo'</pre>';
		if( $seqstats === false ) {self::$errormsg='SStations: Could not obtain sequences by id';
		return false;}
		return $seqstats;
		/*
		$statNames = array();
		foreach($seqstats as $t=>$seq){
			array_push($statNames, $seq['shortName']);
			//$statNames[] = $seq['shortName'];
		}
//LiLogger::log( "seqstat".serialize($statNames) );		
		return $statNames;*/
	}
	
	public static function GetPitsCountForSequence($seq_id){
		if(empty($seq_id)) return false;
		
		$seq_id = Utils::cleanInput($seq_id);
		$sql = "SELECT COUNT (id_ss) FROM seq_stations WHERE id_seq = {$seq_id}";
	
		$db = LinkBox\DataBase::connect(); //get raw connection		
		$conn = $db::getPDO(); //get raw connection
		$count = $conn->query($sql)->fetchColumn();
//var_dump($count);
		if($count > 0){return $count;}else{return 0;}
	}
	
	public static function DeleteSeqStations($seq_id){
		if(empty($seq_id)) return false;
		
		$db = LinkBox\DataBase::connect(); //get raw connection
		$conn = $db::getPDO(); //get raw connection
		return ( $db->executeDelete("DELETE FROM seq_stations WHERE id_seq={$seq_id}") );
	}
}

class Itinerary extends DBObject{

	protected static $orm = array('table'=>'itinerary', 'table_id'=>'id_itin', 'is_uid'=>true, 'where_uid'=>'itinerary.uid');
	protected static $sqlGetAll = 'SELECT id_itin, itinerary.name, start_station, start_time, destination, station.name AS statName, itinerary.uid from itinerary LEFT JOIN station ON itinerary.start_station = station.id_station';
	protected static $sqlGetAllOrdered = 'SELECT id_itin, itinerary.name, start_station, start_time, destination, station.name AS statName, itinerary.uid from itinerary LEFT JOIN station ON itinerary.start_station = station.id_station ORDER BY itinerary.name';

	private $obus;
	private $station;
	private $startTime;
	private $destination;
	public function __construct($itineraryName_, $obus_, $station_, $startTime_, $destination_){
		$this->name = Utils::cleanInput($itineraryName_);
		$this->obus = Utils::cleanInput($obus_);
		$this->station = Utils::cleanInput($station_);
		$this->destination = Utils::cleanInput($destination_);
		$this->startTime = Utils::HHmm2Int( Utils::cleanInput($startTime_) );
		$this->uid = Auth::whoLoggedID();
		$this->sqlPDOSave = "INSERT INTO itinerary(name, start_station, destination, start_time, uid) VALUES(':iName:', :iStSt:, :iDest:, :iStTime:, :iuid:)";
	}
	public function save(){
		$arrParameters = array(
		":iName:"=>$this->name,
		":iStSt:"=>$this->station,
		":iDest:"=>$this->destination,
		":iuid:"=>$this->uid,
		":iStTime:"=>$this->startTime);
		$pdosql = strtr($this->sqlPDOSave, $arrParameters);
		//$pdosql = str_replace(':1:', $this->name, $this->sqlPDOSave);
		//print_r($pdosql);
		return $this->saveObject($pdosql);
	}
	
	public static function load($id){
		$load = self::getFromDB($id);
		if(empty($load)){return false;}else{
		$me = new Itinerary($load['name'], 'no-obus',$load['start_station'], $load['start_time'],$load['destination'] );
		$me->id = $load['id_itin'];
		return $me;}
	}
}
class Sequence extends DBObject{
	
	protected static $orm = array('table'=>'sequences', 'table_id'=>'id_seq', 'is_uid'=>true);
	protected static $sqlGetAll = 'SELECT id_seq, name, destination, uid from sequences';
	protected static $sqlGetAllOrdered = 'SELECT id_seq, name, destination, uid from sequences ORDER BY name';
	private $destination;

	public function __construct($seqName_, $dest_){
		$this->name = Utils::cleanInput($seqName_);
		$this->destination = Utils::cleanInput($dest_);
		$this->uid = Auth::whoLoggedID();
		$this->sqlPDOSave = "INSERT INTO sequences(name, destination, uid) VALUES(':iName:', :iDest:, :iuid:)";
	}
	public function save(){
//LiLogger::log("seq save. dest id{$this->destination}");	
		//$dest = Destination::getEntryByID($this->destination);
		$destArr = Destination::getAllWhere("WHERE id_dest = {$this->destination}"); //### REFACTORED ###
		$dest = $destArr[0];
//LiLogger::log("res".serialize($dest));		
		$destName = $dest['name'];
		if(empty($destName)){
			self::$errormsg = 'Sequence[save]: could not obtain destination name.';
			LiLogger::log(self::$errormsg);
			return false;
		}
		$seqDestName = "[ {$destName} ] via {$this->name}";
		$arrParameters = array(
		":iName:"=>$seqDestName,
		":iuid:"=>$this->uid,
		":iDest:"=>$this->destination);
		$pdosql = strtr($this->sqlPDOSave, $arrParameters);
		//$pdosql = str_replace(':1:', $this->name, $this->sqlPDOSave);
		//print_r($pdosql);
		return $this->saveObject($pdosql);
	}
	
	public static function load($id){
		$load = self::getFromDB($id);
		if(empty($load)){return false;}else{
		$me = new Sequence($load['name'], $load['destination'] );
		$me->id = $load['id_seq'];
		return $me;}
	}
	
}

class User extends DBObject{

	private $db;
	
	protected static $orm = array('table'=>'user', 'table_id'=>'id_user');
	protected static $sqlGetAll = 'SELECT id_user, name, email, pwdHash, regDate from user';
	protected static $sqlGetAllOrdered = 'SELECT id_user, name, email, pwdHash, regDate from user ORDER BY name';
	
	public static $errormsg;
	private $errorEmptyFields;
	
	public $id;
	public $regDate;
	public $email;
	public $name;
	public $pwdHash;
	
	public function __construct($name_, $email_, $pwd, $regDate_ = 0 ){
	
		$this->name = Utils::cleanInput($name_);
		$this->sqlPDOSave = "INSERT INTO user(name, email, pwdHash, regDate) VALUES(':name:', ':email:', ':pwdhash:', ':regDate:')";
		
		$this->regDate = time();//Utils::cleanInput($regDate_);
		$this->email = Utils::cleanInput($email_);
		$this->name = Utils::cleanInput($name_);
		//$this->pwdHash = Utils::cleanInput($pwdHash_);
		//fill empty string as pwd for further validation error when saving
		if(empty($pwd)) {$this->pwdHash = "";} else
			{$this->pwdHash = Utils::getHash($pwd);}
		$this->id = null;
		self::$errormsg = "just created {$this->name} {$this->email}";
	}
	public function save(){

		if( ! $this->validateSave() ) {
			$this->errormsg = 'Validation error when saving: '.self::$errormsg;
			return false;
		}		
		$arrParameters = array(
			":name:"=>$this->name,
			":email:"=>$this->email,
			":pwdhash:"=>$this->pwdHash,
			":regDate:"=>$this->regDate);
		$pdosql = strtr($this->sqlPDOSave, $arrParameters);
		//$pdosql = str_replace(':1:', $this->name, $this->sqlPDOSave);
		//print_r($pdosql);
		$retval = $this->saveObject($pdosql);
		if(!$retval){
			$this->errormsg = 'Could not save user: '.$this->errormsg;
			return false;
		}else 
			return true;
		/*
	//-
		if( ! $this->validateSave() ) {
			self::$errormsg = 'Validation error when saving: '.self::$errormsg;
			return false;
		}
	//-
		$this->db = DataBase::connect();
	
		$SQLpart = "INSERT INTO users(regDate, email, pwdHash, name)";
		$PDOpart = " VALUES(:regDate, :email, :pwdHash, :name)";
		
		//Logger::log(serialize($fieldsWithoutPK));
		
		$sql = $SQLpart.$PDOpart;
		
		$statement = $this->db->connection->prepare($sql);
		if(!$statement){
			self::$errormsg = 'statement didn\'t prepare';
			return false;
		}
		
	$bindState = $statement->bindParam(":regDate", $this->regDate);
	$bindState = $bindState && $statement->bindParam(":email",$this->email, PDO::PARAM_STR);
	$bindState = $bindState && $statement->bindParam(":name",$this->name, PDO::PARAM_STR);
	$bindState = $bindState && $statement->bindParam(":pwdHash",$this->pwdHash, PDO::PARAM_STR);
		
		if(!$bindState){
			self::$errormsg = $statement->errorInfo();
			return false;
		}
		
		if ($statement->execute() ){
			$this->id = $this->db->connection->lastInsertId();
			self::$errormsg = 'saved';
			return true;
			}
		else{
			self::$errormsg = 'Save failed: '.implode(' ', $statement->errorInfo() );
			return false;
		}
		*/
	}
	/* get user from DB by id
	*/
	public static function load($id){
		$load = self::getFromDB($id);
		if(empty($load)){return false;}else{
		$me = new User($load['name'], $load['email'], 'dummyPWD' );
		$me->id = $load['id_user'];
		$me->pwdHash = $load['pwdHash'];
		$me->regDate = $load['regDate'];
		return $me;}
	}
	/*return user for given name or email
	*/
	public static function getUserbyNameOrEmail($name='', $email=''){
		$u = self::getUserDatabyNameOrEmail($name, $email);
		if(false === $u){return false;}else{
			return self::load($u['id_user']);
		}
		
	}
	
	/*return user id for given name or email
	*/
	public static function getUserDatabyNameOrEmail($name='', $email=''){
		
		if( (empty($name)) and ( empty($email)) ){
			return null;
		}
		$name = Utils::cleanInput($name);
		$email = Utils::cleanInput($email);
		
		if(empty($email)){
			$where = "WHERE name='{$name}'";	
		}elseif(empty($name)){
			$where = "WHERE email='{$email}'";			
		}else{
			$where = "WHERE name='{$name}' OR email='{$email}'";		
		}
//echo $where;
		$user = self::getAllWhere($where);
//var_dump($user[0]);die();
		if($user[0]['id_user'] > 0) {return $user[0];} else {return false;}
	
	}
	
	public function update(){
	}
	public function delete(){}
	public static function getByID($id){}
	
	public static function getBy($field, $value){
		if(!isset($field) or !isset($value)){
			return null;
		}
		$field = Utils::cleanInput($field);
		$value = Utils::cleanInput($value);
		
		$db = DataBase::connect();
	
		$sql = "SELECT id_user FROM users WHERE {$field}=?";
		
		$stmt = $db->connection->prepare($sql);
		$stmt->bindValue(1, $value);
		$stmt->execute();

		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		//Logger::log(serialize($fieldsWithoutPK));
//print_r($rows);
	}
	
	public static function get(){}
	
	// are there values for object or not?
	protected function isEmptyFields(){

		$this->errorEmptyFields = "";
	
	if(empty($this->regDate)) $this->errorEmptyFields .= " register date ";
	if(empty($this->email)) $this->errorEmptyFields .= " email ";
	if(empty($this->name)) $this->errorEmptyFields .= " name ";
	if(empty($this->pwdHash)) $this->errorEmptyFields .= " password ";

		if( 
			empty($this->regDate) or 
			empty($this->email) or
			empty($this->name) or
			empty($this->pwdHash) )
		{return true;} 
		else {return false;}
	}
	
	// interface ObjectEntity
	public function isThereSameObject(){
	//if($rows > 0) {return true;} else {return false;}
	if ( $this->isThereSameUser($this->name, $this->email) === true ) {return true;} else {return false;}
	}
	
	//returns false if can not save
	public function	validateSave(){
	
		if( $this->isEmptyFields() ) {
			self::$errormsg = 'Not all data is presented. Cannot create user. <br/> Empty data:'.$this->errorEmptyFields;
			return false;
		}
		
		if( $this->isThereSameObject() ) {
			self::$errormsg = 'User wth the same name or email exists now. Cannot create the same user.';
			return false;
		}
		
		if( ! Utils::isValidEmail($this->email ) ) {
			self::$errormsg = 'Provided email spelled incorrect. Re-type email or provide another one.';
			return false;
		}
	
		return true;
	}
	
	public function	validateUpdate(){}
	
	/*
	//checks if there's user with the same name OR email
	//if error occured - returns null
	//if exists - returns true, otherwise false
	*/
	public static function isThereSameUser($name='', $email=''){
		
		if( (empty($name)) and ( empty($email)) ){
			return null;
		}
		$name = Utils::cleanInput($name);
		$email = Utils::cleanInput($email);
		
		if(empty($email)){
			$where = "WHERE name='{$name}'";	
		}elseif(empty($name)){
			$where = "WHERE email='{$email}'";			
		}else{
			$where = "WHERE name='{$name}' AND email='{$email}'";		
		}
//LiLogger::log('user-auth-where:'.$where);
		$count = self::countWhere($where);
		
		if($count > 0) {return true;} else {return false;}
	
	}
}


?>