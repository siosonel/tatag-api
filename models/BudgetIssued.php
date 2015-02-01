<?php

require_once "models/Accounts.php";
require_once "models/ForwardVerifier.php";

class BudgetIssued extends Base {
	protected $verifier;

	function __construct($data='') { 
		$this->{"@type"} = "budgetIssued";
		$this->brand_id = $this->getID();
		$this->{'@id'} = "/budgets/$this->brand_id/issued";
		$this->table = "records";
		$this->cols = "from_acct,from_user,to_acct,to_user,amount,comment,created,cart_id";
	
		if (Router::$method != 'get') $this->verifier = new ForwardVerifier($data);
		$this->init($data);
		
		$this->okToAdd = array("from_acct", "from_user", "to_acct", "to_user", "amount", "comment");
	}
	
	function get() {
		if (!Requester::isBrandAdmin($this->brand_id)) Error::http(403, "Only admins of brand #$this->brand_id can view details of its budget issuance records.");
	
		$sql = "SELECT r.created, from_acct, from_user, to_acct, to_user, amount, `comment`
		FROM records r JOIN accounts a ON (r.from_acct = a.account_id)
		WHERE brand_id=? LIMIT 50";
		$this->items = DBquery::get($sql, array($this->brand_id));
		$this->setForms();
		return array($this);
	}
		
	function add() {	
		$this->addKeyVal('comment', 'NULL', 'ifMissing');	
		
		$this->catchError("", $this->verifyAuth());	
		$this->record_id = $this->insert();
		
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
		
		if (1*$from['sign']!=-1) $mssg .= "When issuing budgets, the from-account must have a sign of -1 (an N-type account).";
		if (1*$to['sign']!=1) $mssg .= " The to-account must have a sign of 1 (a P-type account).";
		if ($from['brand_id'] != $to['brand_id']) $mssg .= " Budgets may not be created using accounts from different brands.";
		if (strpos($from['acct_auth'],"c")===false) $mssg = " The from-account #$this->from_acct is not authorized for budget creation.";
		if (strpos($from['holder_auth'],"c")===false) $mssg .= " The from-acct-holder is not authorized to create budgets using account #$this->from_acct.";
		if (strpos($to['acct_auth'],"c")===false) $mssg .= " The to-account #$this->to_acct is not authorized for budget creation.";
		if (strpos($to['holder_auth'],"c")===false) $mssg .= " The to-acct-holder is not authorized to create budgets using account #$this->to_acct.";
		
		
		if ($mssg) Error::http(403, $mssg);
	}
}

?>