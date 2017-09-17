<?php
namespace LinkBox;

//==========================================
// DB access class
// ver 2.0
// © genom_by
// last updated 28 oct 2015
//==========================================

use PDO;
use PDOException;

include_once 'utils.inc.php';
include_once 'settings.inc.php';

class DataBase{
	protected static $instance;
	public $connection;
	public static $errormsg = '';
	public $status = 'disconnected';
	private function __construct()
	{
		if (is_null($this->connection)){
			try{
				if(defined(ISSQLITE))
				{
					if( ! file_exists(DBPATHSQLITE) ){
						//$this->connection = new PDO(DSNSQLITE.DBPATHSQLITE);
						//$this->createSQLITEtable($this->connection);
						throw new \Exception('Database not found');
					}else{
						$this->connection = new PDO(DSNSQLITE.DBPATHSQLITE);
						$this->connection->exec('PRAGMA foreign_keys = ON;');						
					}
				}else{
					$this->connection = new PDO(DSNMYSQL, DBUSER, DBPWD);
				}
				$this->connection->exec('SET NAMES utf8');
				$this->connection->exec("SET CHARACTER SET 'utf8'");
				$this->status = 'connected';
				if(defined('DEBUG_MODE')){
					$this->connection->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
					//$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
				}
			}catch(PDOException $pe){
				self::$errormsg = 'Not connected to DB. Error: '.$pe->getMessage();
			}
		}		
	}
	public function __destruct()
	{
		$this->disconnect();
	}	
// dummy fn	
	public function close(){
	}	
// disconnect fn	
	public function disconnect(){
		if( ! is_null(self::$instance) ){
		//if( ! is_null($this->instance) ){
		//var_dump(self::$instance); //DataBase object
		//var_dump($this->instance); //NULL
		//self::$instance->__destruct();
		self::$instance = null;
		$this->status = 'disconnected';		
		}
		//$this->status = 'disconnected';		
		//}
	}
	public static function connect()
	{
		if(is_null(self::$instance) ){
			self::$instance = new DataBase();
			Logger::Log('created and connected');
		}
		return self::$instance;
	}
	public static function getPDO(){
		if(! is_null(self::$instance) ){	
			return self::$instance->connection;
		}
		else return false;
	}
	public static function checkConnect(){
		
		if(! self::connect() ){
			self::$errormsg = 'Not connected to db.';
			Logger::log(self::$errormsg);
			return false;
		} else return true;	
	}
	/* ### REFACTORED ###
	execute prepared query
	*/
	public static function executeQuery($query, $action='default'){
		if(empty($query)){		self::$errormsg = 'DB[executeQuery]: No query provided.';
			Logger::log(self::$errormsg);		
			return false;
		}
		if($action == 'default'){	self::$errormsg = 'DB[executeQuery]: No action provided.';
			Logger::log(self::$errormsg);		
			return false;
		}	
		if(! self::checkConnect() ){ self::$errormsg = 'DB[executeQuery]: Not connected to DB.';
			Logger::log(self::$errormsg);		
			return false;
		}
		try{
			$res = self::getPDO()->exec($query);
		}catch(\Exception $e){
			self::$errormsg = 'DB[executeQuery]: Error while executing query: '.$e->getMessage();
			Logger::log(self::$errormsg);
			Logger::log('DB[executeQuery] desired query:'.$query);
			return false;
		}
		switch ($action){
			case 'insert':
				if($res != 1){
					self::$errormsg = 'DB[executeQuery]: Error while inserting into DB.'.implode(',',self::getPDO()->errorInfo());					Logger::log(self::$errormsg);
					return false;
				}
			break;
			case 'delete':
				if($res === false){
					self::$errormsg = 'DB[executeQuery]: Error while deleting from DB.'.implode(',',self::getPDO()->errorInfo());					Logger::log(self::$errormsg);
					return false;
				}			
			break;
			case 'update':
				if($res < 1){
					self::$errormsg = 'DB[executeQuery]: No one record was updated.'.implode(',',self::getPDO()->errorInfo());					Logger::log(self::$errormsg);
					return false;
				}			
			break;
			case 'pragma':
				if($res === false){
					self::$errormsg = 'DB[executeQuery]: Pragma command failed.'.implode(',',self::getPDO()->errorInfo());					Logger::log(self::$errormsg);
					return false;
				}			
			break;
			default:
			return false;
		}
	return true;
		
	} ## refactored end	
	### REFACTORED ###
	public static function executeInsert($query){
		return self::executeQuery($query, 'insert');
	}
	### REFACTORED ###	
	public static function executeDelete($query){
		/*$res=self::executeQuery('PRAGMA foreign_keys = ON;', 'pragma');
		if($res===false){
		self::$errormsg = 'DB[executeDelete]: Pragma failed.'.implode(',',self::getPDO()->errorInfo());			Logger::log(self::$errormsg);
		}*/
		return self::executeQuery($query, 'delete');
	}
	### NEW ###	
	public static function executeUpdate($query){
		return self::executeQuery($query, 'update');
		//Logger::log($query);
		//return false;
	}

/* refactored 
@sql query to fetch
%array of objects or %false +$errormsg
*/	
public static function getArrayBySQL($sql, $ref=-1){
//Logger::log("get all for table: {$table} and id {$byID}");	
	if(empty($sql)){ self::$errormsg = 'DB[getArrayBySQL]:No query provided.';
		return false;
	}
	if(! self::checkConnect() ){ self::$errormsg = 'DB[getArrayBySQL]:No connection.';
		return false;
	}
	try{
		$statement = self::getPDO()->prepare($sql);
		if(!$statement){
			self::$errormsg = "DB[getArrayBySQL]".implode(' ', self::getPDO()->errorInfo() );
			return false;
		}
		if(false === $statement->execute() ){
			self::$errormsg = "DB[getArrayBySQL]".implode(' ', $statement->errorInfo() );
			return false;
		}
		
		$statement->setFetchMode(PDO::FETCH_ASSOC);
		$entries = $statement->fetchAll();
		
		if($entries === false ){		self::$errormsg = "DB[getArrayBySQL] No entries returned: ".implode(' ', $statement->errorInfo() );
			return false;
		}else{
			return $entries;
		}		
	}catch(\Exception $e){
		self::$errormsg = "DB[getArrayBySQL] Error getting records: ".$e->getMessage();
		Logger::log("DB[getArrayBySQL] exception executing query::".$sql);
		Logger::log(self::$errormsg);
		return false;		
	}
} //getArrayBySQL

/*---------------------------------------------------------------------------------
* perform Update operation
* @table string what table to update
* @arrayFV array key=>value pairs where key is fieldname in database
* @condFVO array : fieldname=>(value=>operation)
* @return mixed count of affected rows or false on error
*/
	public function Update($table, $arrayFV, $condFVO)
	{
		$SQLpart = "UPDATE {$table} SET ";
		$CONDpart = " WHERE ";
		
		foreach($arrayFV as $field => $value){
			$SQLpart .= "$field=:{$field}, ";
		}
		$SQLpart = rtrim($SQLpart,', ');
		
		foreach($condFVO as $field => $valueOp){
			list($value, $operation) = each($valueOp);
			$CONDpart .= "$field $operation :{$field} AND ";
		}
		//$CONDpart = rtrim($CONDpart,' AND ');
		$CONDpart = substr($CONDpart, 0, -5);
		
		$sql = $SQLpart.$CONDpart;
echo 'sql:'.$sql;		
		$statement = $this->connection->prepare($sql);
		if(!$statement){
			$this->errormsg = 'statement didn\'t prepare';
			return false;
		}
		
		reset($arrayFV);
		foreach($arrayFV as $field => $value){
			$res = $statement->bindValue(":$field", $value);//, $this->sqlFieldsPDOTypes[$field]);
			if(!$res){
				$this->errormsg = $statement->errorInfo();
				return false;
			}
		}
		reset($condFVO);
		foreach($condFVO as $field => $valueOp){
			list($value, $operation) = each($valueOp);
			$res = $statement->bindValue(":$field", $value);//, $this->sqlFieldsPDOTypes[$field]);
			if(!$res){
				$this->errormsg = $statement->errorInfo();
				return false;
			}
		}
		
		if ($statement->execute() ){
			$affCount = $statement->rowCount();
			
			return $affCount;
			}
		else{
			$this->errormsg = 'Save failed: '.implode(' ', $statement->errorInfo() );
			return false;
		}
	}
/*---------------------------------------------------------------------------------
* perform Insert operation
* @table string to what save
* @arrayFV array key=>value pairs where key is fieldname in database
* @return mixed last inserted id or false on error
*/
	public function Insert($table, $arrayFV)
	{
		$SQLpart = "INSERT INTO {$table}(";
		$PDOpart = " VALUES(";
		
		//Logger::log(serialize($fieldsWithoutPK));
		foreach($arrayFV as $field => $value){
			$SQLpart .= $field;
			$SQLpart .= ', ';
			$PDOpart .= ':'.$field;
			$PDOpart .= ', ';			
		}
		$SQLpart = rtrim($SQLpart,', ');
		$SQLpart .= ') ';
		$PDOpart = rtrim($PDOpart,', ');
		$PDOpart .= ') ';
		
		$sql = $SQLpart.$PDOpart;
		
		$statement = $this->connection->prepare($sql);
		if(!$statement){
			$this->errormsg = 'statement didn\'t prepare';
			return false;
		}
		reset($arrayFV);
		foreach($arrayFV as $field => $value){
			$res = $statement->bindValue(":$field", $value);//, $this->sqlFieldsPDOTypes[$field]);
			if(!$res){
				$this->errormsg = $statement->errorInfo();
				return false;
			}
		}
		
		if ($statement->execute() ){
			return $this->connection->lastInsertId();
			}
		else{
			$this->errormsg = 'Save failed: '.implode(' ', $statement->errorInfo() );
			return false;
		}
	}
	
