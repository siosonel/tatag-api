<?php

class HolderRelays extends Collection {
	function __construct($data='') { 		
		$this->holder_id = Router::$resource=='holder' ? $this->getID() : 0;
		
		if (!$this->holder_id AND $data->holder_id) {
			$this->holder_id = $data->holder_id;
			unset($data->holder_id);
		}
		
		if (!in_array($this->holder_id, Requester::holderIDs())) Error::http(
			403, "The user does not have access to this accountholder's information."
		);
		
		$this->{"@type"} = "holderRelays";
		$params = $_GET ? '?'. http_build_query($_GET) : '';
		$this->{'@id'} = "$this->root/holder/$this->holder_id/relays". $params;
		$this->table = "relays";
		$this->idkey = 'relay_id';
		
		$this->pageOrder = "desc"; //prevents being reset
		$this->init($data);
		
		$this->okToFilterBy = array("holder_id", "relay_id", 'txn_type', 'tag', 'r.holder_id');
	}
	
	function add() {
		$this->okToAdd = array(
			"holder_id","amount_min","amount_max","redirect","tag","secret","txntype",
			"by_all_limit", "by_brand_limit", "by_user_limit", "by_user_wait"
		);
		
		$this->addKeyVal('holder_id',$this->holder_id,'ifMissing');
		$this->addKeyVal('redirect','NULL','ifMissing');
		$this->addKeyVal('tag','NULL','ifMissing');
		$this->addKeyVal('txntype','pn','ifMissing');
		$this->addKeyVal('secret','NULL','ifMissing');
		$this->addKeyVal('by_all_limit',25,'ifMissing');
		$this->addKeyVal('by_brand_limit',5,'ifMissing');
		$this->addKeyVal('by_user_limit',2,'ifMissing');
		$this->addKeyVal('by_user_wait',48,'ifMissing');
		
		$this->obj->relay_id = $this->insert();	
		return array($this->obj);
	}
	
	function set() {
		if (!in_array($this->holder_id, Requester::holderIDs())) Error::http(403, 
				"The user does not have access to this accountholder's information.");
		return $this->add();
	}
	
	function get() {		
		$_GET['r.holder_id'] = $this->holder_id;
		unset($_GET['holder_id']);
		$this->setFilters($_GET);		
		unset($_GET['r.holder_id']);
		
		$sql = "SELECT relay_id AS id, r.holder_id, user_id, account_id, amount_min, amount_max, redirect, secret, tag, txntype, r.created, r.updated,
			by_all_limit, by_brand_limit, by_user_limit, by_user_wait
			FROM relays r
			JOIN holders h ON h.holder_id=r.holder_id
			WHERE $this->filterCond AND r.relay_id < $this->limitID
			ORDER BY id ASC
			LIMIT $this->itemsLimit";
			
		$this->items = DBquery::get($sql, array_merge($this->filterValArr));
		
		foreach($this->items AS &$r) {
			if ($r['user_id'] != Requester::$user_id) Error::http(403, 
				"The user is not the accountholder of this relay and does not have accees to its details.");
			
			$r['token'] = $r['secret'] ? $r['id'] .".". $r['secret'] : $r['id'];
			$r['@id'] = "$this->root/relay/". $r['id'];
			$r['@type'] = 'relay';
			$r['edit'] = '/form/relay-edit';
		}
		
		$this->paginate('relay_id');
		$this->{'add'} = "/form/holder-relays-add";
		return array($this);
	}
}

