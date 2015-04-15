<?php

class BrandAbout extends Base {
	function __construct($data='') {		
		$this->brand_id = $this->getID();
		$this->{"@type"} = 'brandAbout';	
		$this->{"@id"} = "/brand/$this->brand_id/about";
		
		$this->table = "brands";		
		$this->idkey = 'brand_id';
		$this->okToGet = array("brand_id", "name", "mission", "description");
		
		$this->init($data);
	}	
	
	function get() {
		$this->getInfo();
		$startDate = "2015-01-01 00:00:00";
		$endDate = "2015-12-31 11:59:59";
		$tally = DBquery::get("CALL tally($this->brand_id, '$startDate', '$endDate')")[0];
		
		if ($tally) {
			$this->tally = array_merge(array("@type" => "budgetTally"),$tally);
			//$this->setForms();	
		}
		
		return array($tally ? $this : null);
	}
		
	
	function getInfo() {
		$sql = "SELECT name, description, mission, created, url, advisor, type_system, type_id, country_code, area_code  FROM brands WHERE brand_id=?";
		$row = DBquery::get($sql, array($this->brand_id));
		if ($row[0]) {
			foreach($row[0] AS $key=>$val) $this->$key = $val;
		}
	}
}

?>