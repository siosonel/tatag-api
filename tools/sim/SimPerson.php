<?php

class SimPerson {
	protected $cycleNum;
	protected $currTick=-1;
	protected $offer;
	protected $coIDs;
	
	function __construct($data) {		
		$name = (mt_rand(0,99) . microtime(true));
		$input = json_decode('{
			"email": "'. $name .'@tatag.sim",
			"name": "'. $name .'",
			"password": "'. $name .'"
		}');
		
		$Users = new UserCollection($input);		
		$arr = $Users->add();
		$this->user_id = $arr[0]->user_id;
		$this->create_brand();
		
		$this->offer = new stdClass();
		$this->offer->from_user = $this->user_id;
		$this->offer->from_acct = $this->expId;		
	}
	
	function create_brand() {
		Requester::$user_id = $this->user_id;
		$types = array("gov", "for-profit", "nonprofit");
		$distribution = array(0,1,2,0,1,2,2,2);
		$area_codes = array(206,425,253,415,650,408);
		
		$input = json_decode('{
			"name": "brand #'. $this->user_id .'",
			"mission": "To be a brand of '. $this->name .'",
			"description": "a brand of '. $this->name .'",
			"country_code": "USA",
			"area_code": '. $area_codes[mt_rand(0,5)] .',
			"type_system": "'. $types[$distribution[mt_rand(0,7)]] .'",
			"type_id": '. mt_rand(1,10) .'
		}'); 
		
		$BrandCollection = new BrandCollection($input);
		$brand = $BrandCollection->add()[0];
		global $Brands; $Brands[] = $brand;
		
		foreach($brand->accounts AS &$a) {
			if ($a->name=='Main Revenue') { $this->revId = $a->account_id; $this->revBal = 0;}
			else if ($a->name=='Main Expense') {$this->expId = $a->account_id; $this->expBal = 0;}
		}
		
		$sql = "UPDATE brands SET created='2015-03-01 00:00:00'";
		DBquery::set($sql);
	}
		
	function startCycle($cycleNum) {
		$this->cycleNum=$cycleNum;
		if (!$cycleNum) $this->seedCoIDs();
		$this->addBudget();
	}
	
	
	function seedCoIDs() {
		$coIDs = range(0,NUM_PERSONS-1);
		shuffle($coIDs);
		
		foreach($coIDs AS $k=>$i) {
			if ($i != $this->user_id) {
				$this->coIDs[] = $i;
				for($j=0; $j<$k; $j++) $this->coIDs[] = $i;
			} 
		}
		
		shuffle($this->coIDs); 
		$this->coIDs = array_slice($this->coIDs, 0, round(count($this->coIDs)/5));
	}
	
	function addBudget() {		
		$amount = !$this->cycleNum ? mt_rand(50,100) : $this->getCycleInflow();
		$date = date( "Y-m-d", strtotime("2015W". sprintf("%02u", $this->cycleNum+10) ."1") );
		$created = "$date 12:00:00"; 
		$updated = "$date 12:00:05";
		
		$sql = "INSERT INTO records (txntype,from_acct,from_user,to_acct,to_user,amount,ref_id,status,created,updated) 
			VALUES ('np',$this->revId,$this->user_id,$this->expId,$this->user_id,$amount,$this->cycleNum,7,'$created','$updated')";
		
		if (DBquery::set($sql)) {
			$this->revBal += -1*$amount;
			$this->expBal += $amount;
		}
	}
	
	function getCycleInflow() {
		$sql = "SELECT SUM(AMOUNT) as inflow 
			FROM records 
			WHERE to_acct=$this->revId AND from_acct!=$this->expId AND ref_id=$this->cycleNum-1
			HAVING inflow > 0";
		$rows = DBquery::get($sql);
		if (!$rows) return 1;
		return round((10*$rows[0]['inflow'])/mt_rand(8,11));
	}
	
	function useBudget($cycleNum, $tickNum) {
		if ($this->expBal <= 0) return;
		
		$this->offer->amount = mt_rand(1, max(2,round($this->expBal/(TICK_MAX-$tickNum)))); 
		if ($this->offer->amount <= 0 OR $this->offer->amount > $this->expBal) return;
		
		$this->cycleNum=$cycleNum;
		
		$coId = $this->coIDs[ mt_rand(0,count($this->coIDs)-1) ]; if ($coId==$this->user_id) echo "[$coId]";
		//global $sharedCoIDs; $coId = $sharedCoIDs[ mt_rand(0,count($this->coIDs)-1) ];
		
		global $Persons;
		if ($Persons[$coId]->evalTxnOffer($this->offer)) $this->expBal += -1*$this->offer->amount;
		//else $this->declined[] = array($coId, $this->offer->amount);
	}
	
	function evalTxnOffer($offer) {
		if ($offer->amount > -1*$this->revBal) {return false;}
		$date = date( "Y-m-d", strtotime("2015W". sprintf("%02u", $this->cycleNum+10) ."1") );
		$created = "$date 12:00:00"; 
		$updated = "$date 12:00:05";
		
		$sql = "INSERT INTO records (txntype,from_acct,from_user,to_acct,to_user,amount,ref_id,created,updated,status) 
			VALUES ('pn', $offer->from_acct, $offer->from_user, $this->revId, $this->user_id, $offer->amount, $this->cycleNum, '$created', '$updated', 7)";
			
		if (DBquery::set($sql)) {
			$this->revBal += $offer->amount;
			return 1;
		}
	}
}



