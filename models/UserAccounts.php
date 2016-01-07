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
			"account_" => array("id"=>"@id=$this->root/account/{id}", "#"=>array("@type"=>"account")),
			"brand_" => array("id"=>"@id=$this->root/team/{id}", "#"=>array("@type"=>"brand")),
			"user_" =>  array("id"=>"@id=$this->root/user/{id}", "#"=>array("@type"=>"user"))
		);
		
		foreach($items AS &$r) {
			if (!$r['alias']) $r['alias'] = $r['account_name']; 
			
			$this->nestResources($r, $nestingRef, $graph, $tracked);
			
			$r['relay'] = array();
			$r['@type'] = 'userAccount';
			$r['@id'] = $this->{'@id'} ."?holder_id=$r[id]"; 
			
			$this->setAllowedActions($r, $account);
			$r['holder-edit'] = "$this->root/form/holder-edit";
			$r['edit'] = "$this->root/form/holder-edit";
			$r['relays'] = "$this->root/holder/$r[id]/relays";
			$graph[] = $r;
			$this->items[] = $r['@id'];
		}
		
		$this->{$this->collectionOf} = $this->items;
		
		return $graph;
	}
	
	function setAllowedActions(&$r, $account) {
		if (strpos($r['holder_authcode'],"*")!==false) $r['authcode'] = $account['authcode'];
		else $r['authcode'] = implode("", array_intersect(str_split($r['holder_authcode']), str_split($account['authcode'])));
		
		$auth = "_".$r['authcode']; //indent to not have to use strict strpos false comparison
		
		$r['relay']['default'] = $r['holder_id']."-".$r['limkey'];
		
		if (strpos($auth,"c")) {
			if ($account['sign']==1) $r['relay']['add'] = $r['holder_id']."-".$r['limkey']."-c";
			else $r['add'] = "$this->root/form/budget-add";
		}

		if (strpos($auth,"f")) $r['transfer'] = "$this->root/form/budget-transfer";
		if (strpos($auth,"t")) $r['relay']['transfer'] = $r['holder_id']."-".$r['limkey']."-t";
		
		if (strpos($auth,"i") OR strpos($auth,"x")) {
			if ($account['sign']==-1) $r['relay']['use'] = $r['holder_id']."-".$r['limkey']."-ix";
			else $r['use'] = "$this->root/form/budget-use";
		}
		
		unset($r['authcode']);
	}
}

