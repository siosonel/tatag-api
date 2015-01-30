<?php
/*
Self access to user's memberships
*/

class UserBrands extends Base {	
	function __construct($data='') {
		$this->{"@type"} = 'userMemberships';
		$this->user_id =  $this->getID();	
		if (!Requester::isUser($this->user_id)) Error::http(401, "The requester must be logged in as the requested user.");
		
		$this->{"@id"} = "/user/$this->user_id/brands";
		$this->table = "members";
		$this->cols = 'user_id,email,name,password,created,ended';
		$this->idkey = 'user_id';
		$this->init($data);
		
		$this->okToSet = array("hours");
		$this->okToFilterBy =  array("member_id");	
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
	
		$sql = "SELECT brand_id, b.name AS brand_name, member_id, m.created AS joined, role, hours 
			FROM members m
			JOIN brands b USING (brand_id)
			WHERE user_id=? AND m.ended IS NULL";
		
		$this->items = DBquery::get($sql, array($this->user_id));
		$this->setForms();
		
		foreach($this->items AS &$row) {
			$row['@id'] = $this->{'@id'} ."?member_id=". $row['member_id']; 
			$row["_brand"] = "/brands/".$row['brand_id']."/about";			
			$this->setForms();
			
			/*if ($row['role']=='admin') {
				$row["_adminViews"] = array("/member/".$row['member_id'], "/brand/".$row['brand_id']);
			}*/
		}
		
		return array($this);
	}
}

