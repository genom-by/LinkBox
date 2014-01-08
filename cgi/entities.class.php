<?php
/*
entities
*/
namespace LinkBox;

include_once 'EntityModel.class.php';

class Test extends EntityModel{
	public $regdate;
	public $name;
	public $email;
	public $id;
	public $number;
	
	protected $sqlTable = 'test';
	protected $sqlPK = array('id_test'=>'id');
	protected $sqlFields = array('id_test'=>'id', 'email'=>'email', 'name'=>'name', 'num'=>'number');	//sql => class
	protected $sqlFieldsPDOTypes = array('id_test'=>\PDO::PARAM_INT, 'email'=>\PDO::PARAM_STR, 'name'=>\PDO::PARAM_STR, 'num'=>\PDO::PARAM_INT);
	
	protected $requiredFields = array(
	'name',
	'email',
	'num'	
	);
	public function __construct($regd, $nm, $em, $num){
	parent::__construct();
		$this->InitialiseValues(array('name'=>$nm, 'email'=>$em, 'num'=>$num));
	}
	//public function 
	/*
	*initialises internal values
	*/
	public function InitialiseValues($array){

	foreach($this->sqlFields as $sqlField => $classField){
		$this->$classField = Utils::cleanInput($array[$sqlField]);
		//Logger::log("$this->$classField". Utils::cleanInput($array[$sqlField]));
		}		
	}
	
	
	static function LoadByID($id){
		$blank = new Test(null,null,null,null);
		$id = Utils::cleanInput($id);
		$blank->id = $id;

		if ($blank->Count('PK', $id) <> 1 ) 
	//echo $blank->errormsg;
	return null;
		$me = $blank->Find('id', $id);
		//var_dump($me);
		if( $me <> -1 )
			$blank->InitialiseValues($me);
//var_dump($blank);		
		if($blank instanceof Test)
			return $blank;
			else return null;
	}
}

class User extends EntityModel{
	private $regdate;
	private $name;
	private $email;
	private $id;
	private $pass;
	
	protected $sqlTable = 'users';
	protected $sqlFields = array('id_user', 'email', 'name', 'regdate', 'pwd');
	protected $sqlPK = array('id_user');
	
	protected $requiredFields = array(
	'name',
	'email',
	'pass'	
	);
	//$missingFields array - uses with array_diff() and array_filter()
	private $emptyFields=1;
	
	public function __construct($regd, $nm, $em, $pass){
		empty($regd)?$this->emptyFields *= 0 : $this->regdate = Utils::cleanInput($regd);
		empty($nm)?$this->emptyFields *= 0 : $this->name = Utils::cleanInput($nm);
		empty($em)?$this->emptyFields *= 0 : $this->email = Utils::cleanInput($em);
		
		empty($pass)?$this->emptyFields *= 0 : $this->pass = md5(Utils::cleanInput($pass));
		
		if($this->emptyFields==0){
		throw new \Exception('Trying to create not full User');
		}
	}
	
	static function LoadByID($id){
		$this->__construct(null,null,null,null);
	}
	
	private function getRegTime(){
		if(is_null($this->regdate) or empty($this->regdate)){
			return ":no date:";
		}else{
			return date("Y m d", $this->regdate);
		}
	}

}
?>