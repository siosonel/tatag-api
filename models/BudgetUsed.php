<?php
require_once "models/Accounts.php";

class BudgetUsed extends Base {
	protected $verifier;

	function __construct($data='') { 
		$this->{"@type"} = "budgetUsed";
		$this->brand_id = $this->getID('brand_id');
		$this->{'@id'} = "$this->root/budgets/$this->brand_id/used";
		$this->table = "records";
		
		if (Router::$method != 'get') {
			$verClass =  $data->amount < 0 ? 'ReverseVerifier' : 'ForwardVerifier';
			require_once "models/$verClass.php";
			$this->verifier = new $verClass($data);
		}
		
		$this->init($data);
		
		$this->okToAdd = array("from_acct", "from_user", "to_acct", "to_user", "amount", "note", "txntype", "throttle_id", "relay_id", "promo_id");
	}
	
	function get() {
		if (!Requester::isBrandAdmin($this->brand_id)) Error::http(403, "Only admins of brand #$this->brand_id can view details of its budget issuance records.");
	
		$sql = "SELECT r.record_id, r.created, from_acct, from_user, to_acct, to_user, amount, `note`
		FROM records r JOIN accounts a ON (r.from_acct = a.account_id)
		WHERE brand_id=? AND txntype='pn' 
		ORDER BY record_id DESC LIMIT 50";
		$this->items = DBquery::get($sql, array($this->brand_id));
		$this->setForms();
		return array($this);
	}
	
	function add() {
		$this->addKeyVal('note', 'NULL', 'ifMissing');	
		$this->addKeyVal('txntype', $this->verifier->txnType, 'ifMissing');
		$this->addKeyVal('throttle_id', $this->verifier->to_holder['throttle_id']);
		$this->addKeyVal('relay_id', isset($this->verifier->Relay) ? $this->verifier->Relay->relay_id : 0);
		$this->addKeyVal('promo_id', isset($this->verifier->promo_id) ? $this->verifier->promo_id : 0);
		
		$this->catchError($this->verifyBals(), $this->verifyAuth());	
		$this->record_id = $this->insert();
		$this->catchError($this->verifyBals()); //void transaction record as needed
		
		if ($this->amount < 0) $this->reversal_id = $this->verifier->trackReversal($this->record_id);
		//unlike instant approval of budget issuance or transfer when from_user==to_user, 
		//do not instantly approve an inter-entity budget use, do not skip advisor input
		
		
		//no need to divulge to-endpoint information
		foreach($this AS $key=>$val) {
			if (substr($key,0,3)==='to_') unset($this->$key);
		}

		$this->advise = $this->getAdvise();
		$this->verifier->notifyRecipient(
			$this->obj->to_user, "budget use $this->amount", "[". json_encode($this->advise) ."]. You don't have to do anything."
		);
		
		return array($this);
	}	
	
	function catchError($balErr, $authErr='') {		
		$mssg = $balErr . $authErr; 
		
		if ($this->record_id AND method_exists($this->verifier, 'throttleCheck')) $mssg .= $this->verifier->throttleCheck($this->amount);
		
		if ($this->record_id AND $mssg) {
			$sql = "UPDATE records SET status=-1 WHERE record_id=$this->record_id";
			$rowCount = DBquery::set($sql);
			if (!$rowCount) Error::http(403, "Affected rows=0.");	
		}
		
		if ($mssg) Error::http(403, $mssg);		
	}
	
	function verifyAuth() {
		$from = $this->verifier->from_holder;
		$to = $this->verifier->to_holder;
		$mssg = "";
		
		if (1*$from['sign'] != 1) $mssg .= "When using budgets, the from-account must have a sign of 1 (a P-type account).";
		if (1*$to['sign'] != -1) $mssg .= "When using budgets, the to-account must have a sign of -1 (an N-type account).";
		
		// external budget use
		if ($from['brand_id'] != $to['brand_id']) {	
			if (strpos($from['acct_auth'],"x")===false) $mssg .= "The from-account #$this->from_acct is not authorized for external budget use. ";
			if (strpos($from['holder_auth'],"x")===false) $mssg .= "The from-acct-holder #$this->from_holder->user_id is not authorized to use budget externally using account #$this->from_acct.";
			if (strpos($to['acct_auth'],"x")===false) $mssg .= "The to-account #$this->to_acct is not authorized for external budget use. ";
			if (strpos($to['holder_auth'],"x")===false) $mssg .= "The to-acct-holder #$this->to_holder->user_id is not authorized to use budget externally using account #$this->to_acct. ";
		}
		else { //internal budget use
			if (strpos($from['acct_auth'],"i")===false) $mssg = "The from-account #$this->from_acct is not authorized for internal budget use. ";
			if (strpos($from['holder_auth'],"i")===false) $mssg .= "The from-acct-holder is not authorized to use budget internally using account #$this->from_acct. ";				
			if (strpos($to['acct_auth'],"i")===false) $mssg .= "The to-account #$this->to_acct is not authorized for internal budget use. ";
			if (strpos($to['holder_auth'],"i")===false) $mssg .= "The to-acct-holder is not authorized to use budget internally using account #$this->to_acct. ";
		}
		
		if ($mssg) Error::http(403, $mssg);
	}
	
	function verifyBals() {
		if (1*$this->verifier->from_holder['balance'] < $this->amount) return "Account #$this->from_acct has insufficient balance. ";
	}

	function getAdvise() {
		$info = new stdClass();
		$info->from_brand = $this->verifier->from_holder['brand_id'];
		$info->to_brand = $this->verifier->to_holder['brand_id'];

		if ($info->from_brand==$info->to_brand) return 7;

		include_once "models/AppAdvise.php";
		$Advisor = new AppAdvise($info);
		return $Advisor->get();
	}
}

