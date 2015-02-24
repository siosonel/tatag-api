<?php

require_once "models/BrandHolders.php";

class AccountHolders extends BrandHolders {
	function __construct($data='') {		
		$this->account_id = $this->getID();
		$this->setDetails();
		if (!Requester::isBrandAdmin($this->brand_id)) Error::http(403, "Only brand admins can access member accounts resource.");
				
		$this->{"@type"} = "accountHolders";		
		$this->{'@id'} = "/account/$this->account_id/holders";
		$this->table = "holders";
		$this->idkey = 'holder_id';
		
		$this->init($data);
		
		$this->okToAdd = array("user_id", "account_id", "authcode", "limkey");
		$this->okToGet = array("holder_id", "holder_auth", "brand_id", "account_id", "name", "account_auth");
		$this->okToSet = array("authcode", "ended");
		$this->okToFilterBy =  array("holder_id");	
	}
	
	function setDetails() {		
		$sql = "SELECT brand_id, account_id FROM accounts WHERE account_id=? AND ended IS NULL";
		$row = DBquery::get($sql, array($this->account_id));
		if (!$row) Error::http(404, "Account #$this->account_id was not found."); 
		
		foreach($row[0] AS $key=>$val) $this->$key = $val;		
		return;
	}
	
	function get() {
		$sql = "SELECT holder_id, h.authcode, h.account_id, a.brand_id, a.name, h.created, member_id, h.user_id, u.name
			FROM holders h
			JOIN accounts a ON h.account_id=a.account_id
			LEFT JOIN members m ON h.user_id=m.user_id
			LEFT JOIN users u ON u.user_id=h.user_id 
			WHERE h.account_id=?
			GROUP BY h.holder_id, h.account_id, h.user_id";
		
		$this->items = DBquery::get($sql, array($this->account_id));
		
		foreach($this->items AS &$r) {
			$r['@id'] = $this->{'@id'} ."?holder_id=". $r['holder_id'];
		}
		
		return array($this);
	}
}