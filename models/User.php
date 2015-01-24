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
		
		$this->email = Requester::$email;		
		$this->okToSet = array("ended","email","name","password");
		$this->okToFilterBy =  array("user_id","email");	
	}
	
	function add($data='') {
		Error::http(403);
	}
	
	function set() {		
		$this->update("WHERE user_id=?", array(Requester::$user_id));
		return $this->obj;
	}
	
	function get() {
		$this->_userMemberships = $this->{'@id'}."/brands";
		$this->_userAccounts = $this->{'@id'}."/accounts";
		$this->setForms();	
		
		include_once "models/userBrands.php";		
		include_once "models/userAccounts.php";
		$obj = json_decode('{"user_id":' . $this->user_id .'}');	
		
		return array_merge(
			array($this),
			(new UserBrands($obj))->get(),
			(new UserAccounts($obj))->get()
		);
	}
}

?>