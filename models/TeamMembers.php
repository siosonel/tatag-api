<?php
//like a brand admin view but without links to forms, member and accountholding details

class TeamMembers extends Base {
	function __construct($data='') {
		$this->brand_id = $this->getID();
		if (!Requester::isMember($this->brand_id)) Error::http(403, "The '/team/$this->brand_id/members' resource is only viewable by members of brand #$this->brand_id.");
		
		$this->{"@type"} = 'teamMembers';			
		$this->{"@id"} = "/team/$this->brand_id/members";
		$this->table = "members";	
		$this->idkey = 'member_id'; 
		
		$this->init($data); 
	}
	
	function get() {		
		$sql = "SELECT member_id, brand_id, m.user_id, role, hours, m.created, u.name
			FROM members m
			JOIN users u ON u.user_id=m.user_id 
			WHERE brand_id=? 
			AND m.ended IS NULL
			ORDER BY member_id DESC";
		
		$this->items = DBquery::get($sql, array($this->brand_id));		
		foreach($this->items AS &$r) {
			$r['@id'] = $this->{"@id"} ."?member_id=". $r['member_id'];
		}
		
		return array($this);
	}
}

?>