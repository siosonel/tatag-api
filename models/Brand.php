<?php

class Brand extends Base {	
	function __construct($data='') {
		$this->brand_id = $this->getID();		
		if (!Requester::isBrandAdmin($this->brand_id)) Error::http(403, "The requester is not an admin for brand #$this->brand_id.");
	
		$this->{"@type"} = 'brand';
		$this->{"@id"} = "$this->root/brand/$this->brand_id";
		$this->table = "brands";
		
		$this->init($data);
			
		$this->okToSet = array("name", "ended", "mission", "description", "url", "advisor", "type_system", "type_id", "country_code", "area_code", "logo");
		$this->okToFilterBy = array("brand_id");
	}
	
	function set() {	
		$this->update(array("brand_id"=>$this->brand_id));
		return array($this->obj);
	}
	
	function get() {
		$this->getInfo();
		
		$startDate = "2015-01-01 00:00:00";
		$endDate = "2015-12-31 11:59:59";
		$tally = DBquery::get("CALL tally($this->brand_id, '$startDate', '$endDate')")[0];
		if (!$tally) return array(null);
		$this->tally = array_merge(array("@type" => "budgetTally"),$tally);
		
		
		$this->setForms();	
		
		include_once "models/BrandMembers.php";
		include_once "models/BrandAccounts.php";
		include_once "models/BrandHolders.php";
		include_once "models/BrandPromos.php";
		include_once "models/BudgetThrottles.php";
		$obj = json_decode('{"brand_id":' . $this->brand_id .'}');
		
		return array_merge(
			array($this),
			(new BrandMembers($obj))->get(),
			(new BrandAccounts($obj))->get(),
			(new BrandHolders($obj))->get(),
			(new BrandPromos($obj))->get(),
			(new BudgetThrottles($obj))->get()
		);
	}	
	
	function getInfo() {
		$sql = "SELECT name, description, mission, created, url, advisor, type_system, type_id, country_code, area_code FROM brands WHERE brand_id=?";
		$row = DBquery::get($sql, array($this->brand_id));
		if ($row[0]) {
			foreach($row[0] AS $key=>$val) $this->$key = $val;
			$this->brand_name = $this->name;
			
			$this->brandMembers = "$this->root/brand/$this->brand_id/members";
			$this->brandAccounts = "$this->root/brand/$this->brand_id/accounts";
			$this->brandAbout = "$this->root/brand/$this->brand_id/about";		
			$this->brandPromos = "$this->root/brand/$this->brand_id/promos";	
			$this->budgetRecords = "$this->root/budget/$this->brand_id/records";	
			$this->budgetThrottles = "$this->root/budget/$this->brand_id/throttles";
		}
	}
}

?>