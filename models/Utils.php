<?php

class Router {
	public static $table;
	public static $id;
	public static $method; 
	public static $Resource;
	
	public static function run() {		
		$method = strtolower($_SERVER['REQUEST_METHOD']);
		self::$method = $method;
		
		$_url = trim($_GET['_url'], " \/\\\t\n\r\0\x0B");
		list(self::$table, self::$id) = explode("/", $_url);

		$ObjClass = ucfirst(self::$table); //echo "--". self::$method ." ". self::$table ." ". self::$id ."--". $ObjClass;
		if (!self::$table OR !file_exists("models/$ObjClass.php")) Error::http(404, "The resource='".self::$table."' does not exist");

		require_once "models/$ObjClass.php";
		self::$Resource = new $ObjClass;
		
		if (!method_exists(self::$Resource,$method)) Error::http(405, "The method='$method' is not supported by resource='". self::$table ."'.");		
		
		$data = $method=='get' ? json_decode('{"id":'.self::$id.'}') : json_decode(file_get_contents("php://input"));			
		exit(json_encode(self::$Resource->$method($data), JSON_NUMERIC_CHECK));
	}
}

class Error {
	public static $test_mode;
	public static $codePath;
	public static $debug=0;
	
	//
	static function log() {
	
	}
	
	//
	static function message($message,$method="") {    
		//if (!$method) 
			return $message;
		//else 
			//return $method ."(". $message .")";    
	}
	
	//
	static function halt($message,$method="") {     
		@header("Content-Type: text/plain");
		if (self::$codePath) {
			include self::$codePath;
			$codeNum = $map[$method];
			$status = $codes[$codeNum];
			exit(json_encode(array("code"=>$codeNum,"status"=>$status,"message"=>$message)));
		}
		else if (!self::$debug) exit(json_encode(array("status"=>"Error","message"=>$message)));
		else exit(json_encode(array("status"=>"Error","message"=>$method ."(". $message .")")));   
	}  
	
	
	static function http($numCode, $mssg="", $debugMssg="") {
		if (function_exists("http_response_code")) http_response_code($numCode);
		else header("HTTP/1.1 $numCode");
		
		if (self::$debug) $mssg .= " [". $debugMssg ."]";			
		exit(json_encode($mssg));
	}
}


class DBquery {
	public static $alert;
	public static $test_mode;
	public static $dbs = array();
	public static $conn = null;
	public static $params = array();
	public static $statement;
	public static $json_numeric = 1;
	
	static function init(&$dbs, $filter=array()) {
		self::$dbs = &$dbs;
		self::connect(self::$dbs, $filter);		
		self::select_db($filter[0]);			
	}
	
	static function connect($CONN_ARR, $filter, $user='', $pwd='', $switch_to_alias='') { 
		foreach($CONN_ARR AS $alias => $prop) { 
			if (!$filter OR in_array($alias, $filter)) {
				$TYPE = isset($prop['TYPE']) ? $prop['TYPE'] : 'mysql';
				$SERVER_NAME = $prop['SERVER_NAME'];
				$DB_NAME = $prop['DB_NAME'];
				$USER = !$user ? $prop['USER'] : $user; //optional override of username
				$PWD = !$pwd ? $prop['PWD'] : $pwd; //optional override of pwd

				if (!$SERVER_NAME OR !$DB_NAME OR !$USER OR !$PWD) Error::http(500, "Missing DBconnect parameter(s).", "DBquery->connect");
				
				$OPTS = $TYPE == 'mysql' ? 	array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'", PDO::MYSQL_ATTR_USE_BUFFERED_QUERY=> 1) : array();			
				
				$dsn = "$TYPE:host=$SERVER_NAME;dbname=$DB_NAME;charset=utf8";
				self::$dbs[ $alias ]['conn'] = new PDO($dsn, $USER, $PWD, $OPTS);
				//self::$dbs[ $alias ]['conn']->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
			}
		}
		
		if ($switch_to_alias) self::select_db($switch_to_alias); //allow shortcut to setting default connection
	}

	static function close() {
		foreach(self::$dbs AS $alias=>$prop) self::$dbs[$alias]['conn'] = null;
	}
	
	static function select_db($alias) { 
		if (!isset(self::$dbs[$alias])) Error::halt("Missing connection alias.","DBquery->select_db");
		self::$conn = self::$dbs[$alias]['conn'];
	}
	
