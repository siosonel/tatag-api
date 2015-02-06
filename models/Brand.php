<?php

class Brand extends Base {
	function __construct($data='') {
		$this->brand_id = $this->getID();		
		if (!Requester::isBrandAdmin($this->brand_id)) Error::http(403, "The requester is not an admin for brand #$this->brand_id.");
	
		$this->{"@type"} = 'brand';
		$this->{"@id"} = "/brand/$this->brand_id";
		$this->table = "brands";
		
		$this->init($data);
			
		$this->okToSet = array("ended","mission","description");
		$this->okToFilterBy = array("brand_id");
	}
	
	function add() {
		Error::http(403);
	}
	
	function set() {	
		$this->update(array("brand_id"=>$this->brand_id));
		return array($this->obj);
	}
	
	function get() {
		$this->getInfo();
		$tally = DBquery::get("CALL tally(?)", array($this->brand_id))[0];
		
		if (!$tally) return array(null);
		
		$this->tally = array_merge(array("@type" => "budgetTally"),$tally);
		$this->setForms();	
		
		include_once "models/BrandMembers.php";
		include_once "models/BrandAccounts.php";
		include_once "models/BrandHolders.php";
		$obj = json_decode('{"brand_id":' . $this->brand_id .'}');
		
		return array_merge(
			array($this),
			(new BrandMembers($obj))->get(),
			(new BrandAccounts($obj))->get(),
			(new BrandHolders($obj))->get()
		);
	}	
	
	function getInfo() {
		$sql = "SELECT name, description, mission, created FROM brands WHERE brand_id=?";
		$row = DBquery::get($sql, array($this->brand_id));
		if ($row[0]) {
			foreach($row[0] AS $key=>$val) $this->$key = $val;
		}
	}
	
	/*
	
	
	private function getMembers() {
		$sql = "SELECT members.created, member_id, users.created, users.user_id, email, role, hours
			FROM members JOIN users ON members.user_id=users.user_id
			WHERE brand_id=? AND members.ended IS NULL";
		
		return DBquery::get($sql, array($this->brand_id));
	}*/
}

?>