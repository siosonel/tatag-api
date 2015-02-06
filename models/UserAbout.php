<?php
/*
Public access to user information
*/

class UserAbout extends Base {	
	function __construct($data='') {
		$id =  $this->getID();	
		$this->{"@id"} = "/users/$id";
		$this->table = 'users';
		$this->user_id = $id;
		$this->idkey = 'user_id';
		$this->init($data);
	}
	
	function add($data='') {
		Error::http(403);
	}
	
	function set() {
		Error::http(403);
	}
	
	function get() { //limited public profile information
		$sql = "SELECT m.user_id, name, m.created, COUNT(DISTINCT(brand_id)) AS numMemberships, SUM(hours) AS totalHours
		FROM members m
		JOIN users ON m.user_id=users.user_id
		WHERE m.user_id IN (?) AND m.ended IS NULL
		GROUP BY m.user_id";
		
		return DBquery::get($sql, array($this->user_id));	
	}
}

