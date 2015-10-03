<?php

class BudgetRecords extends Base {
	protected $verifier;

	function __construct($data='') {
		//if (!isset($_GET['txntype'])) Error::http(400, "The request must have a value for the URL query parameter 'txntype'.");
		if (isset($_GET['txntype']) AND !in_array($_GET['txntype'], array('np','nn','pp','pn'))) Error::http(400, "The URL query parameter 'txntype' value must be 'np', 'nn', 'pp, OR 'pn'.");
	
		$this->txntype = $_GET['txntype'];		
		$this->subtype = (isset($_GET['subtype']) AND $_GET['subtype']) ? $_GET['subtype'] : '';
		
		$this->{"@type"} = "budgetRecords";
		$this->brand_id = $this->getID();
		if (!Requester::isMember($this->brand_id)) Error::http(403, "Only members or admins of brand #$this->brand_id can view details of its budget activity.");
		
		$this->{'@id'} = "$this->root/budget/$this->brand_id/records";
		$this->table = "records";		
		$this->init($data);		
	}
		
	function get() {
		if (!$this->txntype) {
			$this->issued = $this->{'@id'} ."?txntype=np";
			$this->revTransfer = $this->{'@id'} ."?txntype=nn";
			$this->expTransfer = $this->{'@id'} ."?txntype=pp";
			$this->intrause = $this->{'@id'} ."?txntype=pn&subtype=intrause";
			$this->inflow = $this->{'@id'} ."?txntype=pn&subtype=inflow";
			$this->outflow = $this->{'@id'} ."?txntype=pn&subtype=outflow";
			
			return array_merge(
				array($this), 
				$this->getByTxnType('np'), $this->getByTxnType('nn'), $this->getByTxnType('pp'),
				$this->getIntrause(), $this->getInflow(), $this->getOutflow()				
			);
		}
		else if ($this->txntype!='pn') return $this->getByTxnType();
		else {
			if ($this->subtype=='intrause') return $this->getIntrause();
			else if ($this->subtype=='inflow') return $this->getInflow();
			else if ($this->subtype=='outflow') return $this->getOutflow();
			else Error::http(403, "When requesting records with txntype='pn', a URL query parameter value must be provided for 'subtype' and the value must equal 'intrause', 'inflow', OR 'outflow'. Actual value='$this->subtype'.");
		}
	}
	
	function getByTxnType($txntype="") {
		if (!$txntype) $txntype = $this->txntype;
	
		$sql = "SELECT txntype, record_id, r.created, from_acct AS `from`, to_acct AS `to`, amount, `note`
		FROM records r 
		JOIN accounts a ON r.from_acct = a.account_id OR r.to_acct = a.account_id
		WHERE brand_id=? AND txntype=?
		GROUP BY record_id
		ORDER BY record_id DESC 
		LIMIT 50";
		$items = DBquery::get($sql, array($this->brand_id, $txntype));
		
		if ($txntype=='np') $type = "issued";
		else if ($txntype=='nn') $type = "revTransfer";
		else if ($txntype=='pp') $type = "expTransfer";
		
		return array(array(
			"@type" => $type,
			"@id" => "$this->root/budget/$this->brand_id/records?txntype=$txntype",
			"items" => $items
		));
	}
	
	function getIntrause() {
		$sql = "SELECT txntype, record_id, r.created, from_acct AS `from`, to_acct AS `to`, amount, `note`
		FROM records r 
		JOIN accounts af ON r.from_acct = af.account_id 
		JOIN accounts at ON r.to_acct = at.account_id
		WHERE txntype='pn' AND af.brand_id=? AND at.brand_id=?
		GROUP BY record_id
		ORDER BY record_id DESC LIMIT 50";
		$items = DBquery::get($sql, array($this->brand_id, $this->brand_id));
		
		return array(array(
			"@type" => "intrause",
			"@id" => "$this->root/budget/$this->brand_id/records?txntype=pn&subtype=intrause",
			"items" => $items
		));
	}
	
	function getInflow() {
		$sql = "SELECT txntype, record_id, r.created, bf.name AS `from`, to_acct AS `to`, amount, `note`
		FROM records r 
		JOIN accounts af ON r.from_acct = af.account_id 
		JOIN brands bf ON af.brand_id = bf.brand_id
		JOIN accounts at ON r.to_acct = at.account_id
		WHERE txntype='pn' AND af.brand_id!=? AND at.brand_id=?
		GROUP BY record_id
		ORDER BY record_id DESC LIMIT 50";
		$items = DBquery::get($sql, array($this->brand_id, $this->brand_id));
		
		return array(array(
			"@type" => "inflow",
			"@id" => "$this->root/budget/$this->brand_id/records?txntype=pn&subtype=inflow",
			"items" => $items
		));
	}
	
	function getOutflow() {
		$sql = "SELECT txntype, record_id, r.created, from_acct AS `from`, bt.name AS `to`, amount, `note`
		FROM records r 
		JOIN accounts af ON r.from_acct = af.account_id 
		JOIN accounts at ON r.to_acct = at.account_id
		JOIN brands bt ON at.brand_id = bt.brand_id
		WHERE txntype='pn' AND af.brand_id=? AND at.brand_id!=?
		GROUP BY record_id
		ORDER BY record_id DESC LIMIT 50";
		$items = DBquery::get($sql, array($this->brand_id, $this->brand_id));
		
		return array(array(
			"@type" => "outflow",
			"@id" => "$this->root/budget/$this->brand_id/records?txntype=pn&subtype=outflow",
			"items" => $items
		));
	}
}

?>