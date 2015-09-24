<?php

class PhlatMedia {
	private static $format = "formatDefault";

	public static function write($output, $error="") {		
		$response = self::{self::$format}($output, $error);
		
		exit(json_encode($response, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
	}
	
	static function formatDefault($output, $error="") {
		$wrapper = new stdClass();
		$wrapper->{"@context"}= "--test--";		
		
		if ($error) $wrapper->error = $error;			
		$wrapper->{"@graph"} = $output;
		return $wrapper;
	}
	
	static function adjustLinks(&$R) {
		if ($R->{'@id'}) $R->{'@id'} = self::addLinkPrefix($R->{'@id'});
		
		if ($R->links) {
			foreach($R->links AS $key=>&$url) {
				if (!is_array($url)) $url = self::addLinkPrefix($url);
				else {
					foreach($url AS &$u) $u = self::addLinkPrefix($u);
				}
			}
		}
		
		if ($R->actions) {
			foreach($R->actions AS &$url) $url = self::addLinkPrefix($url);
		}
	}
	
	static function addLinkPrefix($link) {
		return "/api$link";
	}
	
	
	static function formatHal($output, $error="") {		
		//this might be useful later on for adjusting link format, 
		//such as converting links to objects with href property
		foreach($output AS $R) {
			self::linksFormatHal($R);
			
			if ($R->items) {
				foreach($R->items AS &$Item) {
					$Item = json_decode(json_encode($Item));
					self::linksFormatHal($Item);
				}
			}
		}
		
		
		if ($error) {
			$hal = new stdClass();
			$hal->error = $error;	
		}
		else $hal = array_shift($output);
	
		$hal->{"_embedded"} = $output;
		return $hal;
	}
	
	static function linksFormatHal(&$R) {
		if ($R->{'@id'}) $R->{'@id'} = self::addLinkPrefix($R->{'@id'});
		
		if ($R->links) {
			foreach($R->links AS $key=>&$url) {
				if (!is_array($url)) $url = self::linkObject($url);
				else {
					foreach($url AS &$u) $u = self::linkObject($u);
				}
			}
		}
		else $R->links = array();
		
		$rel_self = new stdClass();
		$rel_self->href = $R->{'@id'};
		
		if (is_array($R->links)) $R->links['self'] = $rel_self;
		else $R->links->self = $rel_self;
		
		if ($R->actions) {
			foreach($R->actions AS &$url) $url = self::addLinkPrefix($url);
		}
	}
	
	static function linkObject($href) {
		$link = new stdClass();
		$link->href = "/api$href";
		return $link;
	}
}

class Error {
	public static $test_mode;
	public static $codePath;
	public static $debug=0;
	public static $log="";
	public static $format='';
	
	//
	static function log() {
	
	}
	
	static function http($numCode, $errorMssg="", $output=array()) {
		if (self::$format=='sms') self::sms($errorMssg);
		else {
			if (function_exists("http_response_code")) http_response_code($numCode);
			else header("HTTP/1.1 $numCode");
			
			if (self::$log) file_put_contents(self::$log, $errorMssg, FILE_APPEND);
			exit(PhlatMedia::write($output, $errorMssg));
		}
	}
	
	static function sms($mssg) {
	 header("content-type: text/xml");
	 exit('<?xml version="1.0" encoding="UTF-8"?>
		<Response>
			<Message>'. $mssg .'</Message>
		</Response>');	
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


class BrandLogo {
	public static function wrap($brand_name, $id, $type='svg') {
		return array(
			"@id" => "$id", 
			$type => "image/svg+xml",
			"data" => $type=='base64svg' ? base64_encode(self::svg($brand_name)) : self::svg($brand_name)
		);
	}
	
	public static function svg($brand_name='{brand_name}') {
		return '<svg>
<rect width="100%" height="100%" fill="rgba(100,150,200,0.5)" />
<circle cx="50%" cy="50%" r="30%" fill="transparent" />
<text x="50%" y="50%" font-size="3rem" text-anchor="middle" fill="white">'. $brand_name .'</text>
</svg>'; 
	}
	
	public static function dataURL($brand_name) {
		return 'data:image/svg+xml;charset=utf-8;base64,'. base64_encode(self::svg($brand_name));
		//return 'data:image/svg+xml;'. self::svg($brand_name);
	}
	
	public static function svgTemplate() {
		return array(
			'@id' => '/ui/logo.php',
			'@type' => 'svgTemplate',
			'delimiter' => array('{', '}'),
			'substitute' => array('brand_name'),
			'data' => self::svg()
		);
	}
}
