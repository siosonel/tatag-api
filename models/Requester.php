<?php

include_once "models/Utils.php";
include_once "models/Base.php";

class Requester {
	public static $user_id=0;
	private $pass='';
	public static $email='';
	public static $name='';
	public static $member_id=0;
	public static $holder_id;
	public static $defs;
	public static $forms;
	
	public static $graph=array();
	public static $graphRefs=array();
	
	
	static function init() {
		//@header("Content-Type: text/plain");
		@header("Content-Type: application/json");
		error_reporting(error_reporting() & ~E_NOTICE);
		
		global $dbs;
		include_once "config.php";
		DBquery::init($dbs, array("tatagtest"));
		
		self::$defs = json_decode(file_get_contents("ref/defs.json")); //print_r(self::$defs); exit();
		
		if (!isset($_SERVER['PHP_AUTH_USER'])) Error::http(401, "The header must include basic auth information.");
		 
		$user = $_SERVER['PHP_AUTH_USER'];
		self::$user_id = is_numeric($user) ? 1*$user : 0; 
		self::$email = self::$user_id ? "" : "$user";
		$pwd = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : "";  //exit('"'. self::$user_id ." ". $pwd ."---".  self::$email. '---"');
		
		self::login($pwd);
	}
	
	static function login($pwd) {
		$sql = "SELECT user_id, name, password, email FROM users WHERE (user_id=? OR email=?)";
		$row = DBquery::get($sql, array(self::$user_id, self::$email));
		if (!$row) Error::http(401, "Invalid user ID/email or password. User ID: ". self::$user_id .", Email: ". self::$email);
		
		$user = $row[0];		 
		if (!$user OR !password_verify($pwd, $user['password'])) Error::http(401, "Invalid user ID/email or password. User ID: ". self::$user_id .", Email: ". self::$email);

		self::$user_id=$user['user_id'];
		self::$name=$user['name'];
		self::$email=$user['email'];
	}
	
	static function isUser($user_id) {
		return (self::$user_id == $user_id);
	}
	
	static function isBrandAdmin($brand_id) {
		if (!$brand_id) Error::http(400, 'Invalid brand id (null).'); 
	
		$sql = "SELECT member_id FROM members WHERE brand_id IN (?) AND user_id IN (?) AND role='admin'";
		$row = DBquery::get($sql, array($brand_id, self::$user_id)); 
		
		if (!count($row)) {
			$sql = "SELECT member_id FROM members WHERE brand_id IN (?) LIMIT 1";
			$row = DBquery::get($sql, array($brand_id));
			if (!count($row)) return 1; //the first member of a brand must be assigned the role of admin
		}
		else {
			self::$member_id=$row[0]['member_id'];
			return 1;
		}
	}
	
	static function isAccountAdmin($account_id) {	
		$sql = "SELECT member_id FROM members JOIN accounts USING (brand_id) WHERE account_id=? AND user_id IN (?) AND role='admin'";
		$row = DBquery::get($sql, array($account_id, self::$user_id));
		if (!$row) return 0; //	401, "User #". self::$user_id ." is not an account admin for account #$account_id."

		
		self::$member_id=$row[0]['member_id'];
		return 1;
	}
	
	static function isAccountHolder($account_id) { 	
		$sql = "SELECT holder_id, authcode FROM holders WHERE account_id IN (?) AND user_id IN (?)";
		$row = DBquery::get($sql, array($account_id, self::$user_id));
		if ($row) self::$holder_id=$row[0]['holder_id'];
		
		return $row[0];
	}
	
	static function isMember($brand_id) {
		$sql = "SELECT member_id FROM members WHERE brand_id IN (?) AND user_id IN (?) LIMIT 1";
		$row = DBquery::get($sql, array($brand_id, self::$user_id));
		if ($row) return 1; 
	}
}
