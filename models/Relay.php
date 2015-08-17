<?php

class Relay extends Base {
	function __construct($data='') { 		
		$this->relay_id = $this->getID();
		
		if (Router::$resource=='relay' AND !Requester::isRelayHolder($this->relay_id)) Error::http(
			403, "The user does not have access to this accountholder's information."
		);
		
		$this->{"@type"} = "relay";
		$this->{'@id'} = "$this->root/relay/$this->relay_id";
		$this->table = "relays";
		$this->idkey = 'relay_id';
		
		$this->init($data);
		$this->okToFilterBy = array('relay_id');
	}
	
	function set() {
		$this->okToSet = array('secret','amount_min','amount_max','tag','redirect','ended','txntype');				
		$this->addKeyVal('secret','NULL','ifMissing');		
		$this->addKeyVal('redirect','NULL','ifMissing');			
		$this->addKeyVal('tag','NULL','ifMissing');			
		$this->addKeyVal('txntype','pn','ifMissing');
		
		$_GET['holder_id'] = $this->holder_id;
		$this->update(array('relay_id'=>$this->relay_id));
		return array($this->obj);
	}
	
	function get() {				
		$sql = "SELECT relay_id, r.holder_id, user_id, account_id, amount_min, amount_max, qty, redirect, secret, tag, txntype, r.created, r.updated, r.ended, qty, by_all_period, by_all_limit, by_brand_period, by_brand_limit, by_user_period, by_user_limit
			FROM relays r
			JOIN holders h ON h.holder_id=r.holder_id
			WHERE relay_id=?";
			
		$rows = DBquery::get($sql, array($this->relay_id));
		if (!$rows) return array(new stdClass());
		if (1*$rows[0]['user_id'] != Requester::$user_id) Error::http(
			403, "The user is not the accountholder of this relay and does not have accees to its details."
		);
		
		
		$this->setForms();
		foreach($rows[0] AS $k=>$v) $this->$k = $v;
		return array($this);
	}
	
	function setDetails($relay_id) {	
		$sql = "SELECT relay_id, secret, r.holder_id AS holder_id, limkey, txntype, COALESCE(amount_min,0) as amount_min, COALESCE(amount_max,999999999) as amount_max, qty, by_all_period, by_all_limit, by_brand_period, by_brand_limit, by_user_period, by_user_limit 
		FROM relays r 
		JOIN holders USING (holder_id) 
		WHERE relay_id=? AND r.ended IS NULL";
		$rows = DBquery::get($sql, array($relay_id));
		if (!$rows) Error::http(403, "Relay id# '$relay id' is not active.");
		
		foreach($rows[0] AS $k=>$v) $this->$k = $v;
	}
	
	function checkAgainst($secret, $amount) {
		$mssg="";
		
		if ($this->secret AND $this->secret != $secret) $mssg .= "Invalid relay credentials.";
		
		if ($this->qty == 0) $mssg .= "The total usage limit for relay #$relay_id has been exceeded.";
		
		if ($this->amount_min > $amount OR $this->amount_max < $amount) $mssg .= "The amount must be between $this->amount_min and $this->amount_max.";
		
		if ($mssg) Error::http(403, $mssg);
	}
	
	function getDefaultAmount() {
		return $this->amount_min > 0 ? $this->amount_min : $this->amount_max;
	}
	
	function adjustQty() {
		if (!isset($this->qty) OR $this->qty < 1) return;
		$sql = "UPDATE relays SET qty = qty-1 WHERE relay_id=?";
		$mssg = DBquery::set($sql, array($this->relay_id));
	}
}

