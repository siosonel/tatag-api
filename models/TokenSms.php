<?php
require_once "models/Token.php";

class TokenSms extends Token {	
	function getByPhoneNum() {		
		if (!$phone = $_REQUEST['From']) Error::sms("The SMS sender's phone number was not detected.");
		if (!$body = $_REQUEST['Body']) Error::sms("Missing email address.");
		
		$sql = "SELECT user_id, email, wallet FROM users WHERE phone=?";
		$row = DBquery::get($sql, array($phone));
		
		if ($row) {
			if ($body != $row[0]['email']) {
				Error::sms("The email address does not match the one set for phone #$phone.");
			} 
			else {
				$this->user_id = $row[0]['user_id'];
				Requester::$consumer_id = $row[0]['wallet'];
				return $this->setToken();
			}
		}
		else {
			require_once "models/UserCollection.php";
			$Users = new UserCollection(json_decode('{
				"email": "'. $body .'",
				"name": "'. $body .'",
				"password": "'. mt_rand(5,99999999) .'",
				"phone": "'. $phone .'",
				"login_provider": "phone",
				"wallet": 2
			}'));
			
			$arr = $Users->add();
			$this->user_id = $arr[0]->user_id;
			return $this->setToken();
		}
	}
	
	function setToken() {
		$this->okToAdd = array("consumer_id", "otk", 'login_provider', 'user_id'); 
		
		$this->otk = mt_rand(1, 1000000);	
		$this->addKeyVal('user_id',$this->user_id);
		$this->addKeyVal("otk", $this->otk);
		$this->addKeyVal('consumer_id', Requester::$consumer_id); 
		$this->addKeyVal('login_provider', "sm");
		
		$this->token_id = $this->insert();
		return array($this);
	}
}