	static function get($statement, $vars=array(), $noResultStatus=0, $noResultMssg="") { 
		if (gettype($statement=='string')) $statement = self::$conn->prepare($statement);
		
		try { 
			$result = $statement->execute($vars); 
			
			if (!$result) {
				$info = $statement->errorInfo(); 
				Error::http(500, $info[2]);
			}
			
			if (!$statement->rowCount()) {
				if ($noResultStatus) Error::http($noResultStatus, $noResultMssg);
				else if ($noResultMssg) Error::halt($noResultMssg);
			}
		} 
		catch(PDOException $e) {
			Error::http(500, $e->getMessage()); 
		}
		
		self::$statement = $statement;	
		$results = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
		return $results;
	}
	
	static function post($statement, $vars=array(), $noResultStatus=0, $noResultMssg="") {
		if (gettype($statement=='string')) $statement = self::$conn->prepare($statement);
		
		try { 
			$result = $statement->execute($vars);
			
			if (!$result) {
				$info = $statement->errorInfo(); 
				Error::http(500, $info[2]);
			}
			
			if (!$statement->rowCount()) {
				if ($noResultStatus) Error::http($noResultStatus, $noResultMssg);
				else if ($noResultMssg) Error::halt($noResultMssg);
			}
			else {
				if (stripos($statement->$queryString,"INSERT")!==false) return $statement->lastInsertId();
			}
		} 
		catch(PDOException $e) { 
			Error::halt($e->getMessage()); 
		} 
	}		
}

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
		}
	}
	
	static function login($id,$pass) { //self::$user_id=2; return;
		$sql = "SELECT user_id, name, password FROM users WHERE (user_id=? OR email=?)"; //exit($sql);
		$row = DBquery::get($sql, array(self::$user_id, self::$email));		
		if (!$row) Error::http(401, "Invalid user ID/email or password.");
		
		$user = $row[0];		
		if (!password_verify($pass, $user['password'])) Error::http(401, "Invalid user ID/email or password.");
	
		self::$user_id=$user['user_id'];
		self::$name=$user['name'];
		$_SESSION['nplite_user_id'] = self::$user_id; //exit(" ok ".self::$user_id);
		$_SESSION['nplite_user_name'] = self::$name;
	}
	
	static function isUser($user_id) {
		return (self::$user_id == $user_id);
	}
	
	static function isBrandAdmin($brand_id) { 	
		$sql = "SELECT member_id FROM members WHERE brand_id IN (:brand_id) AND user_id IN (:user_id) AND role='admin'";
		$row = DBquery::get($sql, array('brand_id'=>$brand_id, 'user_id'=>self::$user_id));
		
		if (!count($row)) {
			$sql = "SELECT member_id FROM members WHERE brand_id IN ($brand_id) LIMIT 1";
			$row = DBquery::get($sql, array('brand_id'=>$brand_id)); 
			if (!count($row)) return 1; //the first member of a brand must be assigned the role of admin
		}
		else {
			self::$member_id=$row[0]['member_id'];
			return 1;
		}
	}
	
	static function isAccountAdmin($account_id) { 	
		$sql = "SELECT member_id FROM members JOIN accounts USING (brand_id) WHERE account_id=:account_id AND user_id IN (:user_id) AND role='admin'";
		$row = DBquery::get($sql, array('account_id'=>$account_id, 'user_id'=>self::$user_id));
		if (!$row) return 0; //	401, "User #". self::$user_id ." is not an account admin for account #$account_id."

		
		self::$member_id=$row[0]['member_id'];
		return 1;
	}
	
	static function isAccountHolder($account_id) { 	
		$sql = "SELECT holder_id, authcode FROM holders WHERE account_id IN (:account_id) AND user_id IN (:user_id)";
		$row = DBquery::get($sql, array('account_id'=>$account_id, 'user_id'=>self::$user_id));
		if (!$row) Error::http(401, "User #". self::$user_id ." is not an account holder for account #$account_id.");
		
		self::$holder_id=$row[0]['holder_id'];
		return $row[0];
	}
	
	static function isMember($brand_id) {
		$sql = "SELECT member_id FROM members WHERE brand_id IN (:brand_id) AND user_id IN (:user_id) LIMIT 1";
		$row = DBquery::get($sql, array('brand_id'=>$brand_id, 'user_id'=>self::$user_id));
		if ($row) return 1; 
	}
}

