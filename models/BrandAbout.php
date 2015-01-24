<?php

class BrandAbout extends Base {
	function __construct($data='') {		
		$this->brand_id = $this->getID();
		$this->{"@type"} = 'brandHolders';	
		
		$this->table = "brands";
		$this->cols = "brand_id,name,mission,description,rating_min,rating_formula,created";
		
		$this->idkey = 'brand_id';
		$this->okToGet = array("brand_id", "name", "mission", "description");
		
		$this->init($data);
	}	
	
	function get() {
		$this->getInfo();
		$tally = DBquery::get("CALL tally(?)", array($this->brand_id))[0];
		
		if ($tally) {
			$this->tally = array_merge(array("@type" => "budgetTally"),$tally);
			$this->setForms();	
		}
		
		return array($tally ? $this : null);
	}
		
	
	function getInfo() {
		$sql = "SELECT name, description, mission, created FROM brands WHERE brand_id=?";
		$row = DBquery::get($sql, array($this->brand_id));
		if ($row[0]) {
			foreach($row[0] AS $key=>$val) $this->$key = $val;
		}
	}
}

?>