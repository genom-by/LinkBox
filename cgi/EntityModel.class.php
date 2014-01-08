<?php
namespace LinkBox;
use PDO;
use PDOException;

include_once 'utils.inc.php';
include_once 'settings.inc.php';
include_once 'database.class.php';

/*---------------------------------------------------------------------------
*abstract class for entities
*/
abstract class EntityModel{

	static protected $dataBase;
	public $errormsg = '';
	
	protected $sqlTable;
	protected $sqlFields; //maps sql fields to entity field
	protected $requiredFields;
	protected $sqlPK; //primary key(s)

	function __construct()
	{
		$this->InitializeDataBase();
	}
	static function InitializeDataBase(){
		self::$dataBase = DataBase::getDb();
		if(is_null(self::$dataBase)){
			$this->errormsg = 'Failed to connect to DataBase in '.__CLASS__;
			throw new Exception($this->errormsg);
		}
	
	}
	
	abstract static function LoadByID($id);
/*---------------------------------------------------------------------------
*Gets DataBase status
*
*/
	function GetDataBaseStatus(){
		return self::$dataBase->status;
}
/*---------------------------------------------------------------------------
*Testing function shows complete SQL query
*
*/	
	function PreviewSQL()
	{
		//$sql = $this->doSQLpart().$this->doPDOpart();
		//return self::$dataBase->PreviewSQL();
		$sql = $this->DoSQLqueryINSERT();
		return $sql;
	}
/*---------------------------------------------------------------------------
*Save entity with proper SQL query into DB
*
*/	
	function SaveSQL()
	{
		
	}
/*-----------------------------------------------------------------------------
*Find and return entity from DB
*
*@return mixed Entity otherwise -1
*/	
	public function Find($fieldName, $value)
	{
		if(! $this->DBconnected() ){
			return -1;
		}
		$sql = $this->DoSQLquerySELECTbyID();
//echo $sql;		
		$statement = self::$dataBase->connection->prepare($sql);
		if(!$statement){
			$this->errormsg = 'statement didn\'t prepare';
			return -1;
		}
		reset($this->sqlPK);
		list($PKsql, $val) = each($this->sqlPK);
		$res = $statement->bindValue(":$PKsql", $value, $this->sqlFieldsPDOTypes[$PKsql]);
		if(!$res){
			$err = $statement->errorInfo();
			return -1;
			}
		
		if(!$statement->execute() ){
			$this->errormsg = implode(' ', $statement->errorInfo() );
			return -1;
		}
		
		$me = $statement->fetchAll();
		if($me === false ){
			$this->errormsg = implode(' ', $statement->errorInfo() );
			return -1;
		}else{
			return $me[0];
		}
	}
/*-----------------------------------------------------------------------------
*Binds values to prepared statement
*
*@return resource prepared Statement otherwise -1
*/	
	private function BindValues($sql, $operation = 'INSERT')
	{
		if(! $this->DBconnected() ){
			return -1;
		}
		
		//echo $sql;
		//var_dump($this);
		$statement = self::$dataBase->connection->prepare($sql);
		if(!$statement){
			$this->errormsg = 'statement didn\'t prepare';
			return -1;
		}
		
		$bindedFields = array();
		
		switch ($operation){
			case 'INSERT':
			$bindedFields = array_diff_assoc($this->sqlFields, $this->sqlPK);break;
			default:
			$bindedFields = $this->sqlFields;
		}
		//var_dump($statement);
		foreach($bindedFields as $field => $value){
		//echo "':$field', {$this->$value}";
			$res = $statement->bindValue(":$field", $this->$value, $this->sqlFieldsPDOTypes[$field]);
			if(!$res) $err = $statement->errorInfo();
			//Logger::log("Binding':$field' - {$this->$value} - {$this->sqlFieldsPDOTypes[$field]}".($res?" :: Error":' :: ok').$err[2]);
			
		}/*
		$res = $statement->bindValue("':num'", $this->number, PDO::PARAM_INT);
		$res = $statement->bindValue(':email', $this->email);
		$res = $statement->bindValue(':name', $this->name);*/
		return $statement;
	}
/*---------------------------------------------------------------------------
*Update current entity into proper table in DB
*
*/	
	function Update()
	{
		if($this->PKexists() != 1){
			$this->errormsg = 'Current entity doesn\'t exists in table';
			return -1;
		}
		$sql = $this->DoSQLqueryUPDATE();
		$preparedStatement = $this->BindValues($sql);
		if($preparedStatement === -1){
			$this->errormsg = 'Values didn\'t bind: '.$this->errormsg;
			return -1;		
		}
		if ($preparedStatement->execute() ){
			//$this->id = PDO::lastInsertId();
			return true;
			}
		else{
			$this->errormsg = 'Update failed: '.implode(' ', $preparedStatement->errorInfo() );
			return -1;
		}		
	}
/*-----------------------------------------------------------------------------
*Save entity into proper table in DB
*
*/	
	public function Save()
	{
		throw new Exception('Save is obsolete');
	}
/*-----------------------------------------------------------------------------
*Save entity into proper table in DB
*
*/	
	public function SaveXXX()
	{
		if(! $this->DBconnected() ){
			return -1;
		}
		
		$res = $this->PKexists();
		if($res == 1){echo 'here';
			return $this->Update();
		}
		if($res == -1){
			$this->errormsg = 'Error while counting';
		return -1;
		}
		
		$sqlSave = $this->DoSQLqueryINSERT();
		$preparedStatement = $this->BindValues($sqlSave);
		if($preparedStatement === -1){
			$this->errormsg = 'Values didn\'t bind: '.$this->errormsg;
			return -1;		
		}
		//return $preparedStatement;
		//var_dump($preparedStatement);
		if ($preparedStatement->execute() ){
			$this->id = self::$dataBase->connection->lastInsertId();
			
			return true;
			}
		else{
			$this->errormsg = 'Save failed: '.implode(' ', $preparedStatement->errorInfo() );
			return -1;
		}
	}
/*---------------------------------------------------------------------------
*Creates sql query as 'SELECT (:field1, :field2) FROM tablename
*/	
	function DoSQLquerySELECTbyID()
	{	
		reset($this->sqlPK);
		list($PKsql, $val) = each($this->sqlPK);
//echo "<br/>PKsql={$PKsql}<br/>";		
//var_dump(each($this->sqlPK));
		$part1 = "SELECT ";
		foreach ($this->sqlFields as $field => $entity){
			$part1 .= $field;
			$part1 .= ', ';
		}
		$part1 = rtrim($part1,', ');
		
		$part2 = " FROM {$this->sqlTable} WHERE {$PKsql} = :{$PKsql}";
		
		return $part1.$part2;		
	}
/*---------------------------------------------------------------------------
*Creates sql query as 'UPDATE tablename(field1, field2...) VALUES(:field1, :field2)'
*/	
	function DoSQLqueryUPDATE()
	{
		reset($this->sqlPK);
		list($PKsql, $val) = each($this->sqlPK);
		
		$part1 = "UPDATE {$this->sqlTable} SET ";
		$part2 = " WHERE {$PKsql} = :{$PKsql}";
		$fieldsWithoutPK = array_diff_assoc($this->sqlFields, $this->sqlPK);
		
		foreach($fieldsWithoutPK as $field => $entity){
			$part1 .= $field;
			$part1 .= '=:{$field},';
		}
		$part1 = rtrim($part1,', ');
				
		return $part1.$part2;		
	}
/*---------------------------------------------------------------------------
*Creates sql query as 'INSERT INTO tablename(field1, field2...) VALUES(:field1, :field2)'
*/	
	function DoSQLqueryINSERT()
	{
		$SQLpart = "INSERT INTO {$this->sqlTable}(";
		$PDOpart = " VALUES(";
		$fieldsWithoutPK = array_diff_assoc($this->sqlFields, $this->sqlPK);
		//Logger::log(serialize($fieldsWithoutPK));
		foreach($fieldsWithoutPK as $field => $entity){
			$SQLpart .= $field;
			$SQLpart .= ', ';
			$PDOpart .= ':'.$field;
			$PDOpart .= ', ';			
		}
		$SQLpart = rtrim($SQLpart,', ');
		$SQLpart .= ') ';
		$PDOpart = rtrim($PDOpart,', ');
		$PDOpart .= ') ';
		
		return $SQLpart.$PDOpart;
	}
/*---------------------------------------------------------------------------
*Creates sql part as 'tablename(field1, field2...)'
*/	
	function doSQLpart()
	{
		return -1;
	}
/*---------------------------------------------------------------------------
*Creates sql part as 'values(:field1, :field2...)'
*/	
	function doPDOpart()
	{
		return -1;
	}
/*---------------------------------------------------------------------------
*Check if connection to DB persists
*@return on error returns -1 otherwise true
*/
	public function DBconnected()
	{
		if(! self::$dataBase->connection) 	{
			$this->errormsg = 'not connected to DB';
			return false;
		}else
			return true;
	}
	
/*---------------------------------------------------------------------------
*Check if this PK exists yet
*@return on error returns -1 otherwise true
*/
	public function PKexists()
	{
		if(! $this->DBconnected() ){
			return -1;
		}
		reset($this->sqlPK);
		list($PKsql, $val) = each($this->sqlPK);
		$cnt1 = $this->Count($PKsql, $this->$val);
		//echo "cnt==$cnt";
		//Logger::log("-count1:$cnt1-");
		return $cnt1;
	}
/*---------------------------------------------------------------------------
* Gets COUNT of entities
* @of_what string by what search
* @id mixed PK if presented
* @return on error returns -1
*/
	public function Count($of_what = 'all', $id = null)
	{
		
		if(! $this->DBconnected() ){
			return -1;
		}

		switch($of_what){
			case 'all':
				$sql = "SELECT COUNT(*) FROM {$this->sqlTable}";
				break;
			case 'PK':
				reset($this->sqlPK);
				list($PKsql, $val) = each($this->sqlPK);
				$sql = "SELECT COUNT(*) FROM {$this->sqlTable} WHERE {$PKsql} = ". self::$dataBase->connection->quote($id);
				break;
			default:
				$sql = '';
		}
	//echo $sql;	
		//$cnt = ($this->connection)::query($sql, PDO::FETCH_COLUMN, 0);

		//$stmt = self::$dataBase->connection->query($sql, PDO::FETCH_COLUMN, 0);
		$stmt = self::$dataBase->connection->prepare($sql);

		//var_dump($stmt);
		if($stmt === false){
			$this->errormsg = "Stmt not prepared".implode(' ', self::$dataBase->connection->errorInfo() );
			//echo 'try to perform query:'.self::$dataBase->connection->queryString;
			return -2;
		}	
		$stmt->execute();		
		$cnt = $stmt->fetchColumn(0);
		if($cnt === false){
			$this->errormsg = implode(' ', self::$dataBase->connection->errorInfo() );
			return -1;
		}
		Logger::log("-count:$cnt-");
		//return $cnt;
	}
	
	public function __toString()
    {
		$ustr = "Table: $this->sqlTable, fields:";
		foreach($this->sqlFields as $field => $value){
			$ustr .= " :$field: ";
			$ustr .= (is_null($this->$value) or empty($this->$value))?"null":$this->$value;
			
		}
        return $ustr;
    }
	
} // EntityModel

?>