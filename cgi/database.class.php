<?php

include_once 'settings.inc.php';

class DataBase{
	protected static $instance;
	protected $connection;
	public $errormsg = '';
	private function __construct()
	{
		if (is_null($this->connection)){
			try{
				if(defined(ISSQLITE))
				{
					if( ! file_exists(DBPATHSQLITE) ){
						$this->connection = new PDO(DSNSQLITE.DBPATHSQLITE);
						$this->createSQLITEtable($this->connection);
					}else{
						$this->connection = new PDO(DSNSQLITE.DBPATHSQLITE);				
					}
				}else{
					$this->connection = new PDO(DSNMYSQL, DBUSER, DBPWD);
				}
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
	}
	
	public static function getDB()
	{
		if(is_null(self::$instance) ){
			self::$instance = new DataBase();
		}
		return self::$instance;
	}
	
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
}
?>