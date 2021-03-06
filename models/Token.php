<?php

class Token extends Base {
	protected $access_token;
	protected $id_type;

	function __construct($data='') {
		$this->{"@type"} = 'token';
		$this->token_id = $this->getID();			
		if ($this->token_id) $this->{"@id"} = "$this->root/token/$this->token_id" ;		
		
		$this->table = 'tokens';
		$this->idkey = 'token_id';
		
		$this->removeFromInput = array("access_token", "id_type", "fb_id", "email", "pwd", "action", "ver_code");
		$this->init($data);
		
		$this->okToSet = array("user_id", "otk", "token_val", "login_provider");
		$this->okToFilterBy =  array("token_id","otk","token_val");	
	}
	
	function add($data='') {
		$this->okToAdd = array("consumer_id", "otk", 'login_provider'); 
		$this->addKeyVal("otk", mt_rand(1, 1000000));
		$this->addKeyVal('consumer_id', Requester::$consumer_id);
		$this->addKeyVal('login_provider', 0, "ifMissing");
		
		$this->token_id = $this->insert();
		return array($this);
	}
	
	function set() {
		if ($this->access_token OR $this->ver_code) return $this->setTokenUserID();		
		else {
			$this->okToSet = array("token_val");		
			$this->addKeyVal('token_val', mt_rand(1, 99999999));
			$this->update(array("token_id" => $this->token_id, "otk"=> $_GET['otk'])); //, "token_val"=>'0'));
			return array($this);
		}
	}
	
	function get() {		
		$sql = "SELECT token_val FROM tokens WHERE token_id=? AND otk=?";
		$row = DBquery::get($sql, array($this->token_id, Requester::$otk));
	}
}