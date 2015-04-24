<?php
//like a brand admin view but without links to forms, member and accountholding details

class TeamMembers extends Collection {
	function __construct($data='') {
		$this->brand_id = $this->getID();
		if (!Requester::isMember($this->brand_id)) Error::http(403, "The '/team/$this->brand_id/members' resource is only viewable by members of brand #$this->brand_id.");
		
		$this->{"@type"} = 'teamMembers';			
		$this->{"@id"} = "$this->root/team/$this->brand_id/members";
		$this->table = "members";	
		$this->idkey = 'member_id'; 
		
		$this->init($data); 
		$this->okToSet = array("role", 'hours','ended');		 
		$this->okToFilterBy = array("member_id", "user_id");
	}
	
	function get() {		
		$sql = "SELECT member_id, brand_id, m.user_id, role, hours, m.created, u.name, m.joined, m.revoked
			FROM members m
			JOIN users u ON u.user_id=m.user_id 
			WHERE brand_id=? 
			AND m.ended IS NULL AND m.revoked IS NULL AND member_id $this->ltgt $this->limitID
			ORDER BY member_id $this->pageOrder
			LIMIT $this->itemsLimit";
		
		$this->items = DBquery::get($sql, array($this->brand_id));		
		foreach($this->items AS &$r) {
			$r['@id'] = $this->{"@id"} ."?member_id=". $r['member_id'];
		}
		
		$this->paginate('member_id');
		return array($this);
	}
}

?>