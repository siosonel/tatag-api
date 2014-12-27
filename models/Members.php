<?php

class Members extends Base {
	function __construct($data='') {
		$this->table = "members";
		$this->cols = "member_id,brand_id,user_id,role,hours,created";
		$this->member_id = $this->getID();
		$this->filterKey = 'member_id'; //print_r($this);
		$this->init($data); 
	}
	
	function add() {
		if (Requester::isBrandAdmin($this->brand_id))	{
			$this->okToAdd = array('brand_id','user_id','role','hours');	
		}
		
		if ($this->isMember()) Error::http(409, "User #$this->user_id is already a member of brand #$this->brand_id."); 
		
		$Member = $this->obj;
		$Member->member_id = $this->insert();
		return $this;
	}
	
	function set() {
		if (Requester::isBrandAdmin($this->brand_id)) {
			array_push($this->okToSet, "role");		 
			array_push($this->okToFilterBy, "brand_id","member_id");
		}
		
		if (Requester::isUser()) {
			array_push($this->okToSet, "hours");		 
			array_push($this->okToFilterBy, "member_id");
		}
		
		$this->update();
	}
	
	function get() {
		/*if (Requester::isBrandAdmin($this->brand_id)) $this->getToAdmin();
		else if (Requester::isMember()) $this->getToMember();
		else*/ $this->getToAnon();
	}
	
	function getToAnon() {
		$sql = "SELECT b.brand_id, numMembers 
		FROM brands b
		JOIN (
			SELECT COUNT(*) AS numMembers, brand_id FROM members WHERE brand_id=$this->brand_id 
		) m ON b.brand_id=m.brand_id
		WHERE b.brand_id=$this->brand_id";
		$rows = DBquery::select($sql); exit(json_encode($rows));
		
	}
	
	function isMember($brand_id=0,$user_id=0) {
		if (!$brand_id) {
			if (!$this->brand_id) Error::http(400, "Missing brand_id property for Member->isMember().");
		}
	
		if (!$user_id) {
			if (!$this->user_id) Error::http(400, "Missing user_id argument for Member->isMember().");			
			$user_id = $this->user_id;
		}
		
		$sql = "SELECT member_id FROM members WHERE user_id=$user_id AND brand_id=$this->brand_id AND ended IS NULL";
		$row = DBquery::select($sql);
		return $row[0]['member_id'];
	}
}

?>