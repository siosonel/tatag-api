<?php

class BrandAccounts extends Collection {
	function __construct($data='') {		
		$this->brand_id = $this->getID();
		if (!Requester::isBrandAdmin($this->brand_id)) Error::http(403, "The requester is not an admin for brand #$this->brand_id.");
		
		$this->{"@type"} = 'brandAccounts';		
		$this->{"@id"} = "/brand/$this->brand_id/accounts";
		$this->table = "accounts";
		$this->idkey = 'account_id';
		
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
		$sql = "SELECT accounts.account_id, name, 
			sign*(balance+sign*(COALESCE(t.amount,0) - COALESCE(f.amount,0))) AS balance,
			unit, authcode, created, throttle_id			
		FROM accounts
		LEFT JOIN (
			SELECT from_acct, SUM(amount) AS amount 
			FROM records 
			JOIN accounts ON from_acct=accounts.account_id 
			WHERE brand_id=?
			GROUP BY from_acct
		) f ON from_acct=account_id
		LEFT JOIN (
			SELECT to_acct, SUM(amount) AS amount 
			FROM records
			JOIN accounts ON to_acct=accounts.account_id
			WHERE brand_id=?
			GROUP BY to_acct
		) t ON to_acct=account_id
		WHERE brand_id=? AND account_id $this->ltgt ?
		GROUP BY account_id
		ORDER BY account_id $this->pageOrder
		LIMIT $this->itemsLimit";
		
		$this->items = DBquery::get($sql, array($this->brand_id, $this->brand_id, $this->brand_id, $this->limitID));
		foreach($this->items AS &$r) {
			$r['@id'] = $this->{"@id"} ."?account_id=". $r['account_id'];
			$r['links']['holders'] = "/account/". $r['account_id'] ."/holders";
		}
		
		//the paginate function will call setForms() when there are no next/prev pages to set
		//that is, only embed forms in the first page
		$this->paginate('account_id');		
		return array($this);
	}
}

?>