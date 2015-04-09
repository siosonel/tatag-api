<?php

class Router {
	public static $resource;
	public static $id;
	public static $method; 
	public static $Resource;
	public static $subresource;
	
	public static function run() {	
		self::parseURL();		
		self::setMethod();
		$output = self::getResource(self::getData());
		PhlatMedia::write($output);
	}
	
	public static function parseURL() {
		$_url = trim($_GET['_url'], " \/\\\t\n\r\0\x0B");
		if (!$_url) PhlatMedia::write(self::getLinks());
		
		
		list(self::$resource, self::$id, self::$subresource) = explode("/", $_url);
		unset($_GET['_url']);
		
		if (self::$subresource=='collection' AND self::$id) Error::http(404, "A generic 'collection' subresource for ". self::$resource ." #". self::$id ." does not exist.");		
		if (!self::$subresource AND !is_numeric(self::$id)) self::$subresource = self::$id;
	}
	
	public static function setMethod() {
		$method = strtolower($_SERVER['REQUEST_METHOD']);
		if ((SITE=='dev' OR SITE=='stage') AND isset($_GET['method'])) {$method=$_GET['method']; unset($_GET['method']);}
		
		if ($method=='post') {
			if (
				self::$subresource=='collection' 
				OR self::$subresource=='throttles' 
				OR strpos(self::$resource, 'budget')!==false 
				OR (self::$resource=='token' AND !self::$id) 
			) $method = 'add';
			else $method = 'set';
		}
		
		self::$method = $method;
	}
	
	public static function getData() {
		$src = "php://input";
		if ((SITE=='dev' OR SITE=='stage') AND isset($_GET['file']) AND file_exists("_exclude/". $_GET['file'] .".json")) $src= "_exclude/". $_GET['file'] .".json";
		
		$data = (self::$method=='get') ? json_decode(json_encode(array("id"=>self::$id))) : json_decode(trim(file_get_contents($src)));
		if (gettype($data)!='object') Error::http(400, "Bad Request - unable to retriev or decode submitted data.");
		return $data;
	}
	
	public static function getResource($data) {
		$ObjClass = ucfirst(self::$resource) . ucfirst(self::$subresource);
		if (!self::$resource OR !file_exists("models/$ObjClass.php")) Error::http(404, "The resource was not found.", self::getLinks());
				
		require_once "models/$ObjClass.php";
		self::$Resource = new $ObjClass($data); 
		
		if (!method_exists(self::$Resource,self::$method)) Error::http(405, "The method='$method' is not supported by resource='$ObjClass'.");	
		$method = self::$method;
		return array_merge(self::$Resource->$method(), Requester::$graph);
	}
	
	public static function getLinks($error="") {
		$links = json_decode(file_get_contents("ref/tentativeLinks.json"),true);
		
		foreach($links AS $key=>&$val) {
			$val = str_replace("{user_id}", Requester::$user_id, $val);
		}
		
		$links['user_id'] = Requester::$user_id;
		$links['name'] = Requester::$name;
		$links['login_provider'] = Requester::$login_provider;
		
		return array($links);
	}
}