<?php
require_once "models/Token.php";

class TokenEmail extends Token {
	private $verLifetime = 600;

	function setTokenUserID() {
		if (!method_exists($this, $this->action)) Error::http(400, "Invalid action value='$this->action', must be one of [login, register, recover].");
		
		//need to verify recaptcha code		
		if (SITE!='dev' AND ($this->action=='recoverCode' OR $this->action=='registerCode')) {
			require('../common2/lib/recaptcha/autoload.php');
			$Recaptcha = new \ReCaptcha\ReCaptcha(G_RECAPTCHA);
			$response = $Recaptcha->verify($this->access_token);
		
		
			if (!$response->isSuccess()) Error::http(403, 
				"The submitted data either failed the recaptcha challenge or has expired. ". json_encode($response->getErrorCodes())
			);
		}
		
		$row = $this->getByEmailAddr();
		$this->user_id = $row['user_id'];		
		return $this->{$this->action}($row);
	}
	
	function getByEmailAddr() {
		if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) Error::http(400, "Invalid email value='$this->email'. $this->pwd"); 
	
		$sql = "SELECT user_id, password, login_provider, ver_code, ver_expires FROM users WHERE email=?";
		return DBquery::get($sql, array($this->email))[0];
	}
	
	function login($row) {
		if (!$row) Error::http(400, "The user email='$this->email' was not found during login. Either the email was typed incorrectly or the user must register first.");
		
		if (!password_verify($this->pwd, $row['password'])) 
			Error::http(401, "Invalid user email or password. Email: ". $this->email);
		
		$this->okToSet = array("otk","user_id", "login_provider");	
		$this->addKeyVal('user_id',$this->user_id);		
		$this->addKeyVal('otk', mt_rand(1, 99999999));
		$this->addKeyVal('login_provider', 'email');
		
		$this->update(array(
			"token_id" => $this->token_id, 
			"otk"=> Requester::$otk, 
			"token_val"=>'0'
		));
		
		return array($this);
	}
	
	function sendRecoveryCode($row) {		
		if (!$row) Error::http(400, "Cannot send verification code: The user email='$this->email' was not found during pasword recovery. Either the email was typed incorrectly or the user must register first.");
		
		$this->setAndMailVerCode();
		
		$sql = "UPDATE users SET ver_code='$this->verCode', ver_expires=$this->verExpires WHERE user_id=?";
		DBquery::set($sql, array($row['user_id']));
			
		return array($this);
	}
	
	function sendRegistrationCode($row) { 
		if ($this->verType=='register' AND $row AND $row['password']) Error::http(403, "There is already a user with the email='$this->email'. If you typed your email correctly, recover your password instead of registering.");
		
		$this->setAndMailVerCode();
		
		require_once "models/UserCollection.php";
		$Users = new UserCollection(json_decode('{
			"email": "'. $this->email .'",
			"password": "'. $this->pwd  .'",
			"login_provider": "email",
			"ver_code": "'. $this->verCode .'",
			"ver_expires": "'. $this->verExpires .'"
		}'));
		
		$user = $Users->add()[0];
		return array($user);
	}
	
	function setAndMailVerCode() {		
		$this->verCode = substr(sha1(microtime(). $this->email . $this->verType . mt_rand(0,10000)), 0,6);
		$this->verExpires = time() + $this->verLifetime; 
		
		if (SITE=='dev') return;
		
		$to      = $this->email;
		$subject = 'tatag.cc verification code';
		$message = 'Verification Code: '. $this->verCode;
		$headers = 'From: do-not-reply@tatag.cc' . "\r\n" .
				'Reply-To: do-not-reply@tatag.cc' . "\r\n" .
				'Return-Path: do-not-reply@tatag.cc' . "\r\n" .
				'X-Mailer: PHP/' . phpversion();

		if (!mail($to, $subject, $message, $headers)) Error::http(500, "Unable to deliver mail to '$this->email'.");
	}
		
	function setPassword($row) {
		if (!$row) Error::http(40, "There is no user found to verify against the submitted code, email='$this->email'.");
		
		if ($time > $row['ver_expires']) Error::http(403, "The verification code has expired. Please use the form again to request a new verificaion code to be send to your email.");
		
		if ($this->ver_code != $row['ver_code']) Error::http(403, 'The submitted verification code does not match the one on record.');
		
		$pwd = password_hash($this->pwd, PASSWORD_BCRYPT);
		
		$sql = "UPDATE users SET password=? WHERE user_id=?";
		DBquery::set($sql, array($pwd, $row['user_id']));		
		
		
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
}