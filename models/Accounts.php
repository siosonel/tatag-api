<?php

class Accounts extends Base {
	function __construct($data='') {
		$this->table = "accounts";
		$this->cols = "account_id,brand_id,name,authcode,unit,balance,sign,created";
		$this->account_id = $this->getID();
		$this->idkey = 'account_id';
		$this->okToGet = array("brand_id", "account_id", "name", "balance", "unit", "authcode");
		$this->init($data);
	}
	
	function add() {	
		if (Requester::isBrandAdmin($this->brand_id)) {
			array_push($this->okToAdd, 'brand_id','name','authcode','unit','sign');
		}
		
		$this->addKeyVal('unit', 'hour', 'ifMissing');	
		$this->addKeyVal('sign', 1, 'ifMissing');			
		
		$this->account_id = $this->insert();
		return $this;
	}
	
	function set() {
		$info = $this->getInfo()[0];
		if (!$info) return array();
		
		if (Requester::isBrandAdmin($info['brand_id'])) {
			array_push($this->okToSet, "name","authcode");
			array_push($this->okToFilterBy, "brand_id", "account_id");
		}
		
		$this->update('WHERE account_id=?', array($this->account_id));
		return $this;
	}
	
	function get() {
		$info = $this->getInfo()[0];		
		if (!$info) return array();
		
		if (Requester::isBrandAdmin($info['brand_id'])) $info['holders'] = $this->getByAdmin();
		else if (Requester::isAccountHolder($this->account_id)) $info['holders'] = $this->getByHolder();
		else return array(array("balance"=> $info['sign']*$info['balance'], "unit"=> $info['unit']));

		return array($info);
	}
	
	function getInfo() {
		$sql = "CALL accountInfo(?)";
		return DBquery::get($sql, array($this->account_id));		
	}
	
	function getByAdmin() {
		$sql = "SELECT holder_id, user_id, authcode, created, '---' AS limkey FROM holders WHERE account_id=? AND user_id!=?
			UNION ALL
			SELECT holder_id, user_id, authcode, created, limkey FROM holders WHERE account_id=? AND user_id=?";
		
		return DBquery::get($sql, array($this->account_id, Requester::$user_id, $this->account_id, Requester::$user_id));
	}
	
	function getByHolder() {
		$sql = "SELECT 0 AS holder_id, user_id, authcode, created, '---' AS limkey FROM holders WHERE account_id=? AND user_id!=?
			UNION ALL
			SELECT holder_id, user_id, authcode, created, limkey FROM holders WHERE account_id=? AND user_id=?";
		
		return DBquery::get($sql, array($this->account_id, Requester::$user_id, $this->account_id, Requester::$user_id));
	}
}

?>