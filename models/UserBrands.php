<?php
/*
Self access to user's memberships
*/

class UserBrands extends Base {	
	function __construct($data='') {
		$this->{"@type"} = 'userMemberships';
		$this->user_id =  $this->getID();	
		if (!Requester::isUser($this->user_id)) Error::http(401, "The requester must be logged in as the requested user.");
		
		$this->{"@id"} = "$this->root/user/$this->user_id/brands";
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
		$graph = array($this);
	
		$sql = "SELECT brand_id, b.name AS brand_name, member_id, m.created AS joined, role, hours 
			FROM members m
			JOIN brands b USING (brand_id)
			WHERE user_id=? AND m.ended IS NULL AND revoked IS NULL AND type_system != 'sim'";
		
		$items = DBquery::get($sql, array($this->user_id));
		$this->setForms();
		$tracked = array();
		
		foreach($items AS &$r) {
			$r['@type'] = 'userMembership';
			$r['@id'] = $this->{'@id'} ."?member_id=". $r['member_id'];
			$r["team"] = "$this->root/team/".$r['brand_id'];			
			if ($r['role']=='admin') $r["brand"] = "$this->root/brand/".$r['brand_id'];			
			
			$brand = $this->transferProps($r, array("brand_id"), array("name"=>"brand_name"));
			if (!in_array($r['brand'], $tracked)) {
				$brand['@type'] = 'brand';
				$brand['@id'] = $r['brand'];
				
				$graph[] = $brand;
				$tracked[] = $r['brand'];
			}
			
			$graph[] = $r;			
			$this->items[] = $r['@id'];
		}
		
		return $graph;
	}
}

