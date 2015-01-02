<?php

class Router {
	public static $table;
	public static $id;
	public static $method; 
	public static $Resource;
	
	public static function run() {
		$_url = trim($_GET['_url'], " \/\\\t\n\r\0\x0B");
		list(self::$table, self::$id) = explode("/", $_url);
		
		$method = strtolower($_SERVER['REQUEST_METHOD']);
		
		if ($method=='post') {
			if (self::$id) $method = 'set';
			else $method = 'add';
		}
		
		self::$method = $method;		
		
		$data = ($method=='get') ? json_decode('{"id":'.self::$id.'}') : json_decode(trim(file_get_contents("php://input")));
		if (gettype($data)!='object') Error::http(400, "Bad Request");
		
		$ObjClass = ucfirst(self::$table); 
		if (!self::$table OR !file_exists("models/$ObjClass.php")) Error::http(404, "The resource='".self::$table."' does not exist");
				
		require_once "models/$ObjClass.php";
		self::$Resource = new $ObjClass($data);		
		
		if (!method_exists(self::$Resource,$method)) Error::http(405, "The method='$method' is not supported by resource='". self::$table ."'.");		
		
		exit(json_encode(self::$Resource->$method(), JSON_NUMERIC_CHECK));
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
	
	static function get($statement, $vars=array()) {
		if (gettype($statement=='string')) $statement = self::$conn->prepare($statement); 
		
		try {
			$result = $statement->execute($vars); 
			
			if (!$result) {
				$info = $statement->errorInfo(); 
				Error::http(500, $info[2]);
			}
			
			if (!$statement->rowCount()) return array();
		} 
		catch(PDOException $e) { 
			Error::http(500, $e->getMessage()); 
		}
		
		self::$statement = $statement;
		$results = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor(); 
		return $results;
	}
	
	static function set($statement, $vars=array()) {
		if (gettype($statement=='string')) $statement = self::$conn->prepare($statement);
		
		try { 
			$result = $statement->execute($vars);
			
			if (!$result) {
				$info = $statement->errorInfo(); 
				Error::http(500, $info[2]);
			}
			
			return $statement->rowCount();
		} 
		catch(PDOException $e) { 
			Error::halt($e->getMessage()); 
		} 
	}
}



