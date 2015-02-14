<?php

class Team extends Base {
	function __construct($data='') {
		$this->brand_id = $this->getID();		
		if (!Requester::isMember($this->brand_id)) Error::http(403, "The '/team/$this->brand_id' resource is only viewable by members of brand #$this->brand_id.");
	
		$this->{"@type"} = 'team';
		$this->{"@id"} = "/team/$this->brand_id";
		$this->table = "brands";
		
		$this->init($data);			
		$this->okToFilterBy = array("brand_id");
	}
	
	function get() {
		$this->getInfo();
		$tally = DBquery::get("CALL tally(?)", array($this->brand_id))[0];
		
		if (!$tally) return array(null);
		
		$this->tally = array_merge(array("@type" => "budgetTally"),$tally);
		
		include_once "models/TeamMembers.php";
		include_once "models/TeamAccounts.php";
		$obj = json_decode('{"brand_id":' . $this->brand_id .'}');
		
		return array_merge(
			array($this),
			(new TeamMembers($obj))->get(),
			(new TeamAccounts($obj))->get()
		);
	}	
	
	function getInfo() {
		$sql = "SELECT name, description, mission, created FROM brands WHERE brand_id=?";
		$row = DBquery::get($sql, array($this->brand_id));
		if ($row[0]) {
			foreach($row[0] AS $key=>$val) $this->$key = $val;
			
			$this->links = array(
				"teamMembers" => "/team/$this->brand_id/members",
				"teamAccounts" => "/team/$this->brand_id/accounts",				
				"budgetRecords" => "/budget/$this->brand_id/records",
				"teamAbout" => "/brand/$this->brand_id/about"
			);
		}
	}
}

?>