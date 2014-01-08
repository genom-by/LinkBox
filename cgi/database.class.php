<?php
<<<<<<< HEAD
namespace LinkBox;
use PDO;
use PDOException;
=======
>>>>>>> 6b0274c5dfa49ba30bca5e71fad81824d6d837a6

include_once 'settings.inc.php';

class DataBase{
	protected static $instance;
<<<<<<< HEAD
	public $connection;
	public $errormsg = '';
	public $status = 'disconnected';
=======
	protected $connection;
	public $errormsg = '';
>>>>>>> 6b0274c5dfa49ba30bca5e71fad81824d6d837a6
	private function __construct()
	{
		if (is_null($this->connection)){
			try{
				if(defined(ISSQLITE))
				{
					if( ! file_exists(DBPATHSQLITE) ){
<<<<<<< HEAD
						//$this->connection = new PDO(DSNSQLITE.DBPATHSQLITE);
						//$this->createSQLITEtable($this->connection);
						throw new \Exception('Database not found');
=======
						$this->connection = new PDO(DSNSQLITE.DBPATHSQLITE);
						$this->createSQLITEtable($this->connection);
>>>>>>> 6b0274c5dfa49ba30bca5e71fad81824d6d837a6
					}else{
						$this->connection = new PDO(DSNSQLITE.DBPATHSQLITE);				
					}
				}else{
					$this->connection = new PDO(DSNMYSQL, DBUSER, DBPWD);
				}
<<<<<<< HEAD
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
=======
				$this->connection->exec('SET NAMES utf8'); 
			}catch(PDOException $pe){
				$this->errormsg = 'Not connected to DB. Error: '.$pe->getMessage();
			}
		}
	}
	private function createSQLITEtable($conn)
	{
	    $res = $this->connection->exec("CREATE TABLE links (
					msg_id INTEGER PRIMARY KEY,
					time TIMESTAMP,
					IP char(16),
					link varchar(255),
					name varchar(255),
					isfolder boolean
					)"
					);
		if($res === false){
			echo $this->errormsg = implode(' ', $this->connection->errorInfo() );
			return false;		
		}
>>>>>>> 6b0274c5dfa49ba30bca5e71fad81824d6d837a6
	}
	
	public static function getDB()
	{
		if(is_null(self::$instance) ){
			self::$instance = new DataBase();
		}
		return self::$instance;
	}
	
<<<<<<< HEAD
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
	
=======
	public function getLinksCount()
	{
		if(!$this->connection) 	{
			return false;
		}
		
		$sql = "SELECT COUNT(*) FROM links";
		//$cnt = ($this->connection)::query($sql, PDO::FETCH_COLUMN, 0);
		$stmt = $this->connection->query($sql, PDO::FETCH_COLUMN, 0);
		if(!$stmt ){
			$this->errormsg = implode(' ', $this->connection->errorInfo() );
			return false;
		}		
		$cnt = $stmt->fetchColumn(0);
		if($cnt === false){
			$this->errormsg = implode(' ', $this->connection->errorInfo() );
			return false;
		}
		return $cnt;
	}
>>>>>>> 6b0274c5dfa49ba30bca5e71fad81824d6d837a6
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
	
<<<<<<< HEAD
	
=======
	public function saveLink($msg, $isfolder = false)
	{
	if(! $isfolder){
		if( ! defined(ISSQLITE))
		{
			$sql = "INSERT INTO links(time, IP, link, name, isfolder)
			VALUES( now(), :IP, :link, :name, 0)";
		//VALUES(FROM_UNIXTIME(:time), :IP, :agent, :user, :email, :message, :homepage)";
		}else{
			$sql = "INSERT INTO links(time, IP, link, name, isfolder)
			VALUES( strftime('%s', 'now'), :IP, :link, :name, 0)";		
		}
	}else{
		if( ! defined(ISSQLITE))
		{
			$sql = "INSERT INTO links(time, IP, link, name, isfolder)
			VALUES( now(), :IP, :link, :name, 1)";
		//VALUES(FROM_UNIXTIME(:time), :IP, :agent, :user, :email, :message, :homepage)";
		}else{
			$sql = "INSERT INTO links(time, IP, link, name, isfolder)
			VALUES( strftime('%s', 'now'), :IP, :link, :name, 1)";		
		}	
	}	
		if(!$this->connection) 
		{
			//echo $this->errormsg;
			$this->errormsg = 'not connected';
			return false;
		}
		
		$statement = $this->connection->prepare($sql);
		if(!$statement){
			$this->errormsg = 'not statement';
			return false;
		}
		$time = time();
		//$statement->bindValue(':time', null, PDO::PARAM_NULL);
		//$statement->bindValue(':time', $time, PDO::PARAM_INT);
		$statement->bindValue(':IP', $msg->fields['IP'], PDO::PARAM_STR);
		$statement->bindValue(':name', $msg->fields['name'], PDO::PARAM_STR);
	if(! $isfolder){		
		$statement->bindValue(':link', $msg->fields['link'], PDO::PARAM_STR);		
	}else{
		$statement->bindValue(':link', 'fld/'.$msg->fields['name'], PDO::PARAM_STR);				
	}
		//$statement->bindValue(':filepath', $msg->fields['filepath'], PDO::PARAM_STR);

		if ($statement->execute() )
			return true;
		else{
			$this->errormsg = implode(' ', $statement->errorInfo() );
			return false;
		}
	}
>>>>>>> 6b0274c5dfa49ba30bca5e71fad81824d6d837a6
}
?>