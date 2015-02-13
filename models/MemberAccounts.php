<?php

require_once "models/BrandHolders.php";

class MemberAccounts extends BrandHolders {
	function __construct($data='') {		
		$this->member_id = $this->getID();
		$this->setDetails();
		if (!Requester::isBrandAdmin($this->brand_id)) Error::http(403, "Only brand admins can access member accounts resource.");
				
		$this->{"@type"} = "memberAcounts";		
		$this->{'@id'} = "/member/$this->member_id/accounts";
		$this->table = "holders";
		$this->idkey = 'holder_id';
		
		$this->init($data);
		
		$this->okToAdd = array("user_id", "account_id", "authcode", "limkey");
		$this->okToGet = array("holder_id", "holder_auth", "brand_id", "account_id", "name", "account_auth");
		$this->okToSet = array("authcode", "ended");
		$this->okToFilterBy =  array("holder_id");	
	}
	
	function setDetails() {		
		$sql = "SELECT brand_id, user_id, role, hours, created FROM members WHERE member_id=? AND ended IS NULL";
		$row = DBquery::get($sql, array($this->member_id));
		
		if ($row) { 
			foreach($row[0] AS $key=>$val) $this->$key = $val;
		}
		
		return;
	}
	
	function get() {
		$sql = "SELECT holder_id, h.authcode, h.account_id, a.brand_id, a.name, h.created
			FROM holders h
			JOIN accounts a ON h.account_id=a.account_id
			JOIN members m ON h.user_id=m.user_id AND a.brand_id=m.brand_id
			WHERE h.user_id=?";
		
		$this->items = DBquery::get($sql, array($this->user_id)); print_r($rows);
		
		foreach($this->items AS &$r) {
			$r['@id'] = $this->{'@id'} ."?holder_id=". $r['holder_id'];
		}
		
		return array($this);
	}
}