<?php

class BrandHolders extends Base {
	function __construct($data='') { 
		$this->brand_id = $this->getID();
		if (!Requester::isBrandAdmin($this->brand_id)) Error::http(403, "The requester is not an admin for brand #$this->brand_id.");
		
		$this->{"@type"} = 'brandHolders';			
		$this->{"@id"} = "/brand/$this->brand_id/holders";
		$this->table = "holders";
		$this->cols = "holder_id,user_id,account_id,authcode,created";		
		$this->idkey = 'holder_id';
		
		$this->init($data);
		
		$this->okToAdd = array("user_id", "account_id", "authcode", "limkey");	
		$this->okToSet = array("authcode", "ended");	
		$this->okToFilterBy = array("brand_id", "account_id", "user_id");
		$this->okToGet = array('holder_id', 'account_id', 'authcode');
	}
	
	function add() {		
		$this->addKeyVal('limkey','abc');
		
		if ($row = $this->getByUserAndAccountId($this->obj->user_id,$this->obj->account_id)) return $row[0]; 
		else $this->holder_id = $this->insert();
		
		return $this;
	}
	
	function set() {
		$info = $this->getInfo()[0];
		
		$this->update('WHERE holder_id=?', array($this->holder_id));
		foreach($info AS $key=>$val) $this->$key = $val;
		return $this;
	}
	
	function get() {
		$sql = "SELECT user_id, h.account_id, h.authcode, a.authcode, alias, h.created
			FROM holders h JOIN accounts a ON h.account_id=a.account_id
			WHERE brand_id=?";
		
		$this->items = DBquery::get($sql, array($this->brand_id));
		$this->setForms();		
		return array($this);
	}
	
	function getByUserAndAccountId($user_id,$account_id) {
		$sql = "SELECT holder_id, user_id, account_id, authcode, created FROM holders WHERE user_id=? AND account_id=?";
		return DBquery::get($sql, array($user_id, $account_id));		
	}
}

?>