<?php

class BrandTally extends Base {
	//public $id;
	protected $brand_id;

	function __construct($data='') {
		$this->brand_id = $this->getID();		
		if (!Requester::isMember($this->brand_id)) Error::http(403, "The '/team/$this->brand_id' resource is only viewable by members of brand #$this->brand_id.");
	
		$this->{"@type"} = 'brandTally';
		$this->{"@id"} = "$this->root/team/$this->brand_id/tally";
		$this->table = "members";
		
		$this->init($data);			
		$this->okToFilterBy = array("brand_id", "member_id");
		$this->okToSet = array("joined","revoked");
	}

	function get() {
		$graph = array($this);
		//$this->getRole();
		
		$startDate = "2015-01-01 00:00:00";
		$endDate = "2015-12-31 11:59:59";
		$tally = DBquery::get("CALL tally($this->brand_id, '$startDate', '$endDate')")[0];
		if (!$tally) return array(null);
		foreach($tally AS $k=>$v) $this->$k = $v; //array_merge(array("@type" => "budgetTally"),$tally);

		$this->brand = "$this->root/team/$this->brand_id";		
		return $graph;
	}
}
