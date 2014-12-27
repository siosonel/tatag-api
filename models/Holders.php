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
		
		$this->holder_id = $this->insert();
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
	
	function get() {
	
	}
}

?>