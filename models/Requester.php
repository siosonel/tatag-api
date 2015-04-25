<?php

include_once "models/Utils.php";
include_once "models/Router.php";
include_once "models/Base.php";
include_once "models/Collection.php";

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
	public static $consumer_id=0;
	public static $token_id=0;
	public static $otk;
	public static $login_provider;
	public static $db_default="tatagtest";
	
	static function init() {
		//@header("Content-Type: text/plain");
		@header("Content-Type: application/json");
		error_reporting(error_reporting() & ~E_NOTICE);
		
		self::define_SITE();
		global $dbs;
		include_once "config.php";		
		
		self::setAccess();		
		self::$defs = json_decode(file_get_contents("ref/defs.json")); //print_r(self::$defs); exit();		
		
		if (!isset($_SERVER['PHP_AUTH_USER'])) Error::http(401, "The header must include basic auth information.");
		 
		$user = $_SERVER['PHP_AUTH_USER'];
		self::$user_id = is_numeric($user) ? 1*$user : 0; 
		self::$email = self::$user_id ? "" : "$user";
		$pwd = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : "";  //exit('"'. self::$user_id ." ". $pwd ."---".  self::$email. '---"');
		
	
		if (self::$db_default=='tatagsim') {}
		else if (strpos($user, "consumer-")!==false) self::consumer_login($user, $pwd);
		else if (strpos($user, "token-")!==false) self::token_login($user, $pwd);
		else self::login($pwd);
	}
	
	static function setAccess() {
		$url = explode("/", trim($_GET['_url'], " \/\\\t\n\r\0\x0B"));
		$path = array_slice($url, 0, 3);
		$openAccess = array("collection", "about", "ts", "ranks", "flow", "inflow", "outflow", "added", "intrause");
		
		if (count($openAccess) > count(array_diff($openAccess, $path)) AND !isset($_SERVER['PHP_AUTH_USER'])) {
			$_SERVER['PHP_AUTH_USER'] = OPEN_ACCESS_USER;
			$_SERVER['PHP_AUTH_PW'] = OPEN_ACCESS_PW;
		} 
		
		global $dbs;
		self::$db_default = (isset($_GET['db']) AND $_GET['db']) ? $_GET['db'] : 'tatagtest';
		DBquery::init($dbs, array(self::$db_default));
	}
	
	static function login($pwd) {
		$sql = "SELECT user_id, name, password, email, login_provider FROM users WHERE (user_id=? OR email=?)";
		$row = DBquery::get($sql, array(self::$user_id, self::$email));
		if (!$row) Error::http(401, "Invalid user ID/email or password. User ID: ". self::$user_id .", Email: ". self::$email);
		
		$user = $row[0];		 
		if (!$user OR !password_verify($pwd, $user['password'])) Error::http(401, "Invalid user ID/email or password. User ID: ". self::$user_id .", Email: ". self::$email);

		self::$user_id=$user['user_id'];
		self::$name=$user['name'];
		self::$email=$user['email'];
		self::$login_provider = $row[0]['login_provider'];
	}
	
	static function consumer_login($user, $pwd) {
		list($label, self::$consumer_id) = explode("-", $user); 
		if (!self::$consumer_id) Error::http(400, "Missing or invalid consumer id.");
		
		$sql = "SELECT secret FROM consumers WHERE consumer_id=?";
		$row = DBquery::get($sql, array(self::$consumer_id));
		if (!$row) Error::http(401, "Invalid credentials for consumer ID='". self::$consumer_id ."'.");
		if (!password_verify($pwd, $row[0]['secret'])) Error::http(401, "Invalid credentials for consumer ID='". self::$consumer_id ."'.");
	}
	
	static function token_login($user, $pwd) {
		list($label, self::$token_id) = explode("-", $user); 
		if (!self::$token_id) Error::http(400, "Missing or invalid token id.");
		
		$sql = "SELECT tokens.user_id, users.name, tokens.login_provider 
			FROM tokens LEFT JOIN users ON tokens.user_id=users.user_id
			WHERE token_id=? AND ((token_val='0' AND otk=?) OR (token_val!=0 AND token_val=?))";
		$row = DBquery::get($sql, array(self::$token_id, $pwd, $pwd));
		if (!$row) Error::http(401, "Invalid credentials for token ID='". self::$token_id ."'. $sql $user $pwd");
		
		self::$user_id = $row[0]['user_id'];
		self::$name = $row[0]['name'];
		self::$otk = $pwd;
		self::$login_provider = $row[0]['login_provider'];
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
		$sql = "SELECT holder_id, holders.authcode AS authcode, brand_id, limkey FROM holders JOIN accounts USING (account_id) WHERE account_id IN (?) AND user_id IN (?)";
		$row = DBquery::get($sql, array($account_id, self::$user_id));
		if ($row) self::$holder_id=$row[0]['holder_id'];
		
		return $row[0];
	}
	
	static function isMember($brand_id) {
		$sql = "SELECT member_id FROM members WHERE brand_id IN (?) AND user_id IN (?) LIMIT 1";
		$row = DBquery::get($sql, array($brand_id, self::$user_id));
		if ($row) return $row[0]['member_id']; 
	}
	
	static function holderIDs() {
		$sql = "SELECT holder_id FROM holders WHERE user_id=?";
		$rows = DBquery::get($sql, array(self::$user_id));
		$holderIDs = array();
		foreach($rows AS $r) $holderIDs[] = $r['holder_id'];
		return $holderIDs; 
	}
	
	static function isRelayHolder($relay_id) {
		$sql = "SELECT user_id FROM relays JOIN holders USING (holder_id) WHERE relay_id=? LIMIT 1";
		$rows = DBquery::get($sql, array($relay_id));
		if (!$rows OR $rows[0]['user_id'] != self::$user_id) return false;
		return true;
	}
	
	static function define_SITE() {
		// detect from most secure to least, and if not detected default to most secure site
		$SN = $_SERVER['SERVER_NAME'];
		$PATH = $_SERVER['PHP_SELF'];
		$ADDR = $_SERVER['SERVER_ADDR'];
		
		if (substr($SN,-4)==".dev" OR $SN=='localhost') define("SITE", "dev");
		else if ($SN=='stage.tatag.cc') define('SITE','stage');
		else if ($SN=='tatag.cc') define('SITE', 'live');
		else define('SITE', 'live');
	}
}
