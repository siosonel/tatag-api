<?php

class Members extends Base {
	function __construct($data='') {
		$this->table = "members";
		$this->member_id = $this->getID();
		$this->idkey = 'member_id'; 
		if (isset($data->ended) AND $data->ended + 300 > time()) $data->ended = date("Y-m-d H:i:s", $data->ended);
		
		$this->okToGet = array("brand_id", "member_id", "created AS joined", "role", "hours");		
		$this->init($data); 
	}
	
	function add() {
		if (Requester::isBrandAdmin($this->brand_id))	{
			$this->okToAdd = array('brand_id','user_id','role','hours');	
		}
		
		if ($this->getMemberId()) Error::http(409, "User #$this->user_id is already a member of brand #$this->brand_id."); 
		
		$Member = $this->obj;
		$Member->member_id = $this->insert();
		return $Member;
	}
	
	function set() {
		if ($this->member_id) $this->setDetails();
	
		if (Requester::isBrandAdmin($this->brand_id)) {
			array_push($this->okToSet, "role", 'hours','ended');		 
			array_push($this->okToFilterBy, "brand_id","member_id");
		}
		
		if ($this->user_id == Requester::$user_id) {
			array_push($this->okToSet, "hours", 'ended');		 
			array_push($this->okToFilterBy, "member_id");
			
			if ($this->ended AND $this->user_id==Requester::$user_id) 
				Error::http(403, 'To prevent a brand from not having an admin, an admin cannot deactivate his own membership.');
		}
		
		$this->update("WHERE member_id=?", array($this->member_id));
		return $this;
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
		return DBquery::get($sql);		
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
		if ($row) return $row[0]['member_id'];
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