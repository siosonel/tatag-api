<?php

class Team extends Base {
	public $id;

	function __construct($data='') {
		$this->brand_id = $this->getID();		
		if (!Requester::isMember($this->brand_id)) Error::http(403, "The '/team/$this->brand_id' resource is only viewable by members of brand #$this->brand_id.");
	
		$this->{"@type"} = 'brand';
		$this->{"@id"} = "$this->root/team/$this->brand_id";
		$this->table = "members";
		
		$this->init($data);			
		$this->okToFilterBy = array("brand_id", "member_id");
		$this->okToSet = array("joined","revoked");
	}
	
	function set() {
		$this->setFilters($_GET);
		$sql = "SELECT user_id, member_id FROM members WHERE $this->filterCond";
		$rows = DBquery::get($sql, $this->filterValArr);
		
		foreach($rows AS $r) {
			if ($r['user_id'] != Requester::$user_id) 
				Error::http(403, "The requester cannot set another member's information. 
				Please check that requester (#$this->user_id) is filtering by his or her own member_id (#". $r['member_id'] .").");
		}
		
		$this->update();
		return array($this->obj);
	}
	
	function get() {
		//$this->getRole();		
		$this->getInfo();
		
		$startDate = "2015-01-01 00:00:00";
		$endDate = "2015-12-31 11:59:59";
		$tally = DBquery::get("CALL tally($this->brand_id, '$startDate', '$endDate')")[0];
		if (!$tally) return array(null);
		$this->tally = array_merge(array("@type" => "budgetTally"),$tally);
		
		$this->setForms();		
		
		include_once "models/TeamMembers.php";
		include_once "models/TeamAccounts.php";
		include_once "models/TeamThrottles.php";
		include_once "models/BrandPromos.php";
		
		$obj = json_decode('{"brand_id":' . $this->brand_id .'}');
		unset($this->brand_id);
		
		return array(
			$this,
			(new TeamMembers($obj))->get(),
			(new TeamAccounts($obj))->get(),
			(new TeamThrottles($obj))->get(),
			(new BrandPromos($obj))->get()			
		);
	}	
	
	function getInfo() {
		$sql = "SELECT name, logo FROM brands WHERE brand_id=?";
		$row = DBquery::get($sql, array($this->brand_id));
		if ($row[0]) {
			$about = new stdClass();
			foreach($row[0] AS $key=>$val) $about->$key = $val;
			
			$this->id = $this->brand_id;
			$this->name = $row[0]['name'];
			$this->logo = $row[0]['logo'];
			
			$this->members = "$this->root/team/$this->brand_id/members";
			$this->accounts = "$this->root/team/$this->brand_id/accounts";							
			$this->throttles = "$this->root/team/$this->brand_id/throttles";
			$this->records = "$this->root/budget/$this->brand_id/records";	
			$this->about = "$this->root/brand/$this->brand_id/about";
			$this->promos = "$this->root/brand/$this->brand_id/promos";
		}
	}
	
	function getRole() {		
		$sql = "SELECT role, hours, m.created, u.name, m.joined, m.revoked
			FROM members m
			JOIN users u ON u.user_id=m.user_id 
			WHERE brand_id=? AND m.user_id=?
			AND m.ended IS NULL AND m.revoked IS NULL 
			ORDER BY m.created DESC";
		
		$row = DBquery::get($sql, array($this->brand_id, Requester::$user_id));
		
		if ($row) {
			foreach($row AS $r) {
				foreach($r AS $k=>$v) $this->$k = $v; 
			}
		}
		
		return array($this);
	}
}

?>