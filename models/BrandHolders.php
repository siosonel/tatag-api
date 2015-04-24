<?php

class BrandHolders extends Collection {
	function __construct($data='') { 
		$this->brand_id = $this->getID();
		if (!Requester::isBrandAdmin($this->brand_id)) Error::http(403, "The requester is not an admin for brand #$this->brand_id.");
		
		$this->{"@type"} = 'brandHolders';			
		$this->{"@id"} = "$this->root/brand/$this->brand_id/holders";
		$this->table = "holders";	
		$this->idkey = 'holder_id';
		
		$this->init($data);
		
		$this->okToAdd = array("user_id", "account_id", "authcode", "limkey");	
		$this->okToSet = array("authcode", "ended");	
		$this->okToFilterBy = array("account_id", "user_id", "holder_id");
		$this->okToGet = array('holder_id', 'account_id', 'authcode');
	}
	
	function add() {		
		$this->addKeyVal('limkey','abc');
		
		if ($row = $this->getByUserAndAccountId($this->obj->user_id,$this->obj->account_id)) return $row[0]; 
		else $this->holder_id = $this->insert();
		
		return array($this);
	}
	
	function set() {		
		if (!$_GET) return $this->add();
		
		$this->update($_GET);
		return array($this);
	}
	
	function get() {		
		$sql = "SELECT holder_id, user_id, h.account_id, h.authcode, a.authcode, alias, h.created
			FROM holders h JOIN accounts a ON h.account_id=a.account_id
			WHERE brand_id=? AND holder_id $this->ltgt $this->limitID
			ORDER BY holder_id $this->pageOrder
			LIMIT $this->itemsLimit";
		
		$this->items = DBquery::get($sql, array($this->brand_id));
		$this->paginate('holder_id');
		return array($this);
	}
	
	function getByUserAndAccountId($user_id,$account_id) {
		$sql = "SELECT holder_id, user_id, account_id, authcode, created FROM holders WHERE user_id=? AND account_id=?";
		return DBquery::get($sql, array($user_id, $account_id));		
	}
}

?>