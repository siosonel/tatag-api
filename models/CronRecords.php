<?php

class CronRecords extends Collection {
	private $weekNum=0;
	private $numBrands=0;
	private $rating=array();
	private $minNumRaters = 2;
	private $nominalRating = 95;
	
	function __construct() {
		$this->weekNum = date('W');
	}
	
	function get() {
		if ($_SERVER['REMOTE_ADDR']!=$_SERVER['SERVER_ADDR'] AND $_SERVER['REMOTE_ADDR']!='127.0.0.1') Error::http(403, "Requests for the sim/records resource must originate from the server environment.");
	
		$sql = "SELECT record_id FROM records WHERE status=0 AND TIMESTAMPDIFF(SECOND,created,NOW()) > 300";
		$rows = DBquery::get($sql);
		
		$pending = count($rows); 		
		foreach($rows AS $r) {
			DBquery::get("CALL approveRecord(". $r['record_id'] .")");
		}

		return array(array("numApproved"=>$pending));
	}
}

