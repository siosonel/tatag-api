<?php

include_once "utils/Utils.php";
include_once "utils/Router.php";
include_once "models/Base.php";
include_once "models/Collection.php";

class Requester {
	public static $user_id=0;
	private $pass='';
	private static $memberships=0;
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
	public static $ProtDomain;
	
	static function init() {
		//@header("Content-Type: text/plain");
		@header("Content-Type: application/json");
		error_reporting(error_reporting() & ~E_NOTICE);
		
		self::define_SITE();
		if (SITE=="live" OR SITE=='stage') error_reporting(0);
		
		$audience = self::detect_AUDIENCE();
		global $dbs;
		include_once "config-$audience.php";		
		
		self::setUser();		
		$protocol = (isset($_SERVER['HTTPS']) AND $_SERVER['HTTPS']) ? 'https' : 'http';
		self::$ProtDomain = $protocol ."://". $_SERVER['SERVER_NAME'];
	}
	
	static function inSession() { 
		//danger of using the same session for different consumers
		return false;
		
		session_start(); //$_SESSION['user_id'] = 21; 
		if (!$_SESSION['user_id']) return false;
		self::$user_id = $_SESSION['user_id'];
		self::$name = $_SESSION['user_name'];
		self::$email = $_SESSION['user_email'];
		return true;
	}
	
	static function setUser() {
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
		$openAccess = array(
			"", 
			"collection", "about", "ts", "ranks", "tally", 
			"flow", "inflow", "outflow", "added", "intrause",
			"sim", "arRatio", "promo", "report", "trial"
		);

		if (SITE=='dev') {
			$openAccess[] = "cron";
			$openAccess[] = "sys";
		}
		
		if (!isset($_SERVER['PHP_AUTH_USER'])
			AND (AUDIENCE=='sim' 
			OR $_SERVER['PHP_SELF']=='/api/login.php' 
			OR count($openAccess) > count(array_diff($openAccess, $path)))
		) {
			$_SERVER['PHP_AUTH_USER'] = OPEN_ACCESS_USER;
			$_SERVER['PHP_AUTH_PW'] = OPEN_ACCESS_PW;
		}
		
		self::setDBs();
	}

	static function setDBs() {
		global $dbs;
		self::$db_default = 'tatagtest';
		
		//override as needed
		if (isset($_GET['db']) AND $_GET['db'] AND SITE!='live') {
			self::$db_default = $_GET['db'];
			unset($_GET['db']);
		}
		
		DBquery::init($dbs, array(self::$db_default)); 
	}
	
	static function login($pwd) {
		if (self::$user_id==0) {
			if (strtolower($_SERVER['REQUEST_METHOD'])!='get') Error::http(403,"The user must be logged in order to submit this request.");
			self::$name='Guest';
		}
		else {	
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
		
		$sql = "SELECT tokens.user_id, users.name, tokens.login_provider, UNIX_TIMESTAMP(tokens.updated) as updated
			FROM tokens LEFT JOIN users ON tokens.user_id=users.user_id
			WHERE token_id=? AND ((token_val='0' AND otk=?) OR (token_val!=0 AND token_val=?))";
		$rows = DBquery::get($sql, array(self::$token_id, $pwd, $pwd));
		
		if (!$rows) Error::http(401, "Invalid credentials for token ID='". self::$token_id ."'.");
		
		$updated = $rows[0]['updated'];
		//if ($updated AND time() - $updated > 86400) Error::http(401, "The login-enabled token#". self::$token_id ." for this user has expired (maximum 24-hours API session reached.).");
		
		self::$user_id = $rows[0]['user_id'];
		self::$name = $rows[0]['name'];
		self::$otk = $pwd;
		self::$login_provider = $rows[0]['login_provider'];
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
		$rows = DBquery::get($sql, array($account_id, self::$user_id));
		if (!$rows) return 0; //	401, "User #". self::$user_id ." is not an account admin for account #$account_id."

		
		self::$member_id=$row[0]['member_id'];
		return 1;
	}
	
	static function isAccountHolder($account_id) { 	
		$sql = "SELECT holder_id, holders.authcode AS authcode, brand_id, limkey FROM holders JOIN accounts USING (account_id) WHERE account_id IN (?) AND user_id IN (?)";
		$rows = DBquery::get($sql, array($account_id, self::$user_id));
		if ($rows) self::$holder_id=$rows[0]['holder_id'];
		
		return $rows[0];
	}
	
	static function isMember($brand_id) {
		if (is_array(self::$memberships)) return self::$memberships[$brand_id];
		else {
			$sql = "SELECT member_id FROM members WHERE brand_id IN (?) AND user_id IN (?) LIMIT 1";
			$rows = DBquery::get($sql, array($brand_id, self::$user_id));
			if ($rows) return $rows[0]['member_id'];
		}
	}
	
	static function detectMemberships() {
		$sql = "SELECT brand_id, member_id FROM members WHERE user_id IN (?)";
		$rows = DBquery::get($sql, array(self::$user_id));
		if ($rows) {
			self::$memberships = array(); 
			foreach($rows AS $r) self::$memberships[$r['brand_id']] = $r['member_id']; 
		}
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
		else if ($SN=='stage.tatag.cc' OR $SN=='sim-stage.tatag.cc') define('SITE','stage');
		else if ($SN=='tatag.cc') define('SITE', 'live');
		else define('SITE', 'live');
	}
	
	static function detect_AUDIENCE() {
		// detect from most secure to least, and if not detected default to most secure site
		$SN = $_SERVER['SERVER_NAME'];
		
		if (substr($SN,0,4)=="sim." OR substr($SN,0,4)=="sim-") return "sim";
		else return "public";
	}
}
