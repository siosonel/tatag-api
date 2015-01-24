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
		
		$this->cols = 'user_id,email,name,password,created,ended';
		$this->idkey = 'user_id';
		$this->init($data);
		
		$this->okToSet = array("ended","email","name","password");
		$this->okToFilterBy =  array("user_id","email");	
	}
	
	function add($data='') {
		Error::http(403);
	}
	
	function set() {		
		$this->table = "members";
		$markers = array_pad(array(), count($this->member_ids), "?");
		$this->update("WHERE members_id IN ($markers)", $this->member_ids);
		return $this->obj;
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
			
			if ($row['role']=='admin') {
				$row["_adminViews"] = array("/member/".$row['member_id'], "/brand/".$row['brand_id']);
			}
		}
		
		return $this;
	}
}

