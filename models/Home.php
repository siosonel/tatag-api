<?php

class Home {
	function __construct() {}
	
	function get() {		
		$home = json_decode(file_get_contents("ref/examples/wip/home.json"),true);
		$home['@graph'][0]['linkTerms'] = json_decode(file_get_contents("ref/examples/wip/linkTerms.json"),true);
		$home['@graph'][0]['navDirections'] = json_decode(file_get_contents("ref/examples/wip/navDirections.json"),true);
		
		foreach($home['@graph'] AS &$r) {
			foreach($r AS $key=>&$val) {
				//if (gettype($val)=="string" AND substr($val,0,5)!='/api/' AND substr($val,0,1)=="/") $val = Router::$root . $val;
				$val = str_replace("{user_id}", Requester::$user_id, $val);
			}
		}
		
		//unset($this->links['userResource']);
		
		//$user['name'] = Requester::$name;
		//$user['login_provider'] = Requester::$login_provider;
		
		//if (!Requester::$user_id) $this->links['userLoginPage'] = '/login.php';
		
		//$this->deprecationSupport($user);
		//return array($this->links, $user);
		return $home['@graph'];
	}
	
	function deprecationSupport($user=array()) {
		if (!Router::$deprecationDate) return;
		$date = Router::$deprecationDate;
		
		if ($date >= 20151019) return;
		
		if ($date < 20151019) $this->links['deprecated'][] = array(
			"date"=>20151019, 
			"reason"=>"use the link rel=me instead",
			"merge-patch"=> array_diff_key($user,array("@id"=>0))
		);
		
		$this->links['deprecationDate'] = Router::$deprecationDate;
	}
}