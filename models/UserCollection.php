<?php
/*
Public access to users collection
*/

class UserCollection extends Base {	
	function __construct($data='') {
		$this->{"@id"} = "/user/collection";
		$this->{'@type'} = "userCollection";
		$this->table = 'users';
		$this->idkey = 'user_id';
		$this->init($data);
		
		$this->okToAdd = array('email', 'name', 'password', "fb_id", "gp_id");
	}
	
	function add($data='') {
		if (!$this->email AND !$this->fb_id AND !$this->gp_id) Error::http(400, "When registerng a user, an email, facebook id (fb_id) and/or google+ id (gp_id) must be used as input.");
		foreach($this->okToAdd AS $key) $this->addKeyVal($key,0,"ifMissing");
	
		$this->obj->password = password_hash($this->obj->password, PASSWORD_DEFAULT);
		$this->valArr[ array_search('password', $this->keyArr) ] = $this->obj->password; 
		
		$User = $this->obj;
		$User->user_id = $this->insert();
		unset($User->password); //no need to communicate this back for privacy
		return array($User);
	}
	
	function get() {
		$this->setForms();
	
		$sql = "SELECT COUNT(*) AS numUsers, MIN(created) AS earliest, MAX(created) AS latest FROM users";		
		$row = DBquery::get($sql);		
		if (!$row) return array($this);				
		foreach($row[0] AS $key=>$val) $this->$key = $val;
		
		return array($this);
	}
	
}

