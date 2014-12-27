<?php

class Accounts extends Base {
	function __construct($data='') {
		$this->table = "accounts";
		$this->cols = "account_id,brand_id,name,authcode,unit,balance,sign,created";
		$this->account_id = $this->getID();
		$this->filterKey = 'account_id';
		$this->init($data);
	}
	
	function add() {	
		if (Requester::isBrandAdmin($this->brand_id)) {
			array_push($this->okToAdd, 'brand_id','name','authcode','unit','sign');
		}
		
		$this->account_id = $this->insert();
		return $this;
	}
	
	function set() {
		if (Requester::isBrandAdmin($this->brand_id)) {
			array_push($this->okToAdd, "name","authcode");
			array_push($this->okToFilterBy, "brand_id", "account_id");
		}
		
		$this->update();
	}
	
	function get() {
		//$this->cols; holders
		//by brand, holder, by unit, by ...
		return $this->getBalance();
	}
	
	function getBalance() {
		$sql = "CALL accountInfo(?)";
		return DBquery::get($sql, array($this->account_id));		
	}
}

?>