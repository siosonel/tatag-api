<?php

class SimRecords extends Collection {
	private $weekNum=0;
	private $numBrands=0;
	private $rating=array();
	private $minNumRaters = 2;
	private $nominalRating = 95;
	
	function __construct() {
		$this->weekNum = date('W');
		$this->setRatings();
	}
	
	
	function setRatings($brandID=0) {
		if (!$brand_id) {
			$sql = "SELECT brand_id, count(*) AS num, sum(rating) AS rating, GROUP_CONCAT(user_id) AS raters
			FROM ratings
			WHERE ended IS NULL
			GROUP BY brand_id";
		
			$rows = DBquery::get($sql);
		}
		else {
			$rows = array(array(
				"other_id"=>$brandID, "num"=>$this->minNumRaters, "rating"=>$nominalRating, "raters"=>""
			));
		}
		
		foreach($rows AS $f) {
			$rated[] = $f['other_id'];
			
			$this->rating[$f['brand_id']] = array(
				"count" => 1*$f['num'],
				"rating_total" => 1*$f['rating'],
				"rating_avg" => ($f['num']*$f['rating'] + (max(0,$this->minNumRaters-1*$f['num']))*$this->nominalRating) / max($f['num'], $this->minNumRaters),
				"raters" => explode(" ", $f['raters'])				
			);
		}
	}
	
	function get() {
		if ($_SERVER['REMOTE_ADDR']!=$_SERVER['SERVER_ADDR'] AND $_SERVER['REMOTE_ADDR']!='127.0.0.1') Error::http(403, "Requests for the sim/records resource must originate from the server environment.");
	
		$sql = "SELECT brand_id FROM brands WHERE type_system='sim'";
		$rows = DBquery::get($sql);
		$this->numBrands = count($rows);
		shuffle($rows);
		
		$brands = array();
		foreach($rows AS $b) {
			$brands[] = DBquery::get("CALL budgetRevExp(". $b['brand_id'] .")")[0];
			if (!$this->rating[$b['brand_id']]) $this->setRatings($b['brand_id']); 
		}
	
		foreach($brands AS &$b) {
			$b['revBal'] = 1*$b['revBal'];
			$b['expBal'] = 1*$b['expBal'];
			$b['inflow'] = 1*$b['inflow'];
			$b['lastWeekAdded'] = 1*$b['lastWeekAdded'];
			
			if (!$b['revBal'] AND !$b['expBal']) $this->addBudget($b);
			else if ($b['lastWeekAdded'] < $this->weekNum AND $b['revBal'] > $b['inflow']) $this->addBudget($b); 
			else $this->transact($b, $brands[mt_rand(0,$this->numBrands-1)]);
		} 
		
		return $brands;
	}
	
	function addBudget(&$b) {
		if (!$b['revAcct'] OR !$b['expAcct']) return;
		if ($b['revBal'] > $b['inflow']) return;
	
		$amount = $b['inflow'] ? $b['inflow'] : mt_rand(5,20);
		if ($amount>20) $amount = 20;
		
		$revAcct = $b['revAcct'];
		$expAcct = $b['expAcct'];
		
		$sql = "INSERT INTO records (txntype,from_acct,from_user,to_acct,to_user,amount,ref_id,status,created,updated) 
			VALUES ('np',$revAcct,0,$expAcct,0,$amount,$this->weekNum,0,NOW(),NOW())";
		
		$b['added'] = $sql;
		DBquery::set($sql);
		$b['record_id'] = DBquery::$conn->lastInsertId();		
		DBquery::get("CALL approveRecord(". $b['record_id'] .")");
	}
	
	function transact(&$from, &$to) {
		if ($from['brand_id']==$to['brand_id']) return;
		if (!$from['expBal'] OR !$to['revBal']) return;
		if (!$this->rating[$from['brand_id']] OR !$this->rating[$to['brand_id']]) return;
		
		if ($from['expBal']==1 OR $to['revBal']==1) $amount = 1;
		else $amount = min(20, mt_rand(1, max(2, round(min($from['expBal']/4, $to['revBal']/4))))); //round($to['expBal'])));	
		
		if ($amount>$from['expBal'] OR $amount>$to['revBal']) return;
		
		$fromAcct = $from['expAcct'];
		$toAcct = $to['revAcct'];
		
		$sql = "INSERT INTO records (txntype,from_acct,from_user,to_acct,to_user,amount,note,ref_id,status,created,updated) 
			VALUES ('pn',$fromAcct,0,$toAcct,0,$amount,'sim',$this->weekNum,0,NOW(),NOW())";
			
		$from['outflow'] = $sql;
		DBquery::set($sql);
		$from['record_id'] = DBquery::$conn->lastInsertId();	
		
		$status = $this->advise($from,$to);
		
		if ($status==7) DBquery::get("CALL approveRecord(". $from['record_id'] .")");
		else {
			$sql = "UPDATE records SET status=?, updated=NOW() WHERE record_id=?";
			DBquery::set($sql, array($status, $from['record_id']));
		}
	}
	
	function advise(&$from,&$to) {
		$fromID = $from['brand_id'];
		$toID = $to['brand_id'];
		$this->rating[$fromID]['rand_val'] = mt_rand(0,100);
		
		if ($this->rating[$fromID]['rand_val'] <= $this->rating[$fromID]['rating_avg']) $from['record_status'] = 7;
		else $from['record_status'] = -1;
		
		return $from['record_status'];
	}
}

