<?php


class Requester {
	public static $user_id=0;
	private $pass='';
	public static $email='';
	public static $name='';
	public static $member_id=0;
	public static $holder_id;
	
	static function init() {
		//session_set_cookie_params(0, '/nplite/');
		if (session_status() == PHP_SESSION_NONE) session_start(); //print_r($_SESSION); //exit();
		
		if (isset($_GET['user_id']) AND $_GET['user_id']) {unset($_SESSION['nplite_user_id']); echo 0; exit();}
		else if ($_SERVER['REQUEST_METHOD']=="POST") {
			if ($_GET['s']=='users' AND (!$_POST['user_id'] OR $_POST['name'])) return;
			if ((!isset($_POST['user']) OR !isset($_POST['pass'])) AND !isset($_SESSION['nplite_user_id'])) Error::http(401, "Missing credentials."); 
		}
	
		if (isset($_SESSION['nplite_user_id'])) {
			self::$user_id = $_SESSION['nplite_user_id'];
			self::$name = $_SESSION['nplite_user_name'];
		}
		else if (isset($_POST['user'])) {
			self::$user_id = $_POST['user']; 
			self::$email = $_POST['user'];
			$pass = $_POST['pass'];
			self::login($id,$pass);
		} else exit("hhh");
	}
	
	static function login($id,$pass) {	//self::$user_id=2; return;
		$sql = "SELECT user_id, name, password FROM users WHERE (user_id='$id' OR email='". self::$email ."')"; //exit($sql);
		$row = DBquery::select($sql);
		
		if (!count($row)) Error::http(401, "Invalid user ID/email or password.");
		else {
			$user = $row[0];		
			$pass = getHash($pass, $user['password']);
			if ($pass != $user['password']) Error::http(401, "Invalid user ID/email or password.");
		
			self::$user_id=$user['user_id'];
			self::$name=$user['name'];
			$_SESSION['nplite_user_id'] = self::$user_id; //exit(" ok ".self::$user_id);
			$_SESSION['nplite_user_name'] = self::$name;
		}
	}
	
	static function isUser($user_id) {
		return (self::$user_id == $user_id);
	}
	
	static function isBrandAdmin($brand_id) { 	
		$sql = "SELECT member_id FROM members WHERE brand_id IN ($brand_id) AND user_id IN (". self::$user_id .") AND role='admin'";
		$row = DBquery::select($sql);
		
		if (!count($row)) {
			$sql = "SELECT member_id FROM members WHERE brand_id IN ($brand_id) LIMIT 1";
			$row = DBquery::select($sql); 
			if (!count($row)) return 1; //the first member of a brand must be assigned the role of admin
		}
		else {
			self::$member_id=$row[0]['member_id'];
			return 1;
		}
	}
	
	static function isAccountAdmin($account_id) { 	
		$sql = "SELECT member_id FROM members JOIN accounts USING (brand_id) WHERE account_id=$account_id AND user_id IN (". self::$user_id .") AND role='admin'";
		$row = DBquery::select($sql);
		
		if (!count($row)) Error::http(401, "User #". self::$user_id ." is not an account admin for account #$account_id.");
		else {
			self::$member_id=$row[0]['member_id'];
			return 1;
		}
	}
	
	static function isAccountHolder($account_id) { 	
		$sql = "SELECT holder_id, authcode FROM holders WHERE account_id IN ($account_id) AND user_id IN (". self::$user_id .")"; echo "\n$sql";
		$row = DBquery::select($sql);
		
		if (!count($row)) Error::halt(401, "User #". self::$user_id ." is not an account holder for account #$account_id.");
		else {
			self::$holder_id=$row[0]['holder_id'];
			return $row[0];
		}
	}
	
	static function isMember($brand_id) {
		$sql = "SELECT member_id FROM members WHERE brand_id IN ($brand_id) AND user_id IN (". self::$user_id .") LIMIT 1";
		$row = DBquery::select($sql); 
		if ($row) return 1; 
	}
}

?>