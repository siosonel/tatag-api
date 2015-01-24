<?php
/*
Holder access to user's accounts
*/

class UserAccounts extends Base {	
	function __construct($data='') {
		$this->{"@type"} = 'userAccounts';
		$this->user_id =  $this->getID();	
		if (!Requester::isUser($this->user_id)) Error::http(401, "The requester must be logged in as the requested user.");
		
		$this->{"@id"} = "/user/$this->user_id/accounts";
		$this->init($data);
		
		$this->okToSet = array("alias", "limkey");
		$this->okToFilterBy =  array("user_id", "holder_id");	
	}
	
	function add($data='') {
		Error::http(403);
	}
	
	function set() {
		$this->table = "holders";
		$markers = array_pad(array(), count($this->holder_ids), "?");
		$this->update("WHERE holder_id IN ($markers)", $this->holder_ids);
		return $this->obj;
	}
	
	function get() {
		$forms = array();
		
		$sql = "SELECT a.account_id AS account_id, a.name AS account_name, alias,
			h.user_id, a.brand_id AS brand_id, b.name AS brand_name, 			
			sign, balance+sign*(COALESCE(t.amount,0) - COALESCE(f.amount,0)) AS balance, unit,
			holder_id, limkey, a.authcode as account_authcode, h.authcode as holder_authcode,
			m.role As role
			FROM accounts a
			JOIN brands b ON a.brand_id = b.brand_id
			JOIN holders h ON a.account_id=h.account_id AND h.user_id=?
			JOIN members m ON m.brand_id = a.brand_id
			LEFT JOIN (
				SELECT from_acct, SUM(amount) AS amount 
				FROM records
				WHERE entry_id > 0
				GROUP BY from_acct
			) f ON from_acct=a.account_id
			LEFT JOIN (
				SELECT to_acct, SUM(amount) AS amount 
				FROM records
				WHERE entry_id > 0
				GROUP BY to_acct
			) t ON to_acct=a.account_id
			GROUP BY a.account_id"; 
			
		$this->items = DBquery::get($sql, array($this->user_id));
		$this->setForms();
		
		foreach($this->items AS &$row) {	
			$row['@id'] = $this->{'@id'} ."?holder_id=". $row['holder_id']; 	
			$row["_brand"] = "/brand/".$row['brand_id'] ."/about";
			
			if ($row['role']=='admin') {
				$row["_adminViews"] = array("/account/".$row['account_id'], "/brand/".$row['brand_id']);
			}
		}
		
		return $this;
	}
}

