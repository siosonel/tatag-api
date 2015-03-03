<?php

class PhlatMedia {	
	public static function write($output, $error="") {
		$wrapper = new stdClass();
		
		$wrapper->{"@context"}= "--test--";
		
		if ($error) $wrapper->error = $error;		
		$graph = $output;
		
		$wrapper->{"@graph"} = $output;
	
		exit(json_encode($wrapper, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
	}	
}

class Error {
	public static $test_mode;
	public static $codePath;
	public static $debug=0;
	public static $log="";
	
	//
	static function log() {
	
	}
	
	static function http($numCode, $errorMssg="", $output=array()) {
		if (function_exists("http_response_code")) http_response_code($numCode);
		else header("HTTP/1.1 $numCode");
		
		if (self::$log) file_put_contents(self::$log, $errorMssg, FILE_APPEND);
		exit(PhlatMedia::write($output, $errorMssg));
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
		if (!isset(self::$dbs[$alias])) Error::http(500, "Missing connection alias.");
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
				Error::http(500, json_encode($info[2]));
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



