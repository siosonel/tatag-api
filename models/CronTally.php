<?php

require_once "models/Tally.php";

class CronTally extends Tally {
	function __construct() {
		if ($_SERVER['REMOTE_ADDR']!=$_SERVER['SERVER_ADDR'] AND $_SERVER['REMOTE_ADDR']!='127.0.0.1') {
			Error::http(403, "Requests for cron resources must originate from the server environment.");
		}

		$this->{"@id"} = "$this->root/cron/tally";
		$this->{'@type'} = "cronTally";
	}

	function get() { //DBquery::set("delete from tallies where 1");
		$this->currWeek = DBquery::get('SELECT WEEKOFYEAR(NOW()) AS week;')[0]['week'];
		$prevWeek = $currWeek -1;
	
		$sql = "SELECT brand_id, WEEKOFYEAR(created) as weekCreated FROM brands ORDER BY brand_id ASC";		
		$rows = DBquery::get($sql);
		
		$this->numBrands = count($rows);
		$this->totalUpdates = 0;
		foreach($rows AS $r) $this->totalUpdates += $this->setTally($r['brand_id'], $r['weekCreated']);
		return array($this);
	}
	
	function setTally($brand_id, $weekCreated) {
		$sql = "SELECT tally_id, n_start_bal, p_start_bal, issued, intrause, inflow, outflow, week
			FROM tallies 
			WHERE brand_id=$brand_id 
			ORDER BY week DESC LIMIT 1";
		$row = DBquery::get($sql);
		
		if (!$row) {
			$tally_id = $this->insertTally($brand_id, $weekCreated, 0, 0); //insert beginning tally
			$row = DBquery::get("SELECT * FROM tallies WHERE tally_id=$tally_id");
		}
	
		//update previous tally, plus insert a new tally for the current week
		$nBal = $row[0]['n_start_bal'];
		$pBal = $row[0]['p_start_bal'];
		$tally_id = $row[0]['tally_id'];
		$numUpdated = 0;
		
		for($i=$row[0]['week']; $i < $this->currWeek; $i++) {
			$day1 = date( "Y-m-d", strtotime("2015W". sprintf("%02u", $i) ."1") ) ." 00:00:00";
			$day7 = date( "Y-m-d", strtotime("2015W". sprintf("%02u", $i) ."7") ) ." 00:00:00"; 
			$b = DBquery::get("CALL tally($brand_id, '$day1', '$day7')");
			$b = $b ? $b[0] : array("added"=>0, "intrause"=>0, "inflow"=>0, "outflow"=>0, "numMembers"=>0, "totalMemberHours"=>0);
			
			if ($i!=$row[0]['week']) $tally_id = $this->insertTally($brand_id, $i, $nBal, $pBal);
						
			$nBal += $b['added'] - $b['intrause'] - $b['inflow']; 
			$pBal += $b['added'] - $b['intrause'] - $b['outflow'];
			
			$numUpdated += $this->updateTally($tally_id, $b['added'], $b['intrause'], $b['inflow'], $b['outflow'], $b['numMembers'], $b['totalMemberHours']);
		}

		return $numUpdated;
	}
	
	function insertTally($brand_id, $week, $nBal, $pBal) {
		$sql = "INSERT INTO tallies (created, brand_id, p_start_bal, n_start_bal, issued, intrause, inflow, outflow, num_members, member_hours, week, year)
			VALUES (NOW(), $brand_id, $pBal, $nBal, 0, 0, 0, 0, 0, 0, $week, 2015)";
		
		DBquery::set($sql);
		return DBquery::$conn->lastInsertId();
	}
	
	function updateTally($tally_id, $issued, $intrause, $inflow, $outflow, $num_members, $member_hours) {	
		if (!$issued) $issued = 0;
		if (!$intrause) $intrause = 0;
		if (!$inflow) $inflow = 0;
		if (!$outflow) $outflow = 0;
		
		$sql = "UPDATE tallies SET updated=NOW(), issued=$issued, intrause=$intrause, inflow=$inflow, outflow=$outflow, num_members=$num_members, member_hours=$member_hours
			WHERE tally_id=$tally_id";
			
		return DBquery::set($sql);
	}
}

