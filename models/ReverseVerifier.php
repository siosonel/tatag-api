<?php

class ReverseVerifier {
	public $from_holder;
	public $to_holder;

	function __construct($data) { 		
		if (!$data->orig_record_id) Error::http(400, "A reversal request requires the record_id of the original transaction."); 
		if ($data->amount >= 0) Error::http(400, "A reversal request must have a negative amount.");
	
		$this->verifyHolder($data, 'to');
		if ($this->to_holder['user_id'] != Requester::$user_id) Error::http(401, "When reversing a transaction, the to-account's user_id must match the Requester's user_id."); 
		
		$this->verifyHolder($data, 'from');				
		if ($data->from_acct == $data->to_acct) Error::http(403, "The from_acct and to_acct must have a different account id's.");
		
		$this->setTxnType();
		$this->orig_record_id = $data->orig_record_id;
		$this->verifyAdjustedAmt($data->amount);
		
		unset($data->orig_record_id);
	}
	
	function verifyHolder($data, $ft) {			
		list($holder_id,$limkey) = explode("-", $data->$ft); 
	
		$sql = "CALL holderCheck(?)";			
		$row = DBquery::get($sql, array($holder_id));
		
		if (!$row) Error::http(401, 'Invalid holder_id.');
		if ($row[0]['limkey'] != $limkey) Error::http(401, 'Invalid limkey.');
		
	
		$_holder = $ft ."_holder";
		$this->$_holder = $row[0];
		$data->{$ft ."_acct"} = $row[0]['account_id'];
		$data->{$ft ."_user"} = $row[0]['user_id']; 
		
		//delete now-unneeded holder_id from input data
		unset($data->$ft);
		
		//if account holder authcode is wildcard, override with account authcode value
		if ($this->{$_holder}['holder_auth']=='*') $this->{$_holder}['holder_auth'] = $this->{$_holder}['acct_auth'];
	}
	
	function setTxnType() {
		if ($this->from_holder['unit'] != $this->to_holder['unit']) Error::http(403, 'The accounts in a transaction must use the same unit.');
		
		$fromType = $this->from_holder['sign'] == -1 ? "n" : "p";
		$toType = $this->to_holder['sign'] == -1 ? "n" : "p";
		$this->txnType =  $fromType . $toType;
	}
	
	function verifyAdjustedAmt($amount) {		
		$sql = "SELECT adjusted_amt, txntype FROM reversals WHERE orig_record_id=? ORDER BY rev_record_id DESC LIMIT 1";
		$rows = DBquery::get($sql, array($this->orig_record_id));
		
		if ($rows) {
			$remaining_amount = $rows[0]['adjusted_amt'];
			$txntype = $rows[0]['txntype'];			
		}
		else {
			$sql = "SELECT amount, txntype FROM records WHERE record_id=?";
			$rows = DBquery::get($sql, array($this->orig_record_id));
			
			if (!$rows) Error::http(403, "The transaction record to reverse, #$this->orig_record_id, was not found.");
			if ($rows[0]['amount'] < 0) Error::http(403, "Record #$this->orig_record_id is already a transaction reversal and cannot be reversed.");
			$remaining_amount = $rows[0]['amount'];
			$txntype = $rows[0]['txntype'];
		}
		
		$this->adjusted_amt = $remaining_amount + $amount; //reversal amount is expressed as a negative amount
		if ($this->adjusted_amt < 0) Error::http(403, "The reversal amount ($amount) exceeds the previous amount that can be reversed ($remaining_amount.");		
		if ($txntype != $this->txnType) Error::http(403, "The accounts to be used in a reversal must match the account types of those used in the original transaction.");
	}
	
	function trackReversal($rev_record_id) {
		$sql = "INSERT INTO reversals (orig_record_id, rev_record_id, adjusted_amt, txntype, created)
		VALUES ($this->orig_record_id, $rev_record_id, $this->adjusted_amt, '$this->txntype', NOW())";
		
		$rowCount = DBquery::set($sql);
		if (!$rowCount) Error::http(500, "Error: insert query failed.");
		$id = DBquery::$conn->lastInsertId(); //echo " id=$id ";
		
		return $id;
	}
}

?>