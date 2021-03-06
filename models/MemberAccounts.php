<?php

require_once "models/BrandHolders.php";

class MemberAccounts extends BrandHolders {
	public $id;
	protected $member_id;
	protected $account_id;
	protected $brand_id;
	
	function __construct($data='') {		
		$this->member_id = $this->getID();
		$this->setDetails();
		if (!Requester::isBrandAdmin($this->brand_id)) Error::http(403, "Only brand admins can access member accounts resource.");
				
		$this->{"@type"} = "memberAccounts";		
		$this->{'@id'} = "$this->root/member/$this->member_id/accounts";
		$this->table = "holders";
		$this->idkey = 'holder_id';
		$this->collectionOf = "holding";
		
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
		
		$this->brand = "$this->root/brand/". $row[0]['brand_id'];		
		$this->id = $this->member_id;
		
		return;
	}
	
	function get() {
		$this->add = "$this->root/form/holder-add";
		
		$graph = array($this);
		$tracked = array();
		
		$nestingRef = array(
			"account_" => array(
				"@id" => "$this->root/account/{id}", 
				"@type" => "account",
				"records" => "$this->root/account/{id}/records"
			),
			"brand_" => array(
				"@id" => "$this->root/brand/{id}", 
				"@type" => "brand"
			)	
		);
		
		$sql = "SELECT holder_id AS id, 
				h.authcode AS authcode, 
				h.account_id AS account_id, 
				a.brand_id AS account_brand_id, 
				a.name AS account_name, 
				h.created
			FROM holders h
			JOIN accounts a ON h.account_id=a.account_id
			JOIN members m ON h.user_id=m.user_id AND a.brand_id=m.brand_id
			WHERE h.user_id=? AND a.brand_id=? AND holder_id $this->ltgt $this->limitID
			GROUP BY h.holder_id, h.account_id, h.user_id
			ORDER BY holder_id $this->pageOrder
			LIMIT $this->itemsLimit";
		
		$items = DBquery::get($sql, array($this->user_id, $this->brand_id)); //print_r($rows);
		
		$this->{$this->collectionOf} = array();
		foreach($items AS &$r) {
			$this->nestResources($r, $nestingRef, $graph, $tracked);
			
			$r['@id'] = $this->{'@id'} ."?holder_id=". $r['id'];
			$r['edit'] = "$this->root/form/admin-holder-edit";
			$this->holding[] = $r['@id'];
			$graph[] = $r;
		}
		
		$this->paginate('id');
		return $graph;
	}
}