<?php

class TeamAccounts extends Collection {
	function __construct($data='') {		
		$this->brand_id = $this->getID();
		if (!Requester::isMember($this->brand_id)) Error::http(403, "The requester is not a member of brand #$this->brand_id.");
		
		$this->{"@type"} = 'accounts';		
		$this->{"@id"} = "$this->root/team/$this->brand_id/accounts";
		$this->table = "accounts";
		$this->idkey = 'account_id';
		$this->collectionOf = "account";
		
		$this->init($data); 
		
		$this->okToGet = array("brand_id", "account_id", "name", "balance", "unit", "authcode");
	}
	
	function get() {
		$sql = "CALL brandAccountsAsc($this->brand_id, 0, 100)";		
		$items = DBquery::get($sql, array($this->brand_id, $this->brand_id, $this->brand_id));
		
		foreach($items AS &$r) {
			$r['@id'] = $this->{"@id"} ."?account_id=". $r['account_id'];
			$this->{$this->collectionOf}[] = $r;
		}
		
		$this->paginate('account_id');
		return array($this);
	}
}

?>