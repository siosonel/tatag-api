<?php

class TeamAccounts extends Base {
	function __construct($data='') {		
		$this->brand_id = $this->getID();
		if (!Requester::isMember($this->brand_id)) Error::http(403, "The requester is not an admin for brand #$this->brand_id.");
		
		$this->{"@type"} = 'teamAccounts';		
		$this->{"@id"} = "/team/$this->brand_id/accounts";
		$this->table = "accounts";
		$this->idkey = 'account_id';
		
		$this->init($data); 
		
		$this->okToGet = array("brand_id", "account_id", "name", "balance", "unit", "authcode");
	}
	
	function get() {
		$sql = "SELECT accounts.account_id, name, 
			sign*(balance+sign*(COALESCE(t.amount,0) - COALESCE(f.amount,0))) AS balance,
			unit, authcode, created			
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
		GROUP BY account_id
		ORDER BY account_id ASC";
		
		$this->items = DBquery::get($sql, array($this->brand_id, $this->brand_id, $this->brand_id));
		foreach($this->items AS &$r) {
			$r['@id'] = $this->{"@id"} ."?account_id=". $r['account_id'];
		}
		
		return array($this);
	}
}

?>