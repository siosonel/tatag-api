<?php
/*
Holder access to user's accounts
*/

class UserAccounts extends Base {	
	function __construct($data='') { 
		$this->{"@type"} = 'userAccounts'; //print_r($data);
		$this->user_id =  Router::$id ? $this->getID() : Requester::$user_id; //print_r($this);
		if (!Requester::isUser($this->user_id)) Error::http(401, "The requester must be logged in as the requested user.");
		
		$this->{"@id"} = "$this->root/user/$this->user_id/accounts";
		$this->init($data);
		
		$this->okToSet = array("alias", "limkey");
		$this->okToFilterBy =  array("user_id", "holder_id");	
	}
	
	function add($data='') {
		Error::http(403);
	}
	
	function set() {
		$this->table = "holders";
		$this->setFilters($_GET);
		$sql = "SELECT user_id, holder_id FROM $this->table WHERE $this->filterCond";
		$rows = DBquery::get($sql, $this->filterValArr);
		
		foreach($rows AS $r) {
			if ($r['user_id'] != $this->user_id) 
				Error::http(403, "The requester cannot set another holder's information. 
				Please check that requester (#$this->user_id) is filtering by his or her own holder_id (#". $r['holder_id'] .").");
		}
		
		$this->update();
		return array($this->obj);
	}
	
	function get() {
		$forms = array();
		
		$sql = "CALL userAccounts(?)"; 			
		$this->items = DBquery::get($sql, array($this->user_id));
		$this->setForms();
		
		foreach($this->items AS &$row) {	
			$row['@type'] = 'userAccount';
			$row['@id'] = $this->{'@id'} ."?holder_id=". $row['holder_id']; 	
			$row["_brand"] = "$this->root/brand/".$row['brand_id'] ."/about";
			$row['balance'] = number_format(1*$row['balance'], 2, ".", "");
			
			if (strpos($row['holder_authcode'],"*")!==false) $row['authcode'] = $row['account_authcode'];
			else $row['authcode'] = implode("", array_intersect(str_split($row['holder_authcode']), str_split($row['account_authcode'])));
			
			$auth = "_".$row['authcode']; //indent to not have to use strict strpos false comparison
			
			$row['relay']['default'] = $row['holder_id']."-".$row['limkey'];
			
			if (strpos($auth,"c")) {
				if ($row['sign']==1) $row['relay']['budget-add'] = $row['holder_id']."-".$row['limkey']."-c";
				else $row['links']['budget-add'] = "$this->root/forms#budget-add";
			}

			if (strpos($auth,"f")) $row['links']['budget-transfer'] = "$this->root/forms#budget-transfer";
			if (strpos($auth,"t")) $row['relay']['budget-transfer'] = $row['holder_id']."-".$row['limkey']."-t";
			
			if (strpos($auth,"i") OR strpos($auth,"x")) {
				if ($row['sign']==-1) $row['relay']['budget-use'] = $row['holder_id']."-".$row['limkey']."-ix";
				else $row['links']['budget-use'] = "$this->root/forms#budget-use";
			}
			
			$row['links']['accountRecords'] = "$this->root/account/". $row['account_id'] ."/records";
			$row['links']['holder-edit'] = "$this->root/forms#holder-edit";
			$row['links']['relays'] = "$this->root/holder/". $row['holder_id'] ."/relays";
		}
		
		//$this->setForms('budgetIssued');
		//$this->setForms('budgetTransferred');
		//$this->setForms('budgetUsed');
		
		return array($this);
	}
}

