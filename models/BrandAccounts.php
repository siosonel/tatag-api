<?php

class BrandAccounts extends Collection {
	function __construct($data='') {		
		$this->brand_id = $this->getID();
		if (!Requester::isBrandAdmin($this->brand_id)) Error::http(403, "The requester is not an admin for brand #$this->brand_id.");
		
		$this->{"@type"} = 'brandAccounts';		
		$this->{"@id"} = "$this->root/brand/$this->brand_id/accounts";
		$this->table = "accounts";
		$this->idkey = 'account_id';
		$this->collectionOf = "account";
		
		$this->init($data); 
		
		$this->okToGet = array("brand_id", "account_id", "name", "balance", "unit", "authcode");
		$this->okToAdd = array("brand_id", 'name','authcode','unit','sign');
		
		$this->okToSet = array("name","authcode","throttle_id");
		$this->okToFilterBy = array("brand_id", "account_id");
	}
	
	function add() {
		$this->addKeyVal('brand_id', $this->brand_id);	
		$this->addKeyVal('unit', 'hour', 'ifMissing');	
		$this->addKeyVal('sign', 1, 'ifMissing');		
		$this->account_id = $this->insert();
		return array($this);
	}
	
	function set() {		
		if (!$_GET) return $this->add();
		
		$this->update($_GET);
		return array($this);
	}
	
	function get() {
		$this->add = "$this->root/form/account-add";
	
		$sql = "CALL brandAccountsAsc($this->brand_id, 0, 100)";		
		$items = DBquery::get($sql);
		
		$this->{$this->collectionOf} = array();
		foreach($items AS &$r) {
			$r['@id'] = $this->{"@id"} ."?account_id=". $r['account_id'];
			$r['holders'] = "$this->root/account/". $r['account_id'] ."/holders";			
			$r['edit'] = "$this->root/form/admin-account-edit";
			$this->items[] = $r;
		}
		
		//the paginate function will call setForms() when there are no next/prev pages to set
		//that is, only embed forms in the first page
		$this->paginate('account_id');		
		return array($this);
	}
}

?>