<?php

class Brand extends Base {
	function __construct($data='') {
		$this->brand_id = $this->getID();		
		if (!Requester::isBrandAdmin($this->brand_id)) Error::http(403, "The requester is not an admin for brand #$this->brand_id.");
	
		$this->{"@type"} = 'brand';
		$this->{"@id"} = "/brand/$this->brand_id";
		$this->table = "brands";
		
		$this->init($data);
			
		$this->okToSet = array("name", "ended","mission","description");
		$this->okToFilterBy = array("brand_id");
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
		include_once "models/BrandThrottles.php";
		$obj = json_decode('{"brand_id":' . $this->brand_id .'}');
		
		return array_merge(
			array($this),
			(new BrandMembers($obj))->get(),
			(new BrandAccounts($obj))->get(),
			(new BrandHolders($obj))->get(),
			(new BrandThrottles($obj))->get()
		);
	}	
	
	function getInfo() {
		$sql = "SELECT name, description, mission, created FROM brands WHERE brand_id=?";
		$row = DBquery::get($sql, array($this->brand_id));
		if ($row[0]) {
			foreach($row[0] AS $key=>$val) $this->$key = $val;
			
			$this->links = array(
				"brandMembers" => "/brand/$this->brand_id/members",
				"brandAccounts" => "/brand/$this->brand_id/accounts",				
				"budgetRecords" => "/budget/$this->brand_id/records",
				"brandAbout" => "/brand/$this->brand_id/about",
				"brandThrottles" => "/brand/$this->brand_id/throttles"
			);
		}
	}
}

?>