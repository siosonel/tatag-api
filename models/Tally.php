<?php

class Tally {
	function __construct() {
		$this->params = array();
		if (!isset($_GET['countryCode'])) $this->countryCode = '';
		else {
			$this->countryCode = "AND country_code=?";
			$this->params[] = $_GET['countryCode'];
		}

		if (!isset($_GET['areaCode'])) $this->areaCode = '';
		else {
			$this->areaCode = "AND area_code=?";
			$this->params[] = $_GET['areaCode'];
		}

		if (!isset($_GET['typeSystem']))  $typeSystem = '';
		else {
			$this->typeSystem = "AND type_system=?";
			$this->params[] = $_GET['typeSystem'];
		}
	}
	
	function get() { 
		if (!$this->metric) return $this->getAggregated();
		
		$sql = "SELECT WEEKOFYEAR(r.updated) AS week, $this->ft.brand_id as brand, SUM(amount) as amount
		FROM records r
		JOIN accounts f ON r.from_acct=f.account_id
		JOIN accounts t ON r.to_acct=t.account_id
		$this->extraJoinCond
		WHERE txntype='$this->txntype' AND status=7 $this->interBrandCond $this->countryCode $this->areaCode $this->typeSystem
		GROUP BY $this->groupByCond
		ORDER BY week ASC, amount DESC";
		
		$rows = DBquery::get($sql, $this->params);
		
		foreach($rows AS $r) {
			$d["".$r['brand']][] = $r;
		}
 		
		foreach($d AS $key=>$arr) {
			$data[] = array("brand_id"=>$key, $this->metric=>$arr);
		}
		
		return array(array("@id"=>$_SERVER['REQUEST_URI'], "data"=>$data));
	}

	function getAggregated() {
		if (isset($_GET['setBy']) AND $_GET['setBy']==TALLY_USER) return $this->set();   
	
		$sql = "SELECT brand_id, week, n_start_bal, n_start_bal, issued, intrause, inflow, outflow, 
			num_members, member_hours, type_system, type_id, country_code, area_code, tallies.updated
		FROM tallies
		JOIN brands USING (brand_id)
		WHERE 1 $this->countryCode $this->areaCode $this->typeSystem
		ORDER BY brand_id ASC, week ASC";
		
		$rows = DBquery::get($sql, $this->params);
		if (!$rows) return array(new stdClass()); 

		foreach($rows AS $r) {
			$d["".$r['brand_id']][] = $r;
		}
		
		foreach($d AS $key=>$arr) {
			$data[] = array("brand_id"=>$key, "data"=>$arr);
		}
		
		return array(array("@id"=>$_SERVER['REQUEST_URI'], "data"=>$data));	
	}
	
	
	function set() { //DBquery::set("delete from tallies where 1");
		$this->currWeek = DBquery::get('SELECT WEEKOFYEAR(NOW()) AS week;')[0]['week'];
		$prevWeek = $currWeek -1;
	
		$sql = "SELECT brand_id, WEEKOFYEAR(created) as weekCreated FROM brands ORDER BY brand_id ASC";		
		$rows = DBquery::get($sql);
		
		foreach($rows AS $r) $this->setTally($r['brand_id'], $r['weekCreated']);
		return array("data saved");
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
		
		for($i=$row[0]['week']; $i < $this->currWeek; $i++) {
			$day1 = date( "Y-m-d", strtotime("2015W". sprintf("%02u", $i) ."1") ) ." 00:00:00";
			$day7 = date( "Y-m-d", strtotime("2015W". sprintf("%02u", $i) ."7") ) ." 00:00:00"; 
			$b = DBquery::get("CALL tally($brand_id, '$day1', '$day7')");
			$b = $b ? $b[0] : array("added"=>0, "intrause"=>0, "inflow"=>0, "outflow"=>0, "numMembers"=>0, "totalMemberHours"=>0);
			
			if ($i!=$row[0]['week']) $tally_id = $this->insertTally($brand_id, $i, $nBal, $pBal);
						
			$nBal += $b['added'] - $b['intrause'] - $b['inflow']; 
			$pBal += $b['added'] - $b['intrause'] - $b['outflow'];
			
			$this->updateTally($tally_id, $b['added'], $b['intrause'], $b['inflow'], $b['outflow'], $b['numMembers'], $b['totalMemberHours']);
		}
	}
	
	function insertTally($brand_id, $week, $nBal, $pBal) {
		$sql = "INSERT INTO tallies (created, brand_id, p_start_bal, n_start_bal, issued, intrause, inflow, outflow, num_members, member_hours, week, year)
			VALUES (NOW(), $brand_id, $pBal, $nBal, 0, 0, 0, 0, 0, 0, $week, 2015)";
		
		DBquery::set($sql);
		return DBquery::$conn->lastInsertId();
	}
	
	function updateTally($tally_id, $issued, $intrause, $inflow, $outflow, $num_members, $member_hours) {	
		$sql = "UPDATE tallies SET updated=NOW(), issued=$issued, intrause=$intrause, inflow=$inflow, outflow=$outflow, num_members=$num_members, member_hours=$member_hours
			WHERE tally_id=$tally_id";
			
		DBquery::set($sql);
	}
}