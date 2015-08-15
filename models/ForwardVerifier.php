<?php

class ForwardVerifier {
	public $from_holder;
	public $to_holder;

	function __construct($data) {
		$this->verifyHolder($data, 'from');
		if ($this->from_holder['user_id'] != Requester::$user_id) Error::http(401, "The from-account's user_id must match the Requester's user_id in a non-reversal transaction."); 
		
		$this->verifyHolder($data, 'to');						
		if ($data->from_acct == $data->to_acct) Error::http(403, "The from_acct and to_acct must have a different account id's.");
		
		$this->setTxnType();
	}	
	
	function verifyHolder($data, $ft) {
		if (strpos($data->$ft,"-")==false OR strpos($data->$ft, ".")) $data->$ft = $this->relayToHolderInfo($data, $ft);  
		list($holder_id, $limkey) = explode("-", $data->$ft);
	
		$sql = "CALL holderCheck(?)";			
		$row = DBquery::get($sql, array($holder_id));		
		if (!$row) Error::http(401, "Invalid $ft-holder_id.");
		if ($row[0]['limkey'] != $limkey) Error::http(401, "Invalid $ft-limkey.");
		
		$_holder = $ft ."_holder";
		$this->$_holder = $row[0];
		$data->{$ft ."_acct"} = $row[0]['account_id'];
		$data->{$ft ."_user"} = $row[0]['user_id']; 
		
		//delete now-unneeded holder_id from input data
		unset($data->$ft);
		
		//if account holder authcode is wildcard, override with account authcode value
		if ($this->{$_holder}['holder_auth']=='*') $this->{$_holder}['holder_auth'] = $this->{$_holder}['acct_auth'];
	}
	
	
	function relayToHolderInfo($data, $ft) {
		list($relay_id,$secret) = explode(".", $data->$ft);
	
		$sql = "SELECT secret, r.holder_id, limkey, txntype, COALESCE(amount_min,0) as amount_min, COALESCE(amount_max,999999999) as amount_max, qty FROM relays r JOIN holders USING (holder_id) WHERE relay_id=? AND r.ended IS NULL";
		$rows = DBquery::get($sql, array($relay_id));
		if (!$rows) Error::http(403, "Relay id# '$relay id' is not active.");
		$r = $rows[0];
		
		if ($r['secret'] AND $r['secret'] != $secret) Error::http(403, "Invalid relay credentials='$relay'. [$secret]");
		
		if (!$data->amount) $data->amount = $r['amount_min'] > 0 ? $r['amount_min'] : $r['amount_max'];  
		if ($r['amount_min'] > $data->amount OR $r['amount_max'] < $data->amount) Error::http(403, "The amount must be between ". $r['amount_min'] ." and ". $r['amount_max'] .".");
		
		if ($r['qty'] == 0) Error::halt(403, "The total usage limit for relay #$relay_id has been exceeded.");
		
		$this->relay_id = $relay_id;		
		$this->relayQty = $r['qty'];
		$this->relayTxnType = $r['txntype'];
		return $r['holder_id'] ."-". $r['limkey'];
	}
	
	function adjustRelayQty() {
		if (!isset($this->relayQty) OR $this->relayQty < 1) return;
		$sql = "UPDATE relays SET qty = qty-1 WHERE relay_id=?";
		$mssg = DBquery::set($sql, array($this->relay_id));
	}
	
	function throttleCheck($amount) {
		if (!$this->to_holder['throttle_id']) return; 
		$from = $this->from_holder;
		
		require_once "models/Throttle.php";
		$Throttle = new Throttle(); 
		$Throttle->get(array(
			'brand_id' =>array($from['brand_id']), 
			'user_id' => $from["user_id"],
			'throttle_id' => $this->to_holder['throttle_id']
		)); 
		
		if ($Throttle->unusedAmt < $amount) 
			return "The transaction amount exceeds the calculated throttle limit of $Throttle->unusedAmt. Please try again at around ". round($Throttle->period/2) ." seconds.";
	}
	
	function setTxnType() {
		if ($this->from_holder['unit'] != $this->to_holder['unit']) Error::http(403, 'The accounts in a transaction must use the same unit.');
		
		$fromType = $this->from_holder['sign'] == -1 ? "n" : "p";
		$toType = $this->to_holder['sign'] == -1 ? "n" : "p";
		$this->txnType =  $fromType . $toType;
		if (isset($this->relayTxnType) AND $this->relayTxnType AND $this->txnType != $this->relayTxnType)
			Error::http(403, "The relay credential is not authorized for the detected transaction type of '$this->txntype'.");
	}	
}

?>