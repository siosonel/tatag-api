<?php

class Promo extends Base {
	function __construct($data='') { 		
		$this->promo_id = $this->getID();
		
		$this->{"@type"} = "promo";
		$this->{'@id'} = "$this->root/promo/$this->promo_id";
		$this->table = "promos";
		$this->idkey = 'promo_id';
		
		$this->init($data);
		$this->okToFilterBy = array('promo_id');
	}
	
	function set() {
		$rows = DBquery::get("SELECT brand_id FROM promos WHERE promo_id=?", array($this->promo_id));
		if (!Requester::isMember($rows[0]['brand_id'])) Error::http(
			403, "The user is not a member of the brand that owns this promo and does not have accees to its details."
		);
		
		$this->okToSet = array('name','description', 'amount', 'expires', 'imageURL', 'infoURL');		
		$this->update(array('promo_id'=>$this->promo_id));
		return array($this->obj);
	}
	
	function get() {
		$sql = "SELECT promo_id, brand_id, name, description, amount, imageURL, infoURL, p.created, p.updated, expires,
				relay_id, by_all_limit, by_brand_limit, by_user_limit, by_user_wait
			FROM promos p
			JOIN relays USING (relay_id)
			WHERE promo_id=?";
			
		$rows = DBquery::get($sql, array($this->promo_id));
		if (!$rows) return array(new stdClass());
		
		if (!Requester::isMember($rows[0]['brand_id'])) Error::http(
			403, "The user is not a member of the brand that owns this promo and does not have accees to its details."
		);
		
		
		$this->setForms();
		foreach($rows[0] AS $k=>$v) $this->$k = $v;
		return array($this);
	}
	
	function setDetails($promo_id) {	
		$sql = "SELECT promo_id, brand_id, name, description, amount, qty, imageURL, infoURL, created, updated, expires
		FROM promos 
		WHERE promo_id=? AND ended IS NULL";
		$rows = DBquery::get($sql, array($relay_id));
		if (!$rows) Error::http(403, "Promo id# '$promo_id' is not active.");
		
		foreach($rows[0] AS $k=>$v) $this->$k = $v;
	}
	
	function checkAgainst($secret, $amount) {
		$mssg="";		
		if ($this->qty == 0) $mssg .= "The total usage limit for relay #$relay_id has been exceeded.";
		if ($this->amount != $amount) $mssg .= "The amount must equal $this->amount.";		
		if ($mssg) Error::http(403, $mssg);		
	}
	
	function getDefaultAmount() {
		return $this->amount_min > 0 ? $this->amount_min : $this->amount_max;
	}
	
	function adjustQty() {
		if (!isset($this->qty) OR $this->qty < 1) return;
		$sql = "UPDATE promos SET qty = qty-1 WHERE promo_id=? AND qty>0";
		$mssg = DBquery::set($sql, array($this->relay_id));
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

