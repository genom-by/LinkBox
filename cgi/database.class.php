<?php
namespace LinkBox;
use PDO;
use PDOException;

include_once 'settings.inc.php';

class DataBase{
	protected static $instance;
	public $connection;
	public $errormsg = '';
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
					}
				}else{
					$this->connection = new PDO(DSNMYSQL, DBUSER, DBPWD);
				}
				$this->connection->exec('SET NAMES utf8');
				$this->status = 'connected';
				if(defined('DEBUG_MODE')){
					//$this->connection->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
					$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
				}
			}catch(PDOException $pe){
				$this->errormsg = 'Not connected to DB. Error: '.$pe->getMessage();
			}
		}		
	}
	
	public static function getDB()
	{
		if(is_null(self::$instance) ){
			self::$instance = new DataBase();
		}
		return self::$instance;
	}
	
/*---------------------------------------------------------------------------------
* perform Update operation
* @table string what table to update
* @arrayFV array key=>value pairs where key is fieldname in database
* @condFVO array : fieldname=>(value=>operation)
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
	
	
}
?>