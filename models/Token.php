<?php

class Token extends Base {
	protected $access_token;

	function __construct($data='') {
		$this->{"@type"} = 'token';
		$this->token_id = $this->getID();			
		if ($this->token_id) $this->{"@id"} = "/token/$this->token_id" ;		
		
		$this->table = 'tokens';
		$this->idkey = 'token_id';
		
		if ($data->access_token) {
			$this->access_token = $data->access_token;
			unset($data->access_token);
		}
		
		$this->init($data);
		
		$this->okToAdd = array("consumer_id", "otk");
		
		$this->okToFilterBy =  array("token_id","otk","token_val");	
	}
	
	function add($data='') {
		$this->addKeyVal("otk", mt_rand(1, 1000000));
		$this->addKeyVal('consumer_id', Requester::$consumer_id);
		
		$this->token_id = $this->insert();
		return array($this);
	}
	
	function set() {
		if ($this->access_token) return $this->setTokenUserID();		
		else {
			$this->okToSet = array("token_val");		
			$this->addKeyVal('token_val', mt_rand(1, 99999999));
			$this->update(array("token_id" => $this->token_id, "otk"=> $_GET['otk'])); //, "token_val"=>'0'));
			return array($this);
		}
	}
	
	function setTokenUserID() {
		$info = json_decode(file_get_contents("https://www.googleapis.com/oauth2/v1/tokeninfo?access_token=$this->access_token"));
		
		if (!$info) Error::http(400, "No information was retrieved for access_token='$this->access_token'.");
		if ($info->error) Error::http(500, "Error retrieving infomration for access_token='$this->access_token': [$info->error].");
		if ($info->audience != GAPI_CLIENT_ID) Error::http(401, "An invalid client_id was retrieved for access_token='$this->access_token'.");
		
		$this->okToSet = array("otk","user_id");		
		$this->user_id = $this->getByOauthID($info);
		$this->addKeyVal('user_id',$this->user_id);
		
		$this->addKeyVal('otk', mt_rand(1, 99999999));
		$this->update(array("token_id" => $this->token_id, "otk"=> Requester::$otk, "token_val"=>'0'));
		return array($this);
	}
	
	function setTokenVal() {
	
	}
	
	function get() {		
		$sql = "SELECT token_val FROM tokens WHERE token_id=? AND otk=?";
		$row = DBquery::get($sql, array($this->token_id, Requester::$otk));
	}
	
	
	function getByOauthID($info) {
		$gp_id = "".$info->user_id;
	
		$sql = "SELECT user_id FROM users WHERE gp_id=?";
		$row = DBquery::get($sql, array($gp_id));
		if ($row) return $row[0]['user_id'];
		
		require_once "models/UserCollection.php";
		$Users = new UserCollection(json_decode('{
			"email": "'. $info->email .'",
			"name": "'. $info->name .'",
			"password": "'. mt_rand(5,99999999) .'",
			"gp_id": "'. $gp_id .'"
		}'));
		
		$arr = $Users->add();
		return $arr[0]->user_id;
	}
}