	/*
	//
	*/
	public function getLinks($orderby = '', $currentpage=1)
	{
		$sqlorder = " ORDER BY ";
		switch($orderby){
		case 'user': $sqlorder .= 'user'; break;
		case 'email': $sqlorder .= 'email'; break;
		case 'date': 
		default:
			$sqlorder .= 'time DESC';
		}
		/*if($currentpage == 0)
			$sql = "SELECT msg_id, UNIX_TIMESTAMP(time) AS time, IP, agent, user, email, message, homepage FROM messages" . $sqlorder;
			else{
			$sql = "SELECT msg_id, UNIX_TIMESTAMP(time) AS time, IP, agent, user, email, message, homepage FROM messages" . $sqlorder . " LIMIT ".(($currentpage - 1) * PAGING_NUM_PER_PAGE) .", ". PAGING_NUM_PER_PAGE;
			}
		*/
		if( ! defined(ISSQLITE))
		{
			$sql = "SELECT msg_id, UNIX_TIMESTAMP(time) AS time, IP, link, name, isfolder FROM links" . $sqlorder . " LIMIT ".(($currentpage - 1) * PAGING_NUM_PER_PAGE) .", ". PAGING_NUM_PER_PAGE;
		}else{
		//strftime('%s', 'now', 'localtime')
				$sql = "SELECT msg_id, time, IP, link, name, isfolder FROM links" . $sqlorder . " LIMIT ".(($currentpage - 1) * PAGING_NUM_PER_PAGE) .", ". PAGING_NUM_PER_PAGE;
				//$sql = "SELECT msg_id, strftime('%s', time, 'localtime') AS time, IP, agent, user, email, message, homepage FROM messages" . $sqlorder . " LIMIT ".(($currentpage - 1) * PAGING_NUM_PER_PAGE) .", ". PAGING_NUM_PER_PAGE;	
		}	
		//echo $sql;
		if(!$this->connection) 	{
			return false;
		}
		$statement = $this->connection->prepare($sql);
		if(!$statement){
			$this->errormsg = implode(' ', $this->connection->errorInfo() );
			return false;
		}
		if(!$statement->execute() ){
			$this->errormsg = implode(' ', $statement->errorInfo() );
			return false;
		}
		
		$messages = $statement->fetchAll();
		if($messages === false ){
			$this->errormsg = implode(' ', $statement->errorInfo() );
			return false;
		}else{
			return $messages;
		}		
	}
	/*
	CREATE TABLE [seq_stations] (
 [id_ss] INTEGER NOT NULL PRIMARY KEY CONSTRAINT [XPKss] UNIQUE, 
 [id_seq] INTEGER NOT NULL CONSTRAINT [XPKss_seq] REFERENCES [sequences]([id_seq]) ON DELETE CASCADE,  
 [id_station] INTEGER NOT NULL CONSTRAINT [XPKss_stat] REFERENCES [station] ON DELETE RESTRICT, 
 [id_pitstoptype] INTEGER NOT NULL CONSTRAINT [XPKss_pitype] REFERENCES [pitstop_type] ON DELETE SET NULL, 
 [orderal] INTEGER NOT NULL,
 CONSTRAINT "XPKss_ord" UNIQUE("id_ss","orderal")
 );
	*/
	
}
?>