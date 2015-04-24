<?php

class TeamAccounts extends Collection {
	function __construct($data='') {		
		$this->brand_id = $this->getID();
		if (!Requester::isMember($this->brand_id)) Error::http(403, "The requester is not an admin for brand #$this->brand_id.");
		
		$this->{"@type"} = 'teamAccounts';		
		$this->{"@id"} = "$this->root/team/$this->brand_id/accounts";
		$this->table = "accounts";
		$this->idkey = 'account_id';
		
		$this->init($data); 
		
		$this->okToGet = array("brand_id", "account_id", "name", "balance", "unit", "authcode");
	}
	
	function get() {
		$sql = "CALL brandAccountsAsc($this->brand_id, 0, 100)";		
		$this->items = DBquery::get($sql, array($this->brand_id, $this->brand_id, $this->brand_id));
		foreach($this->items AS &$r) {
			$r['@id'] = $this->{"@id"} ."?account_id=". $r['account_id'];
		}
		
		$this->paginate('account_id');
		return array($this);
	}
}

?>