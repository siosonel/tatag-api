<?php

class Holders extends Base {
	function __construct($data='') { 
		$this->table = "holders";
		$this->holder_id = $this->getID();
		$this->idkey = 'holder_id';
		$this->okToGet = array('holder_id', 'account_id', 'authcode');
		$this->init($data);
	}
	
	function add() {
		if (Requester::isAccountAdmin($this->account_id)) {
			array_push($this->okToAdd, "user_id", "account_id", "authcode", "limkey");			
		} //print_r($this);
		
		$this->addKeyVal('limkey','abc');
		
		if ($row = $this->getByUserAndAccountId($this->obj->user_id,$this->obj->account_id)) return $row[0]; 
		else $this->holder_id = $this->insert();
		
		return $this;
	}
	
	function set() {
		$info = $this->getInfo()[0];
		
		if (Requester::isAccountAdmin($info['account_id'])) {
			array_push($this->okToSet, "authcode", "ended");	
			array_push($this->okToFilterBy, "brand_id", "account_id", "user_id");
			if (Requester::$user_id != $info['user_id']) unset($info['limkey']);			
		}
		
		if (Requester::$user_id == $info['user_id']) {
			array_push($this->okToSet, "alias");	
			array_push($this->okToFilterBy, "user_id");
		}
		
		$this->update('WHERE holder_id=?', array($this->holder_id));
		foreach($info AS $key=>$val) $this->$key = $val;
		return $this;
	}
	
	function get() {
		$info = $this->getInfo()[0];
		
		if (Requester::$user_id == $info['user_id']) return array($info);
		if (Requester::isAccountAdmin($info['account_id'])) {
			unset($info['limkey']);
			return array($info);
		}		
		return array();
	}
	
	function getByUserAndAccountId($user_id,$account_id) {
		$sql = "SELECT holder_id, user_id, account_id, authcode, created FROM holders WHERE user_id=? AND account_id=?";
		return DBquery::get($sql, array($user_id, $account_id));		
	}
	
	function getInfo() {
		$sql = "SELECT user_id, account_id, authcode, alias, created, limkey FROM holders WHERE holder_id=?";
		return DBquery::get($sql, array($this->holder_id));
	}
}

?>