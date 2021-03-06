﻿<?php

require_once "models/Accounts.php";

class Records extends Base {
	function __construct($data='') { 
		$this->table = "records";
		$this->cols = "from_acct,from_user,to_acct,to_user,amount,comment,created,cart_id";
		$this->holder_id = $this->getID();
		$this->idkey = 'holder_id';
		$this->init($data);
		
		if ($this->from_acct == $this->to_acct) Error::http(403, "The from_acct and to_acct must have a different account id's.");
		
		$this->to_holder = Requester::isAccountHolder($this->toAcct);
		if (!$this->to_holder) Error::http(401, 'Requester is not an account holder for account# $this->toAcct.'); 
		
		$this->okToAdd = array("from_acct", "from_user", "to_acct", "to_user", "amount", "comment", "cart_id");
	}
		
	function add() {		
		$this->addKeyVal('to_user', Requester::$user_id);		
		$this->addKeyVal('comment', 'NULL', 'ifMissing');	
		$this->addKeyVal('cart_id', 'NULL', 'ifMissing');	
		
		$this->verifyOther();		
		list($from, $to) = $this->setRecordType();
		
		$this->catchError($this->verifyBals($from,$to), $this->verifyAuth($from,$to));	
		$this->record_id = $this->insert();
		$this->catchError($this->verifyBals($from,$to)); //void transaction record as needed
		
		//no need to divulge to-endpoint information
		foreach($this AS $key=>$val) {
			if (substr($key,0,3)==='to_') unset($this->$key);
		}
		
		return array($this);
	}
	
	
	function verifyOther() {
		if (strpos($this->from_acct,'-')) {
			list($holder_id,$limkey) = explode("-", $this->from_acct);
	
			$sql = "SELECT user_id, account_id, limkey, authcode FROM holders WHERE holder_id=$holder_id";
			$row = DBquery::get($sql);
			if ($row[0]['limkey'] != $limkey) Error::http(401, 'Invalid limkey.');
			
			$this->addKeyVal('from_acct', $row[0]['account_id']);
			$this->addKeyVal('from_user', $row[0]['user_id']);
			$this->from_holder = $row[0];
		}
		else {
			$this->from_holder = Requester::isAccountHolder($this->from_acct);
			if (!$holder) Error::http(401, 'The to_acct must be held by the Requester or submitted as encoded relay information.');
			
			$this->addKeyVal('from_user', Requester::$user_id);
		}
	}
	
