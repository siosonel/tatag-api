<?php

class ForwardVerifier {
	public $from_holder;
	public $to_holder;

	function __construct($data) { 		
		$this->verifyHolder($data, 'from');
		if ($this->from_holder['user_id'] != Requester::$user_id) Error::http(401, "The from-account's user_id must match the Requester's user_id in a non-reversal transaction."); 
		
		$this->verifyHolder($data, 'to');				
		if ($data->from_acct == $data->to_acct) Error::http(403, "The from_acct and to_acct must have a different account id's.");
		
		$this->setRecordType();
	}
	
	function verifyHolder($data, $ft) {
		$_holder = $ft ."_holder";
		$_acct = $ft ."_acct";
		$_user = $ft ."_user";		
		
		list($holder_id,$limkey) = explode("-", $data->$_holder); 
	
		$sql = "CALL holderCheck(?)";			
		$row = DBquery::get($sql, array($holder_id));
		
		if (!$row) Error::http(401, 'Invalid holder_id.');
		if ($row[0]['limkey'] != $limkey) Error::http(401, 'Invalid limkey.');
		
		$this->$_holder = $row[0];
		$data->$_acct = $row[0]['account_id'];
		$data->$_user = $row[0]['user_id']; 
		
		//delete now-unneeded holder_id from input data
		unset($data->$_holder);
		
		//if account holder authcode is wildcard, override with account authcode value
		if ($this->{$_holder}['holder_auth']=='*') $this->{$_holder}['holder_auth'] = $this->{$_holder}['acct_auth'];
	}
	
	function setRecordType() {
		if ($this->from_holder['unit'] != $this->to_holder['unit']) Error::http(403, 'The accounts in a transaction must use the same unit.');
		
		$fromType = $this->from_holder['sign'] == -1 ? "n" : "p";
		$toType = $this->to_holder['sign'] == -1 ? "n" : "p";
		$this->recordType =  $fromType . $toType;
	}	
}

?>