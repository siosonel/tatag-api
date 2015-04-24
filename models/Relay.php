<?php

class Relay extends Base {
	function __construct($data='') { 		
		$this->relay_id = $this->getID();
		//if (!$this->isHolder()) Error::http(403, "The requester is not the user for this accountholding='$this->holder_id'.");
		
		$this->{"@type"} = "relay";
		$this->{'@id'} = "$this->root/relay/$this->relay_id";
		$this->table = "relays";
		$this->idkey = 'relay_id';
		
		$this->init($data);
		$this->okToFilterBy = array('relay_id');
	}
	
	function set() {
		$this->okToSet = array('secret','amount_min','amount_max','tag','redirect','ended');				
		$this->addKeyVal('secret','NULL','ifMissing');		
		$this->addKeyVal('redirect','NULL','ifMissing');			
		$this->addKeyVal('tag','NULL','ifMissing');
		
		$_GET['holder_id'] = $this->holder_id;
		$this->update(array('relay_id'=>$this->relay_id));
		return array($this->obj);
	}
	
	function get() {				
		$sql = "SELECT relay_id, r.holder_id, user_id, account_id, amount_min, amount_max, redirect, secret, tag, txntype, r.created, r.updated
			FROM relays r
			JOIN holders h ON h.holder_id=r.holder_id
			WHERE relay_id=?";
			
		$rows = DBquery::get($sql, array($this->relay_id));
		if (!$rows) return array(new stdClass());
		if (1*$rows[0]['user_id'] != Requester::$user_id) Error::http(403, 
			"The user is not the accountholder of this relay and does not have accees to its details.");
		
		
		$this->setForms();
		foreach($rows[0] AS $k=>$v) $this->$k = $v;
		return array($this);
	}
}

