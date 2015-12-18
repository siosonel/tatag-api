<?php

class UserBrands extends Base {
	function __construct($data='') {
		$this->{"@type"} = 'userBrands';
		$this->user_id =  Requester::$user_id;	
		
		$this->{"@id"} = "$this->root/user/$this->user_id/brands";
		$this->table = "members";
		$this->idkey = 'user_id';
		$this->init($data);
		
		$this->okToSet = array("joined","revoked");			
		$this->okToFilterBy = array("member_id");
	}
	
	function get() {
	
		$sql = "SELECT brand_id FROM members WHERE user_id=$this->user_id AND ended IS NULL AND role='admin'";
		$items = DBquery::get($sql, array($this->user_id));
		foreach($items AS $i) {
			$this->brand[] = "/brand/$i[brand_id]";
		}
		
		$this->setForms();
		return array($this);
	}
}
