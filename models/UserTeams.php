<?php

class UserTeams extends Base {
	function __construct($data='') {
		$this->{"@type"} = 'userTeams';
		$this->user_id =  Requester::$user_id;	
		
		$this->{"@id"} = "$this->root/user/$this->user_id/teams";
		$this->table = "members";
		$this->idkey = 'user_id';
		$this->init($data);
		
		$this->okToSet = array("joined","revoked");			
		$this->okToFilterBy = array("member_id");
	}
	
	function get() {
		$sql = "SELECT brand_id FROM members WHERE user_id=$this->user_id AND ended IS NULL";
		$items = DBquery::get($sql, array($this->user_id));
		foreach($items AS $i) {
			$this->brand[] = "$this->root/team/$i[brand_id]";
		}
		
		return array($this);
	}
}
