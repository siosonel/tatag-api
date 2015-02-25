<?php

class Throttle extends Base {
	function __construct($data='') {
		$this->throttle_id = $this->getID();
	
		$this->{"@type"} = 'throttle';
		$this->{"@id"} = "/throttle/$this->throttle_id";
		$this->table = "throttles";
		
		$this->init($data);
			
		$this->okToSet = array("holder_id", "limkey", "period", "by_all", "by_brand", "by_user", "ended");
		$this->okToFilterBy = array("throttle_id");
	}
	
	function set() {	
		$this->setDetails();
		if (!Requester::isBrandAdmin($this->brand_id)) Error::http(403, "The requester is not an admin for brand #$this->brand_id.");
		$this->addKeyVal("limkey", mt_rand(0, 9999999), "ifMissing");
		
		$this->update(array("throttle_id" => $this->throttle_id));
		return array($this->obj);
	}
	
	function get($filters=array()) {
		$this->setDetails($filters['throttle_id']);
		if (!$_GET['brand_id'] AND !$filters) return array($this);
		
		if ($filters) return $this->asApplied($filters);
		else return $this->asApplied($_GET);
	}	
	
	function setDetails($throttle_id=0) {
		$sql = "SELECT * FROM $this->table WHERE throttle_id=?";
		$row = DBquery::get($sql, array($throttle_id ? $throttle_id : $this->throttle_id));
		if (!$row) Error::http(404, "No details were found for throttle #'$this->throttle_id'.");
		
		foreach($row[0] AS $k=>$v) $this->$k = $v;
	}
	
	function asApplied($filters) {
		if (!is_array($filters['brand_id'])) $filters['brand_id'] = explode(",", $filters['brand_id']);
		if (!$filters['brand_id']) 
			Error::http(400, "An array of brand_id values are required when testing the applicability of throttle #$this->throttle_id.");
		
		$currtime = time(); $this->throttle_id=0;		
		$throttle_id = $filters['throttle_id'] ? $filters['throttle_id'] : $this->throttle_id;
		
		$sql = "SELECT a.brand_id, from_user, amount, r.created
			FROM records r
			JOIN accounts a ON a.account_id=from_acct
			WHERE throttle_id=? AND r.status>-1 AND r.txntype='pn' AND $currtime - UNIX_TIMESTAMP(r.created) < $this->period;";
		
		$rows = DBquery::get($sql, array($throttle_id));
		
		$used = array('by_all'=>0, 'by_brand'=>array("0"=>0), 'by_user'=>0);
		foreach($rows AS $r) {		
			$used['by_all'] += $r['amount'];
			if (in_array($r['brand_id'], $filters['brand_id'])) $used['by_brand'][$r['brand_id']] += $r['amount'];
			if (Requester::$user_id==$r['from_user']) $used['by_user'] += $r['amount'];
		}
		
		$this->unusedAmt = min(
			max($this->by_all - $used['by_all'], 0),
			max($this->by_brand - min($used['by_brand']), 0),
			max($this->by_user - $used['by_user'], 0)
		);
		
		unset($used['by_brand']["0"]);
		$this->used = $used;
		return array($this);
	}
}

?>