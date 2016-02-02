<?php
/*
Holder access to user's accounts
*/

class UserAccounts extends Base {	
	function __construct($data='') { 
		$this->{"@type"} = 'userAccounts'; //print_r($data);
		$this->user_id =  Router::$id ? $this->getID() : Requester::$user_id; //print_r($this);
		if (!Requester::isUser($this->user_id)) Error::http(401, "The requester must be logged in as the requested user.");
		
		$this->{"@id"} = "$this->root/user/$this->user_id/accounts";
		$this->collectionOf = "holding";
		
		$this->init($data);
		
		$this->okToSet = array("alias", "limkey");
		$this->okToFilterBy =  array("user_id", "holder_id");	
	}
	
	function add($data='') {
		Error::http(403);
	}
	
	function set() {
		$this->table = "holders";
		$this->setFilters($_GET);
		$sql = "SELECT user_id, holder_id FROM $this->table WHERE $this->filterCond";
		$rows = DBquery::get($sql, $this->filterValArr);
		
		foreach($rows AS $r) {
			if ($r['user_id'] != $this->user_id) 
				Error::http(403, "The requester cannot set another holder's information. 
				Please check that requester (#$this->user_id) is filtering by his or her own holder_id (#". $r['holder_id'] .").");
		}
		
		$this->update();
		return array($this->obj);
	}
	
	function get() {
		$forms = array();
		$graph = array($this);
		
		$sql = "CALL userAccounts(?)"; 			
		$items = DBquery::get($sql, array($this->user_id));
		$this->setForms();
		$tracked = array();
		$nestingRef = array(
			"account_" => array(
				"@id" => "$this->root/account/{id}", 
				"@type" => "account",
				"records" => "$this->root/account/{id}/records"
			),
			"brand_" => array(
				"@id" => "$this->root/team/{id}", 
				"@type" => "brand",
				"members"=> "$this->root/team/{id}/members",
				"accounts"=> "$this->root/team/{id}/accounts",
				"throttles"=> "$this->root/team/{id}/throttles",
				"records"=> "$this->root/budget/{id}/records",
				"about"=> "$this->root/brand/{id}/about",
				"promos"=> "$this->root/brand/{id}/promos",
				"tally"=> "$this->root/brand/{id}/tally",
				"edit"=> "/form/brand-edit"
			),
			"user_" =>  array(
				"@id" => "$this->root/user/{id}", 
				"@type" => "user"
			),
			"throttle_" =>  array(
				"@id" => "$this->root/throttle/{id}", 
				"@type" => "throttle"
			)			
		);
		
		foreach($items AS &$r) {
			if (!$r['alias']) $r['alias'] = $r['account_name']; 
			
			$this->nestResources($r, $nestingRef, $graph, $tracked, $skip);
			
			$r['relay'] = array();
			$r['@type'] = 'userAccount';
			$r['@id'] = $this->{'@id'} ."?holder_id=$r[id]";
			
			$this->setAllowedActions($r, $graph[$tracked[$r['account']]]);
			//$r['holder-edit'] = "$this->root/form/holder-edit";
			$r['edit'] = "$this->root/form/holder-edit";
			$r['relays'] = "$this->root/holder/$r[id]/relays";
			$graph[] = $r;
			$this->holding[] = $r['@id'];
		}
		
		$this->items = array();
		//$this->{$this->collectionOf} = $this->items;
		
		return $graph;
	}
	
	function setAllowedActions(&$holder, $account) {
		if (strpos($holder['authcode'],"*")!==false) $holder['authcode'] = $account['authcode'];
		else $holder['authcode'] = implode("", array_intersect(str_split($holder['authcode']), str_split($account['authcode'])));
		
		$auth = "_".$holder['authcode']; //indent to not have to use strict strpos false comparison
		
		$holder['relayDefault'] = array(
			"token" => "$holder[id]-$holder[limkey]",
			"for" => array()
		);
		
		if (strpos($auth,"c")) {
			if ($account['sign']==1) $holder["relayDefault"]["for"][] = "add";
			else $holder['add'] = "$this->root/form/budget-add";
		}

		if (strpos($auth,"f")) $holder['transfer'] = "$this->root/form/budget-transfer";
		if (strpos($auth,"t")) $holder["relayDefault"]["for"][] = "transfer";
		
		if (strpos($auth,"i") OR strpos($auth,"x")) {
			if ($account['sign']==-1) $holder["relayDefault"]["for"][] = "use";
			else $holder['use'] = "$this->root/form/budget-use";
		}
		
		//unset($holder['authcode']);
	}
}

