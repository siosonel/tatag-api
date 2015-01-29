<?php

//$DEF = json_decode();

class Router {
	public static $resource;
	public static $id;
	public static $method; 
	public static $Resource;
	public static $subresource;
	
	public static function run() {	
		$_url = trim($_GET['_url'], " \/\\\t\n\r\0\x0B");
		if (!$_url) exit(json_encode(self::getLinks(), JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
		list(self::$resource, self::$id, self::$subresource) = explode("/", $_url);
		
		if (self::$subresource=='collection' AND self::$id) Error::http(404, "A generic 'collection' subresource for ". self::$resource ." #". self::$id ." does not exist.");		
		if (!self::$subresource AND !is_numeric(self::$id)) self::$subresource = self::$id;
		//if (!self::$subresource AND !self::$id) self::$subresource = 'links';
		
		
		$method = strtolower($_SERVER['REQUEST_METHOD']);			
		if ($method=='post') { //exit(json_encode(self::$resource ."---". self::$id ."---". self::$subresource));
			if (self::$subresource=='collection') $method = 'add';
			else $method = 'set';
		}
		
		self::$method = $method;		
		
		$data = ($method=='get') ? json_decode(json_encode(array("id"=>self::$id))) : json_decode(trim(file_get_contents("php://input")));
		if (gettype($data)!='object') Error::http(400, "Bad Request");
		
		$ObjClass = ucfirst(self::$resource) . ucfirst(self::$subresource); 
		if (!self::$resource OR !file_exists("models/$ObjClass.php")) Error::http(404, self::getLinks());
				
		require_once "models/$ObjClass.php";
		self::$Resource = new $ObjClass($data);
		
		if (!method_exists(self::$Resource,$method)) Error::http(405, "The method='$method' is not supported by resource='". self::$resource ."'.");		
		
		exit(json_encode(array(
			"@context"=> "--test--",
			"@graph"=> array_merge(self::$Resource->$method(), Requester::$graph)
		), JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
	}
	
	public static function getLinks() {
		$map = json_decode(file_get_contents("ref/tentativeLinks.json"),true);
		
		foreach($map AS $key=>&$val) {
			if ($key=='definitions' || $key=='@type' || $key=='@id') {}
			else if (strpos($key, 'user')===false) unset($map[$key]);
			else $val = str_replace("{user_id}", Requester::$user_id, $val);
		}
		
		$map['_brand'] = Requester::adminLinks();		
		return array("@context"=> "--test--", "@graph"=> array($map));
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
		exit(json_encode($mssg, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
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
				
				$OPTS = $TYPE == 'mysql' ? 	array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'", PDO::MYSQL_ATTR_USE_BUFFERED_QUERY=> true) : array();			
				
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
	
	static function get($statement, $vars=array(), $test=0) {
		if (gettype(self::$statement)=='object') self::$statement->closeCursor();
		if (gettype($statement=='string')) $statement = self::$conn->prepare($statement); 
		
		try {
			$result = $statement->execute($vars); 
			
			if (!$result) {
				$info = $statement->errorInfo();
				Error::http(500, $info[2]);
			}
			
			if (!$statement->rowCount()) return array();
			else {
				self::$statement = $statement;
				$results = $statement->fetchAll(PDO::FETCH_ASSOC);
				return $results;
			}
		} 
		catch(PDOException $e) { 
			Error::http(500, $e->getMessage()); 
		}
	}
	
	static function set($statement, $vars=array()) {
		if (gettype(self::$statement)=='object') self::$statement->closeCursor();
		if (gettype($statement=='string')) $statement = self::$conn->prepare($statement);
		
		try { 
			$result = $statement->execute($vars);
			
			if (!$result) {
				$info = $statement->errorInfo();
				Error::http(500, $info[2]);
			}
			
			self::$statement = $statement;
			$rowcount = $statement->rowCount();
			$statement->fetchAll();
			return $rowcount;
		} 
		catch(PDOException $e) { 
			Error::http(500, $e->getMessage()); 
		} 
	}
}



