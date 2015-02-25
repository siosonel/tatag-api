<?php

class ForwardVerifier {
	public $from_holder;
	public $to_holder;

	function __construct($data) { 		
		$this->verifyHolder($data, 'from');
		if ($this->from_holder['user_id'] != Requester::$user_id) Error::http(401, "The from-account's user_id must match the Requester's user_id in a non-reversal transaction."); 
		
		$this->verifyHolder($data, 'to');						
		if ($data->from_acct == $data->to_acct) Error::http(403, "The from_acct and to_acct must have a different account id's.");
	}
	
	function verifyHolder($data, $ft) {
		list($holder_id, $limkey, $this->throttle_id) = explode("-", $data->$ft);
	
		$sql = "CALL holderCheck(?)";			
		$row = DBquery::get($sql, array($holder_id));		
		if (!$row) Error::http(401, "Invalid $ft-holder_id.");
	
		$_holder = $ft ."_holder";
		$this->$_holder = $row[0];
		$data->{$ft ."_acct"} = $row[0]['account_id'];
		$data->{$ft ."_user"} = $row[0]['user_id']; 
		
		//delete now-unneeded holder_id from input data
		unset($data->$ft);
		
		//if account holder authcode is wildcard, override with account authcode value
		if ($this->{$_holder}['holder_auth']=='*') $this->{$_holder}['holder_auth'] = $this->{$_holder}['acct_auth'];
		
		if ($ft=='to') $this->setTxnType();
		
		if ($ft=='to' AND $this->txnType=='pn' AND $this->throttle_id) $this->verifyThrottle($holder_id,$limkey,$data);  
		else if ($row[0]['limkey'] != $limkey) Error::http(401, "Invalid $ft-limkey.");
	}
	
	function verifyThrottle($holder_id,$limkey,$data) {
		$from = $this->from_holder;
		
		require_once "models/Throttle.php";
		$Throttle = new Throttle(); 
		$Throttle->get(array(
			'brand_id' =>array($from['brand_id']), 
			'user_id' => $from["user_id"],
			'throttle_id' => $this->throttle_id
		));
		
		if ($Throttle->holder_id != $holder_id) $mssg = "The to-holder_id does not match the one for throttle_id #$this->throttle_id.";
		if ($Throttle->limkey != $limkey) $mssg = "The to-limkey does not match the one specified for throttle_id #$this->throttle_id.";
		if ($mssg) Error::http(403, $mssg);
		if ($Throttle->unusedAmt < $data->amount) 
			Error::http(409, "The transaction amount exceeds the calculated throttle limit of $Throttle->unusedAmt. Please try again at around ". round($Throttle->period/2) ." seconds.");
	}
	
	function setTxnType() {
		if ($this->from_holder['unit'] != $this->to_holder['unit']) Error::http(403, 'The accounts in a transaction must use the same unit.');
		
		$fromType = $this->from_holder['sign'] == -1 ? "n" : "p";
		$toType = $this->to_holder['sign'] == -1 ? "n" : "p";
		$this->txnType =  $fromType . $toType;
	}	
}

?>