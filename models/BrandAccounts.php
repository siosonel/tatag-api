<?php

class BrandAccounts extends Base {
	function __construct($data='') {		
		$this->brand_id = $this->getID();
		if (!Requester::isBrandAdmin($this->brand_id)) Error::http(403, "The requester is not an admin for brand #$this->brand_id.");
		
		
		$this->{"@type"} = 'brandAccounts';
		$this->table = "accounts";
		$this->cols = "account_id,brand_id,name,authcode,unit,balance,sign,created";		
		$this->idkey = 'account_id';
		
		$this->init($data); 
		
		$this->okToGet = array("brand_id", "account_id", "name", "balance", "unit", "authcode");
		$this->okToAdd = array('brand_id','name','authcode','unit','sign');
		
		$this->okToSet = array("name","authcode");
		$this->okToFilterBy = array("brand_id", "account_id");
	}
	
	function add() {				
		$this->addKeyVal('unit', 'hour', 'ifMissing');	
		$this->addKeyVal('sign', 1, 'ifMissing');		
		$this->account_id = $this->insert();
		return $this;
	}
	
	function set() {		
		$this->update('WHERE account_id=?', array($this->account_id));
		return $this;
	}
	
	function get() {
		$sql = "SELECT accounts.account_id, name, 
			sign*(balance+sign*(COALESCE(t.amount,0) - COALESCE(f.amount,0))) AS balance,
			unit, authcode			
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
		WHERE brand_id=?
		GROUP BY account_id";
		
		$this->items = DBquery::get($sql, array($this->brand_id, $this->brand_id, $this->brand_id));
		$this->setForms();
		
		return array($this);
	}
}

?>