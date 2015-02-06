<?php

class BrandMembers extends Base {
	function __construct($data='') {
		$this->brand_id = $this->getID();
		if (!Requester::isBrandAdmin($this->brand_id)) Error::http(403, "The requester is not an admin for brand #$this->brand_id.");
		
		$this->{"@type"} = 'brandMembers';			
		$this->{"@id"} = "/brand/$this->brand_id/members";
		$this->table = "members";	
		$this->idkey = 'member_id'; 
		
		$this->init($data); 
		
		$this->okToAdd = array("brand_id",'user_id','role','hours');
		$this->okToSet = array("role", 'hours','ended');		 
		$this->okToFilterBy = array("member_id", "user_id");
	}
	
	function add() {
		if ($this->getMemberId()) Error::http(409, "User #$this->user_id is already a member of brand #$this->brand_id."); 
		
		$this->addKeyVal('brand_id', $this->brand_id);	
		$Member = $this->obj;
		$Member->member_id = $this->insert();
		return array($Member);
	}
	
	function set() {
		if (!$_GET) return $this->add();
		else {
			if ($this->member_id) $this->setDetails();
			
			if ($this->user_id == Requester::$user_id) {			
				if ($this->ended AND $this->user_id==Requester::$user_id) 
					Error::http(403, 'To prevent a brand from not having an admin, an admin cannot deactivate his own membership.');
			}
			
			$this->update($_GET);
			return array($this);
		}
	}
	
	function get() {		
		$sql = "SELECT member_id, brand_id, user_id, role, hours, created FROM members WHERE brand_id=? AND ended IS NULL";
		$this->items = DBquery::get($sql, array($this->brand_id));
		$this->setForms();
		return array($this);
	}
	
	function getMemberId($brand_id=0,$user_id=0) {
		if (!$brand_id) {
			if (!$this->brand_id) Error::http(400, "Missing brand_id property for Member->isMember().");
			$brand_id = $this->brand_id;
		}
	
		if (!$user_id) {
			if (!$this->user_id) Error::http(400, "Missing user_id argument for Member->isMember().");			
			$user_id = $this->user_id;
		}
		
		$sql = "SELECT member_id FROM members WHERE user_id=$user_id AND brand_id=$brand_id AND ended IS NULL";
		$row = DBquery::get($sql);
		return $row[0]['member_id'];
	}
	
	function setDetails() {		
		$sql = "SELECT brand_id, user_id, role, hours, created FROM members WHERE member_id=? AND ended IS NULL";
		$row = DBquery::get($sql, array($this->member_id));
		
		if ($row) { 
			foreach($row[0] AS $key=>$val) $this->$key = $val;
		}
		
		return;
	}
}

?>