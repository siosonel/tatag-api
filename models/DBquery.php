<?php

/*
Purpose: This is a PDO-based replacement for the depracated mysql.dll-based DBquery static class
	- this is backward-compatible to previous usage of DBquery in whatever code files it's currently used
	- PDO connection objects are created as properties of the $dbs argument in init(); these PDO
		objects can be accessed from the original $dbs reference. In other words, connections do not have to be 
		accessed from within this static class.
	- For convenience and backward-compatibility, self::select(), update, and insert do not use PDO prepared statements.
		However, all GET and POST parameter string values are PDO::quote(d) if found within a passed
		SQL statement, so the level of security should be almost the same as using PDO::prepare. Alternatively,
		use the PDO connection object directly outside of this class.
	- useful note: this is a static instead of instantiated class to make it easier to use as a globally scoped object
 
Author: Edgar Sioson
Date: 2013-06-03

*/

if (class_exists("DBquery")) return; //protect against what seems to be multi-thread related error

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
		
		//these parameter values will be substituted with properly quoted/escaped strings when found within SQL statements
		$params = array_unique(array_merge($_GET, $_POST));
		foreach($params AS $key=>$val) {
			if (!is_numeric($val)) {
				if (strpos($val, ',') === false || strpos($val, ' ')) $arr = array($val);
				else {
					$arr = explode(',', $val);
					$arr[] = $val;
				}
				
				foreach($arr AS $v) {
					self::$params[] = "'". $v ."'";
					self::$params[] = '"'. $v .'"';
				}
			}
		} 
		
		// detect if php version supports check_numeric flag in json_encode
		$versionNum = explode(".", phpversion()); 
		if ($versionNum[0] > 4 AND $versionNum[1] > 2) {
			self::$json_numeric = 0;
		} 
	}
  
  static function connect($CONN_ARR, $filter, $user='', $pwd='', $switch_to_alias='') { 
		foreach($CONN_ARR AS $alias => $prop) { 
			if (!$filter OR in_array($alias, $filter)) {
				$TYPE = isset($prop['TYPE']) ? $prop['TYPE'] : 'mysql';
				$SERVER_NAME = $prop['SERVER_NAME'];
				$DB_NAME = $prop['DB_NAME'];
				$USER = !$user ? $prop['USER'] : $user; //optional override of username
				$PWD = !$pwd ? $prop['PWD'] : $pwd; //optional overried of pwd

				if (!$SERVER_NAME OR !$DB_NAME OR !$USER OR !$PWD) Error::halt("Missing DBconnect parameter(s).","DBquery->connect");
				
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
	
	static function select_db($alias) { //echo " $alias ";
		//mysql_select_db($db);
		if (!isset(self::$dbs[$alias])) Error::halt("Missing connection alias.","DBquery->select_db");
		self::$conn = self::$dbs[$alias]['conn'];
	}
  
  //
  static function select($sql, $multiple=FALSE) { //echo "\n$sql\n";
		if (strpos($multiple, ":")) { //change default connection as needed
			list($alias, $multiple) = explode(":", $multiple);
			self::select_db($alias); 
		}
	
		//not using prepared statements for now, so replace GET or POST parameter string occurrences as	needed
    foreach(self::$params AS $val) {
			$sql = str_replace($val, self::$conn->quote(substr($val,1,-1)), $sql); //echo " $val ";
		}
				
		$result = self::$conn->query($sql);
    
    if (!$result) {       
			$info = self::$conn->errorInfo();
			Error::http(500, $info[2] ."--> $sql");
		}
    else {
      if (!$multiple OR $multiple != "row") $select = $result->fetchAll(PDO::FETCH_ASSOC);
			else $select = $result->fetchAll(PDO::FETCH_NUM);
//echo "--". count($select) ." ". gettype($select) ."--". count($select);
      if (self::$json_numeric) { 
				foreach($select AS &$r) {
					foreach($r AS $key=>&$val) {
						if (is_numeric($val)) $val = 1*$val;
					}
				}
			}
    }
    
		$result->closeCursor();
		
    return $select;
  }
  
  //
  static function update($sql) {    
    //not using prepared statements in this case, so quote GET or POST parameter string occurrences as	needed
    foreach(self::$params AS $val) $sql = str_replace($val, self::$conn->quote(substr($val,1,-1)), $sql);
		
		$result = self::$conn->query($sql);
      
    if (!$result) {       
			$info = self::$conn->errorInfo();
			Error::http(500, $info[2] ."--> $sql");
		}
    else if (!$result->rowCount()) 
      $message = self::mssgHandler($sql) ."Affected Rows=0";
              
    return $message;
  }
  
	//
  static function insert($sql) {
    //not using prepared statements in this case, so quote GET or POST parameter string occurrences as	needed
    foreach(self::$params AS $val) $sql = str_replace($val, self::$conn->quote(substr($val,1,-1)), $sql); 
		
		$result = self::$conn->query($sql);
      
		if (!$result) {       
			$info = self::$conn->errorInfo();
			Error::http(500, $info[2] ."--> $sql");
		}
    else return $result->rowCount();
  }
	
	static function get($statement, $vars=array(), $noResultMssg="No results.") {
		if (gettype($statement=='string')) $statement = self::$conn->prepare($statement);
		
		try { 
			$statement->execute($vars);
			if (!$statement->rowCount() AND $noResultMssg) Error::halt($noResultMssg);
    } catch(PDOException $e) { 
			Error::halt($e->getMessage()); 
    }
		
		self::$statement = $statement;	
		$results = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
		return $results;
	}
	
	static function set($prepped, $vars, $noResultMssg) {
		try { 
			$prepped->execute($vars);
			if (!$prepped->rowCount() AND $noResultMssg) Error::halt($noResultMssg);
    } catch(PDOException $e) { 
			Error::halt($e->getMessage()); 
    } 
	}
	
	static function numericArr($arr) {
		foreach($arr AS $v) {
			if (!is_numeric($v)) Error::halt("Non-numeric array value.");
		}
	}

  static function mssgHandler($sql) {   
    return "<br />\nsql: ". $sql ."</br >\n"; //comment this line to silence
  }
}

?>