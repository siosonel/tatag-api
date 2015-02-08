<?php

class AccountRecords extends Base {
	function __construct($data='') {		
		$this->account_id = $this->getID();
		
		if (!$info=Requester::isAccountHolder($this->account_id)) Error::http(403, "The requester is not an accountholder for account #$this->account_id.");		
		$this->brand_id = $info['brand_id'];
		$this->holder_id = $info['holder_id'];
		$this->limkey = $info['limkey'];
		
		$this->{"@type"} = "accountRecords";		
		$this->{'@id'} = "/account/$this->account_id/records";
		$this->table = "records";
		$this->idkey = 'account_id';
		$this->okToGet = array("brand_id", "account_id", "name", "balance", "unit", "authcode");
		$this->init($data);
	}
	
	function get() {
		$actions = array("pn"=>"use", "np"=>"add", "pp"=>"transfer", "nn"=>"transfer");
		
		$minRecordID = (isset($_GET['minRecordID']) AND $_GET['minRecordID']) ? $_GET['minRecordID'] : 0;
		$maxRecordID = (isset($_GET['maxRecordID']) AND $_GET['maxRecordID']) ? $_GET['maxRecordID'] : 99999999;  
		$sql = "CALL accountRecords($this->account_id, $minRecordID, $maxRecordID)";
		$this->items = DBquery::get($sql); 
		
		
		
		foreach($this->items AS &$r) {
			if ($r['brand_id']==$this->brand_id) {
				$r['other'] = $r['other_acct'];
			}
			else {
				unset($r['other_acct']);
				$r['other'] = $r['brand_name'];
			}
			
			if ($r['amount'] > 0) {
				$action = $actions[$r['txntype']];
				$r['orig_record_id'] = $r['record_id'];
				
				if ($r['direction']=='from') $r['links']["budget-un$action"] = "/forms#budget-un$action";
				else $r['relay']["budget-un$action"] = $this->holder_id ."-". $this->limkey ."-". $r['txntype'];
				
				$r['relay']["default"] = $this->holder_id ."-". $this->limkey;
			}
		}
		
		$this->setForms();
		
		return array($this);
	}
}