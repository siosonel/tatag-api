<?php
/*
Public access to users collection
*/

require_once "utils/Requester.php";

class UserCollection extends Base {	
	function __construct($data='') {
		$this->{"@id"} = "$this->root/user/collection";
		$this->{'@type'} = "userCollection";
		$this->table = 'users';
		$this->idkey = 'user_id';
		$this->init($data);
		
		$this->okToAdd = array('email', 'name', 'password', "fb_id", "gp_id", "tw_id", "login_provider", 'phone', 'wallet', "ver_code", "ver_expires");
	}
	
	function add($data='') {
		if (!isset($this->email) AND !isset($this->fb_id) AND !isset($this->gp_id) AND !isset($this->tw_id)) Error::http(400, "When registerng a user, an email, facebook id (fb_id), google+ id (gp_id) , or twitter id (tw_id) must be used as input.");
		
		foreach($this->okToAdd AS $key) $this->addKeyVal($key,"NULL","ifMissing");
	
		$this->obj->password = password_hash($this->obj->password, PASSWORD_DEFAULT);
		$this->valArr[ array_search('password', $this->keyArr) ] = $this->obj->password; 
		
		$User = $this->obj;
		$User->user_id = $this->insert();
		
		require_once "utils/Router.php";
		Requester::$user_id = $User->user_id;
		
		unset($User->password); //no need to communicate this back for privacy
		
		$this->setDefaultBrand();
		return array($User);
	}
	
	function get() {
		$this->setForms();
	
		$sql = "SELECT COUNT(*) AS numUsers, MIN(created) AS earliest, MAX(created) AS latest FROM users";		
		$row = DBquery::get($sql);		
		if (!$row) return array($this);
		foreach($row[0] AS $key=>$val) $this->$key = $val;
		
		return array($this);
	}
	
	function setDefaultBrand() {
		if (AUDIENCE == 'sim') return;
		
		require_once "models/BrandCollection.php";
		require_once "models/Brand.php";
		require_once "models/BudgetIssued.php";
		require_once "models/BrandPromos.php";
		
		$Brand = (new BrandCollection(json_decode('{
			"name": "test-'. time() .'",
			"mission": "test and strengthen the TraceEval ecosystem",
			"description": "This is a trial brand that the user can close at any time."
		}')))->add()[0];
		
		Router::$id = $Brand->brand_id;		
		$b = (new Brand(json_decode('{
			"name": "brand'. $Brand->brand_id .'"
		}')))->set();
				
		$BudgetIssued = (new BudgetIssued(json_decode('{
			"from": "'. $Brand->holders[0]->holder_id .'-'. $Brand->holders[0]->limkey .'", 
			"to": "'. $Brand->holders[1]->holder_id .'-'. $Brand->holders[1]->limkey .'",
			"amount": 10.00,
			"note": "first issued budget"
		}')))->add();

		$Promo = (new BrandPromos(json_decode('{
			"name": "Welcome Team #'. $Brand->brand_id .'",
			"description": "Help new teams reach their starting revenue goals. More teams leads to a more robust market!",
			"amount": 1.00,
			"holder_id": '. $Brand->holders[0]->holder_id .',
			"keyword": "welcome",
			"by_all_limit": 10,
			"by_brand_limit": 2,
			"by_user_limit": 1,
			"by_user_wait": 336
		}')))->add();
	}
}