	function setRecordType() {
		$mssg = "";
		
		$sql = DBquery::$conn->prepare("CALL acctAuthBals(?,?)");
		$sql->execute(array($this->from_acct, $this->to_acct));
		$rows = $sql->fetchAll(PDO::FETCH_ASSOC);
		
		if (!$rows) {
			if ($record_id) {/*delete record*/}
			Error::http(403, "Both from and to accounts have insufficient balance.");
		}
		else { //echo "\n".json_encode($rows,JSON_NUMERIC_CHECK) ."\n";				
			if ($rows[0]['account_id']==$this->from_acct) $from = $rows[0];
			else if ($rows[1] AND $rows[1]['account_id']==$this->from_acct) $from = $rows[1];
			
			if ($rows[0]['account_id']==$this->to_acct) $to = $rows[0];
			else if ($rows[1] AND $rows[1]['account_id']==$this->to_acct) $to = $rows[1];
			
			$fromType = $from['sign'] == -1 ? "n" : "p";
			$toType = $to['sign'] == -1 ? "n" : "p";
			$this->recordType =  $fromType . $toType;
		}
		
		return array($from, $to);
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
	
	function verifyAuth($from,$to) {
		$mssg = "";
		
		if ($from['unit']!=$to['unit']) Error::http(403, 'The accounts in a transaction must use the same unit.');
		
		//if account holder authcode is empty, override with account authcode value
		if (!$this->from_holder['authcode']) $this->from_holder['authcode'] = $from['authcode'];
		if (!$this->to_holder['authcode']) $this->to_holder['authcode'] = $to['authcode'];
		
		//check for transaction restrictions within brand		
		if ($from['brand_id'] == $to['brand_id']) {
			if ($this->recordType=='np') {
				if (strpos($from['authcode'],"c")===false) $mssg = "The from-account #$this->from_acct is not authorized for budget creation. ";
				if (strpos($this->from_holder['authcode'],"c")===false) $mssg .= "The from-acct-holder is not authorized to create budgets using account #$this->from_acct.";
				if (strpos($to['authcode'],"c")===false) $mssg .= "The to-account #$this->to_acct is not authorized for budget creation.";
				if (strpos($this->to_holder['authcode'],"c")===false) $mssg .= "The to-acct-holder is not authorized to create budgets using account #$this->to_acct.";
			}
			else if ($this->recordType=='pn') {
				if (strpos($from['authcode'],"i")===false) $mssg = "The from-account #$this->from_acct is not authorized for internal budget use. ";
				if (strpos($this->from_holder['authcode'],"i")===false) $mssg .= "The from-acct-holder is not authorized to use budget internally using account #$this->from_acct. ";				
				if (strpos($to['authcode'],"i")===false) $mssg .= "The to-account #$this->to_acct is not authorized for internal budget use. ";
				if (strpos($this->to_holder['authcode'],"i")===false) $mssg .= "The to-acct-holder is not authorized to use budget internally using account #$this->to_acct. ";
				
				/*if (!$this->cart_id) $mssg .= "Intra-entity budget use requires a cart_id. If you were trying to reverse currency issuance, use a negative amount instead with the revenue budget account as the from account.";
				else {
					$mssg .= $this->verifyCartMatch($to['brand_id']);
					$mssg .= $this->verifyPriceToAmount();
				}*/
			}
			else {
				if (strpos($from['authcode'],"f")===false) $mssg = "The from-account #$this->from_acct is not authorized to originate budget transfers. ";
				if (strpos($this->from_holder['authcode'],"f")===false) $mssg .= "The from-acct-holder #$this->from_holder->user_id is not authorized to originate budget transfers using account #$this->from_acct.";
				if (strpos($to['authcode'],"t")===false) $mssg .= "The to-account #$this->to_acct is not authorized to receive budget transfers.";
				if (strpos($this->to_holder['authcode'],"t")===false) $mssg .= "The to-acct-holder #$this->to_holder->user_id is not authorized to receive budget transfers using account #$this->to_acct. ";
			}
		}
		//the following conditions are inter-brand transaction restrictions 
		else if ($from['sign']==$to['sign']) {
			$mssg = "Budgets may not be assigned between accounts of different brands.";
		}		
		//budgets may only be created using accounts from the same brand  
		else if ($from['sign']==-1) {
			$mssg = "Budgets may not be created using accounts from different brands.";
		}
		//only budget use is allowed between brands
		else if ($this->cart_id) {
			$mssg .= $this->verifyPriceToAmount();
		}
		
		return $mssg;
	}
	
	function verifyCartMatch($toBrandID) { 
		$mssg=""; 
		
		$sql = "SELECT user_id, brand_id, price FROM carts WHERE cart_id=?";
		$row = DBquery::get($sql, array($this->cart_id)); 
		
		if ($row[0]['brand_id']!=$toBrandID) $mssg = "The to-account brand must match the cart brand_id.";
		
		if (!$mssg AND !$row[0]['user_id']) {
			$sql = "UPDATE carts SET user_id=$this->from_user WHERE cart_id=?";
			$mssg = DBquery::update($sql, array($this->cart_id));
			return $mssg;
		}
		else if ($row[0]['user_id'] != $this->from_user) $mssg .= "The cart user must match the from_user id. ";		
		
		$this->cart = $row[0];		
		return $mssg;
	}
	
	function verifyPriceToAmount() {
		$mssg="";
		
		$sql = "SELECT SUM(amount) AS amt FROM records WHERE cart_id=$this->cart_id";
		$rows = DBquery::get($sql);
		$amt = $rows ? $rows[0]['amt'] : 0;
		
		if ($this->amount<0) {
			if (!$rows) $mssg .= "There are no payments to refund.";
			else if ($amt < -1*$this->amount) $mssg .= "The refund or reversed amount exceeds the total payments-to-date ($amt) for cart_id=$this->cart_id.";
		}
		else if ($this->amount + $amt > $this->cart['price']) $mssg .= "The amount being paid, plus previous payments and reversals, exceeds the total price of the cart_id=$this->cart_id.";
		
		return $mssg;
	}
	
	function verifyBals($from, $to) {		
		$mssg="";
		
		//check for balance restrictions
		switch ($this->recordType) {
		case "pn": //budget use, internal or external
			if ($from['balance'] < $this->amount) $mssg = "Account #$this->from_acct has insufficient balance. ";
			if ($to['balance'] < $this->amount) $mssg .= "Account #$this->to_acct has insufficient balance.";	
			break;
			
		case "nn": //revenue budget assignment
		case "pp": //expense budget assignment
			if ($from['balance'] < $this->amount) $mssg = "Account #$this->from_acct has insufficient balance. ";
		}
		
		return $mssg;
	}
}

?>