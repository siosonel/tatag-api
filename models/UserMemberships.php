<?php
/*
Self access to user's memberships
*/

class UserMemberships extends Base {	
	function __construct($data='') {
		$this->{"@type"} = 'userMemberships';
		$this->user_id =  $this->getID();	
		if (!Requester::isUser($this->user_id)) Error::http(401, "The requester must be logged in as the requested user.");
		
		$this->{"@id"} = "$this->root/user/$this->user_id/memberships";
		$this->table = "members";
		$this->idkey = 'user_id';
		$this->init($data);
		
		$this->okToSet = array("joined","revoked");			
		$this->okToFilterBy = array("member_id");
	}
	
	function add($data='') {
		Error::http(403);
	}
	
	function set() {
		$this->setFilters($_GET);
		$sql = "SELECT user_id, member_id FROM members WHERE $this->filterCond";
		$rows = DBquery::get($sql, $this->filterValArr);
		
		foreach($rows AS $r) {
			if ($r['user_id'] != $this->user_id) 
				Error::http(403, "The requester cannot set another member's information. 
				Please check that requester (#$this->user_id) is filtering by his or her own member_id (#". $r['member_id'] .").");
		}
	
		$this->update();
		return array($this->obj);
	}
	
	function get() {
		$forms = array();
		
		if (!isset($_GET['member_id'])) {
			$graph = array($this);
			$otherCond = "";
			$vals = array($this->user_id);
		}
		else {
			$otherCond = "AND member_id=?";
			$vals = array($this->user_id, $_GET['member_id']);
		}
	
		$sql = "SELECT m.brand_id, member_id AS id, m.joined AS joined, role, hours 
			FROM members m
			JOIN brands b USING (brand_id)
			WHERE user_id=? AND m.ended IS NULL AND revoked IS NULL AND type_system != 'sim' $otherCond";
		
		$items = DBquery::get($sql, $vals);
		$this->setForms();
		$tracked = array();
		
		foreach($items AS &$r) {
			$graph[] = &$r;
			$r["team"] = "$this->root/team/$r[brand_id]";			
			if ($r['role']=='admin') $r["issuer"] = "$this->root/brand/$r[brand_id]";
			unset($r['brand_id']);
			
			$r['@type'] = 'userMembership';
			$r['@id'] = $this->{'@id'} ."?member_id=". $r['id'];
			
			$r['edit'] = "/form/member-edit";
			if (!$r['joined']) $r['accept'] = "/form/member-accept";
			$r['revoke'] = "/form/member-revoke";
			
			if (!isset($_GET['member_id'])) $this->items[] = $r['@id'];
		}
		
		return $graph;
	}
}

