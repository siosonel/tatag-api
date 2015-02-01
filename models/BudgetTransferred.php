<?php

require_once "models/Accounts.php";
require_once "models/ForwardVerifier.php";

class BudgetTransferred extends Base {
	protected $verifier;

	function __construct($data='') { 
		$this->table = "records";
		$this->cols = "from_acct,from_user,to_acct,to_user,amount,comment,created,cart_id";
		
		$this->verifier = new ForwardVerifier($data);
		$this->init($data);
		
		$this->okToAdd = array("from_acct", "from_user", "to_acct", "to_user", "amount", "comment");
	}
		
	function add() {	
		$this->addKeyVal('comment', 'NULL', 'ifMissing');	
		
		$this->catchError($this->verifyBals(), $this->verifyAuth());	
		$this->record_id = $this->insert();
		$this->catchError($this->verifyBals()); //void transaction record as needed
		
		//no need to divulge to-endpoint information
		foreach($this AS $key=>$val) {
			if (substr($key,0,3)==='to_') unset($this->$key);
		}
		
		return array($this);
	}	
	
	function catchError($balErr, $authErr='') {		
		$mssg = $balErr . $authErr;
		
		if ($this->record_id AND $mssg) {
			$sql = "UPDATE records SET status=10 WHERE entry_id=$this->record_id";
			$rowCount = DBquery::update($sql);
			if (!$rowCount) Error::http(403, "Affected rows=0.");	
		}
		
		if ($mssg) Error::http(403, $mssg);		
	}
	
	function verifyAuth() {
		$from = $this->verifier->from_holder;
		$to = $this->verifier->to_holder;
		$mssg = "";
		
		if (1*$from['sign'] != 1*$to['sign']) $mssg .= "The from- and to-accounts must have the same sign in a budget transfer (both P-type or both N-type accounts).";
		if ($from['brand_id'] != $to['brand_id']) $mssg .= " Budgets may not be transferred using accounts from different brands.";
		if (strpos($from['acct_auth'],"f")===false) $mssg = "The from-account #$this->from_acct is not authorized to originate budget transfers. ";
		if (strpos($from['holder_auth'],"f")===false) $mssg .= "The from-acct-holder #$this->from_holder->user_id is not authorized to originate budget transfers using account #$this->from_acct.";
		if (strpos($to['acct_auth'],"t")===false) $mssg .= "The to-account #$this->to_acct is not authorized to receive budget transfers.";
		if (strpos($to['holder_auth'],"t")===false) $mssg .= "The to-acct-holder #$this->to_holder->user_id is not authorized to receive budget transfers using account #$this->to_acct. ";
		
		if ($mssg) Error::http(403, $mssg);
	}
	
	function verifyBals() {
		if (1*$this->verifier->from_holder['balance'] < $this->amount) return "Account #$this->from_acct has insufficient balance. ";
	}
}

?>