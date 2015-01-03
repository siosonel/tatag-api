<?php

class Holders extends Base {
	function __construct($data='') { 
		$this->table = "holders";
		$this->cols = "holder_id,user_id,account_id,authcode,created";
		$this->holder_id = $this->getID();
		$this->filterKey = 'holder_id';
		$this->init($data);
	}
	
	function add() {
		if (Requester::isAccountAdmin($this->account_id)) {
			array_push($this->okToAdd, "user_id", "account_id", "authcode");			
		} //print_r($this);
		
		if ($row = $this->get($this->obj->user_id,$this->obj->account_id)) return $row[0]; 
		else $this->holder_id = $this->insert();
		
		return $this;
	}
	
	function set() {
		if (Requester::isAccountAdmin($this->account_id)) {
			array_push($this->okToSet, "authcode");	
			array_push($this->okToFilterBy, "brand_id", "account_id", "user_id");
		}
		
		if (Requester::isHolder($this->account_id)) {
			array_push($this->okToSet, "alias", "parent");	
			array_push($this->okToFilterBy, "user_id");
		}
		
		$this->update();
	}
	
	function get($user_id,$account_id) {
		$sql = "SELECT holder_id, user_id, account_id, authcode, created FROM holders WHERE user_id=? AND account_id=?";
		return DBquery::get($sql, array($user_id, $account_id));		
	}
}

?>