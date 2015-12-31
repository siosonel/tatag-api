<?php

class AccountRecords extends Collection {
	function __construct($data='') {		
		$this->account_id = $this->getID();
		
		if (!$info=Requester::isAccountHolder($this->account_id)) Error::http(403, "The requester is not an accountholder for account #$this->account_id.");		
		$this->brand_id = $info['brand_id'];
		$this->holder_id = $info['holder_id'];
		$this->limkey = $info['limkey'];
		
		$this->{"@type"} = "accountRecords";		
		$this->{'@id'} = "$this->root/account/$this->account_id/records";
		$this->table = "records";
		$this->idkey = 'account_id';
		
		$this->pageOrder = "desc"; //prevents being reset
		
		$this->init($data);
		
		$this->okToGet = array("brand_id", "account_id", "name", "balance", "unit", "authcode");
		$this->okToSet = array("status");
		$this->okToFilterBy =  array("record_id");	
	}
	
	function set() {
		if (!$_GET['record_id']) Error::http(403, 'Missing record_id GET query parameter value, which is required for updating the record status.');
	
		if ($this->status==7) $row = DBquery::get("CALL approveRecord(". $_GET['record_id'] .")");
		else {
			$this->setFilters($_GET);		
			$row = $this->update();
		}
		
		return array($this->obj);
	}
	
	function get() {
		$actions = array("pn"=>"use", "np"=>"add", "pp"=>"transfer", "nn"=>"transfer");
		$advisory = array();		
		$graph = array($this);
				
		//only desc order, no option to order asc
		$sql = "CALL accountRecords($this->account_id, $this->limitID, $this->itemsLimit, $this->minUpdated, $this->cutoffID)";
		$items = DBquery::get($sql); 		
		
		foreach($items AS &$r) {
			$r['@type'] = 'accountRecord';
			$r['@id'] = $this->{'@id'} .'?record_id='. $r['record_id'];
		
			if ($r['brand_id']==$this->brand_id) {
				$r['other'] = $r['other_acct'];
			}
			else {
				unset($r['other_acct']);
				$r['other'] = $r['brand_name'];
			}
			
			$status = $r['status'];
			$amount = $r['amount'];
						
			if ($amount > 0 AND $status==7) {
				$action = $actions[$r['txntype']];
				$r['orig_record_id'] = $r['record_id'];
				
				if ($action != 'transfer') {
					if ($r['direction']=='from') $r["un$action"] = "$this->root/form/budget-un$action";
					else $r['relay']["un$action"] = $this->holder_id ."-". $this->limkey ."-". $r['txntype'];
				}
				
				$r['relay']["default"] = $this->holder_id ."-". $this->limkey;
			}
			
			if ($status>=0 AND $status<7 AND (($r['direction']=='from' AND $amount>0) OR ($r['direction']=='to' AND $amount<0))) {
				if ($status==0) $r['hold']="$this->root/form/record-hold";
				$r['approve']="$this->root/form/record-approve";
				$r['reject']="$this->root/form/record-reject";
				
				if ($r['brand_id']!=$this->brand_id) {
					if (!isset($this->advisor)) $this->advisor = $this->getAdvisor();
					if (!isset($advisory[$r['brand_id']])) $advisory[$r['brand_id']] = $this->getAdvisory($r['brand_id']);
					$r['advisory'] = $advisory[$r['brand_id']];
				}
			}
			
			$graph[] = $r;
			$this->items[] = $r['@id'];
		}
		
		$this->paginate('record_id', $graph);
		
		return $graph;
	}
	
	function getAdvisor() {
		$sql = "SELECT advisor FROM brands WHERE brand_id=?";
		$row = DBquery::get($sql, array($this->brand_id));
		if (!$row[0]['advisor']) return HOME ."/advisor.php{?brand_id,revBudget,expBudget,inflow,outflow,numMembers,totalMemberHours}";
		return str_replace('http://tatag.dev/api', HOME, $row[0]['advisor']);
	}
	
	function getAdvisory($brand_id) {
		$startDate = "2015-01-01 00:00:00";
		$endDate = "2015-12-31 11:59:59";
	
		$tally = DBquery::get("CALL tally($brand_id, '$startDate', '$endDate')")[0];
		$url = $this->advisor; 
		
		if ($pos = strpos($url,'{?')) {
		  $params = substr($url, $pos+2, -1);
			foreach(explode(",", $params) AS $k) $p[] = "$k=". $tally[$k];
			$url = substr($url,0,$pos) ."?". implode("&", $p);
		}
		else {
			foreach($tally AS $k=>$v) $url = str_replace("{".$k."}", $v, $url);  
		}
		
		$advisory = json_decode(file_get_contents($url));
		return $advisory ? $advisory->{"@graph"}[0] : new stdClass();
	}
}