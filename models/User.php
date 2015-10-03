<?php
/*
Self access to user's information
*/

class User extends Base {	
	function __construct($data='') {
		$this->{"@type"} = 'user';
		$this->user_id = $this->getID();	
		if (!Requester::isUser($this->user_id)) Error::http(401, "The requester must be logged in as the requested user.");
		
		$this->{"@id"} = "$this->root/user/$this->user_id";		
		$this->table = 'users';
		$this->idkey = 'user_id';
		$this->init($data);
		
		$this->name = Requester::$name;
		$this->email = Requester::$email;		
		$this->okToSet = array("ended","email","name","password");
		$this->okToFilterBy =  array("user_id","email");	
		
		$this->login_provider = Requester::$login_provider;
	}
	
	function add($data='') {
		Error::http(403);
	}
	
	function set() {	
		$this->update(array("user_id" => $this->user_id));
		return array($this);
	}
	
	function get() {		
		$this->userMemberships = $this->{'@id'}."/brands";
		$this->userAccounts = $this->{'@id'}."/accounts";			
		$this->userRatings = $this->{'@id'}."/ratings";
		$this->promoCollection = "$this->root/promo/collection";
		$this->brandCollection = "$this->root/brand/collection";
		$this->promoSearch = "/forms#promo-search";
		$this->setForms();	
		
		include_once "models/UserBrands.php";		
		include_once "models/UserAccounts.php";
		include_once "models/UserRatings.php";
		$obj = json_decode('{"user_id":' . $this->user_id .'}');	
		
		$memberships = (new UserBrands($obj))->get(); 
		foreach($memberships AS $m) {
			foreach($m->items AS $b) {
				$this->team[] = "$this->root/team/". $b['brand_id'];
				if ($b['role']=='admin') $this->brand[] = "$this->root/brand/". $b['brand_id'];
			}
		}
		
		return array_merge(
			array($this)
			, $memberships
			, (new UserAccounts($obj))->get()
			, (new UserRatings($obj))->get()
		);
	}
}
