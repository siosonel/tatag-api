<?php

class Home {
	function __construct() {}
	
	function get() {		
		$home = json_decode(file_get_contents("ref/examples/home.json"),true);
		$home['@graph'][0]['linkTerms'] = json_decode(file_get_contents("ref/examples/linkTerms.json"),true);
		$home['@graph'][0]['navDirections'] = json_decode(file_get_contents("ref/examples/navDirections.json"),true);
		
		foreach($home['@graph'] AS &$r) {
			foreach($r AS $key=>&$val) {
				//if (gettype($val)=="string" AND substr($val,0,5)!='/api/' AND substr($val,0,1)=="/") $val = Router::$root . $val;
				$val = str_replace("{user_id}", Requester::$user_id, $val);
			}
		}
		
		include_once "models/UserTeams.php";
		include_once "models/UserBrands.php";
		
		
		$home['@graph'][] = (new UserTeams())->get()[0];
		$home['@graph'][] = (new UserBrands())->get()[0];
		
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