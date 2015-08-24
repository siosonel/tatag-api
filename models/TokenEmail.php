<?php
require_once "models/Token.php";

class TokenEmail extends Token {	
	function setTokenUserID() {
		if (!method_exists($this, $this->action)) Error::http(400, "Invalid action value='$this->action', must be one of [login, register, recover].");
		
		//need to verify recaptcha code		
		
		$row = $this->getByEmailAddr();
		return $this->{$this->action}($row);
	}
	
	function getByEmailAddr() {
		if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) Error::http(400, "Invalid email value='$this->email'. $this->pwd"); 
	
		$sql = "SELECT user_id, password, question, answer, login_provider FROM users WHERE email=?";
		return DBquery::get($sql, array($this->email))[0];
	}
	
	function login($row) {
		if (!$row) Error::http(400, "The user email='$this->email' was not found during login. Either the email was typed incorrectly or the user must register first.");
		
		if (!password_verify($this->pwd, $row['password'])) 
			Error::http(401, "Invalid user email or password. Email: ". $this->email);
		
		$this->okToSet = array("otk","user_id", "login_provider");	
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
	
	function httpRequest($domain,$method,$data) {	
		$context=stream_context_create(array("http" => array(
			"method" => $method,
			"content" => json_encode($data),
			"timeout"=>3, 
			"max_redirects"=>2
		)));
		
		$contents = file_get_contents($domain,0,$context);
		$data = json_decode($contents);
		return ($data ? $data->{'@graph'} : new stdClass());
	}
	
	function recover() {
		if (!$row) Error::http(400, "The user email='$this->email' was not found during pasword recovery. Either the email was typed incorrectly or the user must register first.");
		
		if (strtolower($this->answer) != $row['answer']) Error::http(403, "The submitted answer did not match the one on record.");
		
		/*** TO-DO: set up recovery email, link handling ***/
	}
		
	function getQuestion($row) {
		if (!$row) Error::http(400, "The user email='$this->email' was not found while searching for the security question. Either the email was typed incorrectly or the user must register first.");
		
		return array("question"=>$row['question'], "login_provider"=>$row['login_provider']);
	}
		
	function register() {
		if ($row) Error::http(403, "There is already a user with this email='$this->email'. Either recover the password for it or register using a different email.");
	
		/*** TO-DO: verify email ownership ***/
	
		require_once "models/UserCollection.php";
		$Users = new UserCollection(json_decode('{
			"email": "'. $this->email .'",
			"password": "'. $this->pwd  .'",
			"login_provider": "email",
			"question": "'. $this->question .'",
			"answer": "'. $this->answer .'"
		}'));
		
		$user = $Users->add()[0];
		unset($user->question);
		unset($user->answer);
		return array($user);
	}
}