<?php

class Home {
	function __construct() {
		$this->links = json_decode(file_get_contents("ref/tentativeLinks.json"),true);
		$this->links['navDirections'] = json_decode(file_get_contents("ref/examples/nav-directions.json"),true);
	}
	
	function get() {
		$this->links['@id'] = Router::$root;
		$this->links['user'] = Router::$root . str_replace("{user_id}", Requester::$user_id, $this->links['user']);
		
		if (is_array($this->links['userResource'])) {
			foreach($this->links['userResource'] AS $key=>$val) {
				if (substr($val,0,1)=="/") $val = Router::$root . $val;
				$user[$key] = str_replace("{user_id}", Requester::$user_id, $val);
			}
		}
		
		unset($this->links['userResource']);
		
		$user['name'] = Requester::$name;
		$user['login_provider'] = Requester::$login_provider;
		
		if (!Requester::$user_id) $this->links['userLoginPage'] = '/login.php';
		
		$this->deprecationSupport($user);
		return array($this->links, $user);
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