<?php
require_once "models/Token.php";

class Tokengp extends Token {	
	function setTokenUserID() {
		$info = json_decode(file_get_contents("https://www.googleapis.com/oauth2/v1/tokeninfo?access_token=$this->access_token"));
		
		if (!$info) Error::http(400, "No information was retrieved for access_token='$this->access_token'.");
		if ($info->error) Error::http(500, "Error retrieving information for access_token='$this->access_token': [$info->error].");
		if ($info->audience != GAPI_CLIENT_ID) Error::http(401, "An invalid client_id was retrieved for access_token='$this->access_token'.");
		
		$this->okToSet = array("otk","user_id", "login_provider");		
		$this->user_id = $this->getByOauthID($info);
		$this->addKeyVal('user_id',$this->user_id);		
		$this->addKeyVal('otk', mt_rand(1, 99999999));
		$this->addKeyVal('login_provider', 'gp');
		
		$this->update(array(
			"token_id" => $this->token_id, 
			"otk"=> Requester::$otk, 
			"token_val"=>'0'
		));
		return array($this);
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
			"gp_id": "'. $gp_id .'",
			"login_provider": "gp"
		}'));
		
		$arr = $Users->add();
		return $arr[0]->user_id;
	}
}