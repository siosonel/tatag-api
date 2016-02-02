<?php

class AppDetails extends Base {
	function __construct() {
		$this->consumer_id = $this->getID();
		$this->{"@id"} = "$this->root/app/$this->consumer_id/details";
		$this->{"@type"} = "appDetails";
	}

	function get() {
		$sql = "SELECT * FROM consumers WHERE consumer_id=?";
		$rows = DBquery::get($sql, array($this->consumer_id));
		
		if (!$rows) return array(null);
		if ($rows[0]['user_id']!=Requester::$user_id) Error::http(403, "The details for app #$this->consumer_id is viewable to its developer only.");

		foreach($rows[0] AS $k=>$v) $this->$k = $v;
		
		if ($rows[0]['type']=='advisor') {
			$this->advise = "$this->root/app/$this->consumer_id/advise";
			$this->config = "$this->root/app/$this->consumer_id/config";
		}
			
		return array($this);
	}
}

