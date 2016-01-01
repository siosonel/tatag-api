<?php

class BrandAbout extends Base {
	public $url;

	function __construct($data='') {		
		$this->brand_id = $this->getID();
		$this->{"@type"} = 'brandAbout';	
		$this->{"@id"} = "$this->root/brand/$this->brand_id/about";
		
		$this->table = "brands";		
		$this->idkey = 'brand_id';
		$this->okToGet = array("brand_id", "name", "mission", "description");
		
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
			foreach($row[0] AS $key=>$val) {
				if ($key=='url' AND !$val) $val = "http://tatag.cc/$this->brand_id";
				if ($key=='country_code' AND !$val) $val = "USA";
				$this->$key = $val;
			}
		}
	}
	
	function getMetaVals() {
		$area_codes = json_decode(file_get_contents("ref/area_codes/$this->country_code.json"));			
		foreach($area_codes AS $loc=>$num) {
			if ($num==$this->area_code) {$this->area_name = $loc; break;}
		}
		
		$countries = json_decode(file_get_contents("ref/countries.json"));
		foreach($countries AS $arr) {
			if ($arr[3]==$this->country_code) {$this->country_name = $arr[0]; break;}
		}
		
		$types = json_decode(file_get_contents("ref/brand_classification.json"));
		foreach($types->{$this->type_system}->types AS $obj) {
			if ($obj->id == $this->type_id) {$this->type = $obj->type; break;}
		}
	}
}

?>