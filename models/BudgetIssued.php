<?php
require_once "models/Accounts.php";

class BudgetIssued extends Base {
	protected $verifier;

	function __construct($data='') { 
		$this->{"@type"} = "budgetIssued";
		$this->brand_id = $this->getID('brand_id');
		$this->{'@id'} = "$this->root/budgets/$this->brand_id/issued";
		$this->table = "records";
	
		if (Router::$method != 'get') {
			$verClass =  $data->amount < 0 ? 'ReverseVerifier' : 'ForwardVerifier';
			require_once "models/$verClass.php";
			$this->verifier = new $verClass($data);
		}
		
		$this->init($data);
		
		$this->okToAdd = array("from_acct", "from_user", "to_acct", "to_user", "amount", "note", "txntype");
	}
	
	function get() {
		if (!Requester::isBrandAdmin($this->brand_id)) Error::http(403, "Only admins of brand #$this->brand_id can view details of its budget issuance records.");
	
		$sql = "SELECT r.created, from_acct, from_user, to_acct, to_user, amount, `note`
		FROM records r JOIN accounts a ON (r.from_acct = a.account_id)
		WHERE brand_id=? AND txntype='np' 
		ORDER BY record_id DESC LIMIT 50";
		$this->items = DBquery::get($sql, array($this->brand_id));
		$this->setForms();
		return array($this);
	}
		
	function add() {	
		$this->addKeyVal('note', 'NULL', 'ifMissing');	
		$this->addKeyVal('txntype', $this->verifier->txnType, 'ifMissing');
		
		$this->catchError("", $this->verifyAuth());	
		$this->record_id = $this->insert();
		if ($this->amount < 0) $this->reversal_id = $this->verifier->trackReversal($this->record_id);
		
		//no need to divulge to-endpoint information
		foreach($this AS $key=>$val) {
			if (substr($key,0,3)==='to_') unset($this->$key);
		}
		
		return array($this);
	}	
	
	function catchError($balErr, $authErr='') {		
		$mssg = $balErr . $authErr;
		
		if ($this->record_id AND $mssg) {
			$sql = "UPDATE records SET status=-1 WHERE record_id=$this->record_id";
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
	
	function verifyBals() {
		if ($this->amount < 1 AND 1*$this->verifier->to_holder['balance'] < $this->amount) return "Account #$this->to_acct has insufficient balance. ";
	}
}

?>