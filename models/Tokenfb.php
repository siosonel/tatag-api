<?php
require_once "models/Token.php";

class Tokenfb extends Token {
	protected $fb_id;
	
	function setTokenUserID() {	
		$user = json_decode(file_get_contents("https://graph.facebook.com/me?id=$this->fb_id&access_token=$this->access_token"));
		if (!$user) Error::http(400, "No information was retrieved for access_token='$this->access_token'.");
		if ($user->error) Error::http(500, "Error retrieving information for access_token='$this->access_token': [$info->error->message].");
		
		$app = json_decode(file_get_contents("https://graph.facebook.com/app?access_token=$this->access_token"));
		if ($app->id != FB_CLIENT_ID) Error::http(401, "An invalid client_id was retrieved for access_token='$this->access_token'.");
		
		$this->okToSet = array("otk","user_id", "login_provider");		
		$this->user_id = $this->getByOauthID($user);
		$this->addKeyVal('user_id',$this->user_id);		
		$this->addKeyVal('otk', mt_rand(1, 99999999));
		$this->addKeyVal('login_provider', 'fb');
		
		
		$this->update(array(
			"token_id" => $this->token_id, 
			"otk"=> Requester::$otk, 
			"token_val"=>'0'
		));
		return array($this);
	}
	
	function getByOauthID($user) {
		$fb_id = "".$user->id;
	
		$sql = "SELECT user_id FROM users WHERE fb_id=?";
		$row = DBquery::get($sql, array($fb_id));
		if ($row) return $row[0]['user_id'];
		
		require_once "models/UserCollection.php";
		$Users = new UserCollection(json_decode('{
			"email": "'. $user->email .'",
			"name": "'. $user->first_name ." ". $user->last_name .'",
			"password": "'. mt_rand(5,99999999) .'",
			"fb_id": "'. $fb_id .'",
			"login_provider": "fb"
		}'));
		
		$arr = $Users->add();
		return $arr[0]->user_id;
	}
}

