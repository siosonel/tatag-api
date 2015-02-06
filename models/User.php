<?php
/*
Self access to user's information
*/

class User extends Base {	
	function __construct($data='') {
		$this->{"@type"} = 'user';
		$this->user_id = $this->getID();	
		if (!Requester::isUser($this->user_id)) Error::http(401, "The requester must be logged in as the requested user.");
		
		$this->{"@id"} = "/user/$this->user_id";		
		$this->table = 'users';
		$this->cols = 'user_id,email,name,password,created,ended';
		$this->idkey = 'user_id';
		$this->init($data);
		
		$this->name = Requester::$name;
		$this->email = Requester::$email;		
		$this->okToSet = array("ended","email","name","password");
		$this->okToFilterBy =  array("user_id","email");	
	}
	
	function add($data='') {
		Error::http(403);
	}
	
	function set() {	
		$this->update(array("user_id" => $this->user_id));
		return array($this);
	}
	
	function get() {		
		$this->links = new stdClass();
		$this->links->userMemberships = $this->{'@id'}."/brands";
		$this->links->userAccounts = $this->{'@id'}."/accounts";
		$this->setForms();	
		
		include_once "models/userBrands.php";		
		include_once "models/userAccounts.php";
		$obj = json_decode('{"user_id":' . $this->user_id .'}');	
		
		$memberships = (new UserBrands($obj))->get(); 
		foreach($memberships AS $m) {
			foreach($m->items AS $b) {
				$this->links->budgetIssued[] = "/budget/".$b['brand_id']."/issued";
				$this->links->budgetTransferred[] = "/budget/".$b['brand_id']."/transferred";
				$this->links->budgetUsed[] = "/budget/".$b['brand_id']."/used";
				if ($b['role']=='admin') $this->links->brand[] = '/brand/'. $b['brand_id'];
			}
		}
		
		return array_merge(
			array($this)
			, $memberships
			, (new UserAccounts($obj))->get()
		);
	}
}

?>