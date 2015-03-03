<?php

class SimPerson {
	protected $cycleNum;
	protected $currTick=-1;
	protected $offer;
	
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
	}
	
	function create_brand() {
		Requester::$user_id = $this->user_id;
		
		$input = json_decode('{
			"name": "brand #'. $this->user_id .'",
			"mission": "To be a brand of '. $this->name .'",
			"description": "a brand of '. $this->name .'"
		}');
		
		$BrandCollection = new BrandCollection($input);
		$brand = $BrandCollection->add()[0];
		global $Brands; $Brands[] = $brand;
		
		foreach($brand->accounts AS &$a) {
			if ($a->name=='Main Revenue') { $this->revId = $a->account_id; $this->revBal = 0;}
			else if ($a->name=='Main Expense') {$this->expId = $a->account_id; $this->expBal = 0;}
		}
	}
	
	function addBudget($cycleNum) {
		$this->cycleNum=0;
		$amount = !$cycleNum ? mt_rand(50,100) : $this->getCycleInflow();
		
		$sql = "INSERT INTO records (txntype,from_acct,from_user,to_acct,to_user,amount) 
			VALUES ('np',$this->revId,$this->user_id,$this->expId,$this->user_id,$amount)";
		
		if (DBquery::set($sql)) {
			$this->revBal += -1*$amount;
			$this->expBal += $amount;
		}
	}
	
	function getCycleInflow() {
		$sql = "SELECT SUM(AMOUNT) as inflow FROM records WHERE to_acct=$this->revId AND from_acct!=$this->expId HAVING inflow > 0";
		$rows = DBquery::get($sql);
		if (!$rows) return 1;
		return $rows[0]['inflow'];
	}
	
	function useBudget($cycleNum, $tickNum) {
		$this->offer->from_user = $this->user_id;
		$this->offer->from_acct = $this->expId;
		$this->offer->amount = mt_rand(1, round($this->expBal/($tickNum+1))); 
		if ($this->offer->amount <= 0) return;
		
		$coId = mt_rand(0,NUM_PERSONS-1);
		global $Persons;
		if ($Persons[$coId]->evalTxnOffer($this->offer)) $this->expBal += -1*$amount;
	}
	
	function evalTxnOffer($offer) {
		if ($offer->amount > -1*$this->revBal) {echo "--insuff=".json_encode($offer)."--\n"; return false;}
		$sql = "INSERT INTO records (txntype,from_acct,from_user,to_acct,to_user,amount) 
			VALUES ('pn', $offer->from_acct, $offer->from_user, $this->revId, $this->user_id, $offer->amount)";
			
		if (DBquery::set($sql)) {
			$this->revBal += $offer->amount;
			return 1;
		}
	}
}



