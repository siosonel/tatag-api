<?php

require_once "models/BrandHolders.php";

class AccountHolders extends BrandHolders {
	public $id;
	protected $brand_id;
	protected $account_id;
	
	function __construct($data='') {		
		$this->account_id = $this->getID();
		$this->setDetails();
		if (!Requester::isBrandAdmin($this->brand_id)) Error::http(403, "Only brand admins can access member accounts resource.");
				
		$this->{"@type"} = "accountHolders";		
		$this->{'@id'} = "$this->root/account/$this->account_id/holders";
		$this->table = "holders";
		$this->idkey = 'holder_id';
		$this->collectionOf = "holder";
		
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
		
		$this->id = $this->account_id;
		$this->brand = "$this->root/brand/". $row[0]['brand_id'];
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
			),
			"user_" =>  array(
				"@id" => "$this->root/user/{id}/about", 
				"@type" => "user"
			)		
		);
		
		$sql = "SELECT holder_id AS id, 
				h.authcode AS authcode, 
				h.account_id AS account_id, 
				a.brand_id AS account_brand_id, 
				a.name AS account_name, 
				h.created, 
				member_id, 
				h.user_id AS user_id, 
				u.name AS user_name
			FROM holders h
			JOIN accounts a ON h.account_id=a.account_id
			LEFT JOIN members m ON h.user_id=m.user_id
			LEFT JOIN users u ON u.user_id=h.user_id 
			WHERE h.account_id=? AND holder_id $this->ltgt $this->limitID
			GROUP BY h.holder_id, h.account_id, h.user_id
			ORDER BY holder_id $this->pageOrder
			LIMIT $this->itemsLimit";
		
		$items = DBquery::get($sql, array($this->account_id));
		
		$this->{$this->collectionOf} = array();
		foreach($items AS &$r) {
			$this->nestResources($r, $nestingRef, $graph, $tracked);
			
			$r['@id'] = $this->{'@id'} ."?holder_id=". $r['id'];
			$r['edit'] = "$this->root/form/admin-holder-edit";
			$this->{$this->collectionOf}[] = $r['@id'];
			$graph[] = $r;
		}
		
		$this->paginate('holder_id');
		return $graph;
	}
}