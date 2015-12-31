<?php

include_once "models/BrandCollection.php";

class UserBrands extends BrandCollection {
	function __construct($data='') {
		$this->{"@type"} = 'userBrands';
		$this->user_id =  Requester::$user_id;	
		
		$this->{"@id"} = "$this->root/user/$this->user_id/brands";
		$this->table = "brands";
		$this->idkey = 'brand_id';
		$this->init($data);
	}
	
	function set() {
		return $this->add();
	}
	
	function get() {
	
		$sql = "SELECT brand_id FROM members WHERE user_id=$this->user_id AND ended IS NULL AND role='admin'";
		$items = DBquery::get($sql, array($this->user_id));
		foreach($items AS $i) {
			$this->brand[] = "/brand/$i[brand_id]";
		}
		
		$this->setForms();
		$this->add = "$this->root/form/brand-registration";
		return array($this);
	}
}
