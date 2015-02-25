<?php

class BrandThrottles extends Base {
	function __construct($data='') {
		$this->brand_id = $this->getID();
		
		$this->{"@type"} = 'brandThrottles';
		$this->{"@id"} = "brand/$this->brand_id/throttles";
		$this->table = "throttles";
		
		$this->init($data);
			
		$this->okToAdd = array("brand_id", "period", "by_all", "by_brand", "by_user");
		$this->okToFilterBy = array("throttle_id");
	}
	
	function add() {
		if (!Requester::isBrandAdmin($this->brand_id)) Error::http(403, "Only brand #$this->brand_id admins can add throttling specifications.");
		$this->addKeyVal('brand_id', $this->brand_id);
		
		$this->throttle_id = $this->insert();
		$this->setDetails();
		return array($this);
	}
	
	function get() {
		if (!Requester::isMember($this->brand_id)) Error::http(403, "Only brand #$this->brand_id members have access to this brandThrottles view.");
	
		$sql = "SELECT * FROM $this->table WHERE brand_id=? AND ended IS NULL"; 		
		$this->items = DBquery::get($sql, array($this->brand_id));
		foreach($this->items AS &$t) $t['@id'] = "/throttle/". $t['throttle_id'];
		return array($this);
	}
	
	function setDetails($throttle_id=0) {
		$sql = "SELECT * FROM $this->table WHERE throttle_id=?"; 
		$row = DBquery::get($sql, array($throttle_id ? $throttle_id : $this->throttle_id));
		if (!$row) Error::http(404, "No details were found for throttle #'$this->throttle_id'.");
		
		foreach($row[0] AS $k=>$v) $this->$k = $v;
	}	
}