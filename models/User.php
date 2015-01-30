<?php
/*
Self access to user's information
*/

class User extends Base {	
	function __construct($data='') {
		$this->{"@type"} = 'user';
		$this->user_id =  $this->getID();	
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
		$this->links->adminOf = $this->getAdminLinks();
		$this->setForms();	
		
		include_once "models/userBrands.php";		
		include_once "models/userAccounts.php";
		$obj = json_decode('{"user_id":' . $this->user_id .'}');	
		
		return array_merge(
			array($this)
			, (new UserBrands($obj))->get()
			, (new UserAccounts($obj))->get()
		);
	}
	
	function getAdminLinks() {
		$sql = "SELECT CONCAT('/brand/',brand_id) AS link FROM members WHERE user_id IN (?)";
		$rows = DBquery::get($sql, array($this->user_id));
		$vals = array();		
		foreach($rows AS $r) $vals[] = $r['link'];	
		return $vals;
	}
}

?>