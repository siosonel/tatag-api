<?php

class Relay extends Base {
	public $id;

	function __construct($data='') { 		
		$this->relay_id = $this->getID();
		
		if (Router::$resource=='relay' AND !Requester::isRelayHolder($this->relay_id)) Error::http(
			403, "The user does not have access to this accountholder's information."
		);
		
		$this->{"@type"} = "relay";
		$this->{'@id'} = "$this->root/relay/$this->relay_id";
		$this->table = "relays";
		$this->idkey = 'relay_id';
		
		$this->init($data);
		$this->okToFilterBy = array('relay_id');
	}
	
	function set() {
		$this->okToSet = array(
			'secret','amount_min','amount_max','tag','redirect','ended','txntype',
			'by_all_limit', 'by_brand_limit', 'by_user_limit', 'by_user_wait'
		);
		
		$this->addKeyVal('secret','NULL','ifMissing');		
		$this->addKeyVal('redirect','NULL','ifMissing');			
		$this->addKeyVal('tag','NULL','ifMissing');			
		$this->addKeyVal('txntype','pn','ifMissing');
		
		$_GET['holder_id'] = $this->holder_id;
		$this->update(array('relay_id'=>$this->relay_id));
		return array($this->obj);
	}
	
	function get() {
		$sql = "SELECT relay_id AS id, r.holder_id, user_id, account_id, amount_min, amount_max, redirect, secret, tag, txntype, r.created, r.updated, r.ended, 
		by_all_limit, by_brand_limit, by_user_limit, by_user_wait
			FROM relays r
			JOIN holders h ON h.holder_id=r.holder_id
			WHERE relay_id=?";
			
		$rows = DBquery::get($sql, array($this->relay_id));
		if (!$rows) return array(new stdClass());
		if (1*$rows[0]['user_id'] != Requester::$user_id) Error::http(
			403, "The user is not the accountholder of this relay and does not have accees to its details."
		);
		
		
		$this->setForms();
		foreach($rows[0] AS $k=>$v) $this->$k = $v;
		unset($this->relay_id);
		return array($this);
	}
	
	function setDetails($relay_id) {	
		$sql = "SELECT relay_id, secret, r.holder_id AS holder_id, limkey, txntype, COALESCE(amount_min,0) as amount_min, COALESCE(amount_max,999999999) as amount_max, 
		by_all_limit, by_brand_limit, by_user_limit, by_user_wait
		FROM relays r 
		JOIN holders USING (holder_id) 
		WHERE relay_id=? AND r.ended IS NULL";
		$rows = DBquery::get($sql, array($relay_id));
		if (!$rows) Error::http(403, "The associated recipient token is not active. (resolved to relay id='$relay_id'");
		
		foreach($rows[0] AS $k=>$v) $this->$k = $v;
	}
	
	function checkAgainst($secret, $amount) {
		$mssg="";
		
		if ($this->secret AND $this->secret != $secret) $mssg .= "Invalid relay credentials.";		
		if ($this->amount_min > $amount OR $this->amount_max < $amount) $mssg .= "The amount must be between $this->amount_min and $this->amount_max.";		
		
		if ($mssg) Error::http(403, $mssg);		
	}
	
	function getDefaultAmount() {
		return $this->amount_min > 0 ? $this->amount_min : $this->amount_max;
	}
	
	function checkLimits($brand_id, $user_id) {
		$currTime = time();		
		$cutoff = $currTime - 604800; //last 7 days
		$mssg = "";
		
		foreach(array('user','brand','all') AS $type) {
			$limitNum = $this->{"by_". $type ."_limit"};
			
			if ($type=='brand') {
				$extraJoin = "JOIN accounts a ON records.from_acct = a.account_id";
				$extraCond = "AND brand_id=$brand_id";
			}
			else if ($type=='user') {
				$extraJoin = "";
				$extraCond = "AND from_user=$user_id";
			}
			else {
				$extraJoin = "";
				$extraCond = "";
			}
		
			$sql = "SELECT COALESCE(COUNT(*),0) as total, UNIX_TIMESTAMP(MAX(records.created)) AS lastUsed
				FROM records $extraJoin
				WHERE relay_id=$this->relay_id $extraCond AND UNIX_TIMESTAMP(records.created) > $cutoff";
			
			$total = DBquery::get($sql)[0]['total'];
			if ($total >= $limitNum) Error::http(403, "The $type relay usage limit of $limitNum within the last seven days has been reached or exceeded (counted $total uses).");
			
			if ($type=='user') { 
				$lastUsed = DBquery::get($sql)[0]['lastUsed'];
				$waitElapsed = $currTime - $lastUsed;
				$waitMin = 3600*$this->{"by_". $type ."_wait"};
				
				if ($waitElapsed < $waitMin) Error::http(403, "The user must wait another ". ceil(($waitMin - $waitElapsed)/3600) ." hours before reusing relay #$this->relay_id.");
			}
		}
	}
}

