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

		if (!isset($_GET['typeSystem']) OR !$_GET['typeSystem'])  $typeSystem = '';
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
		$d = array();
		
		$weekMin = 54;
		$weekMax = 0;
		$hasData = array();
		
		foreach($rows AS $r) {
			if ($r['week']<$weekMin) $weekMin = $r['week'];
			if ($r['week']>$weekMax) $weekMax = $r['week'];
			
			$d["".$r['brand']][] = $r;
			$hasData["".$r['brand']][] = $r['week'];
		}
		
		$expectedWeeks = range($weekMin, $weekMax);
		foreach($hasData AS $brand_id=>$hasWeeks) {
			$missingWeeks = array_diff($expectedWeeks, $hasWeeks);
			foreach($missingWeeks AS $wk) $d[$brand_id][] = array('brand'=>$brand_id,'week'=>$wk,'amount'=>0);
		}
 		
		$data = array();
		foreach($d AS $key=>$arr) {
			$data[] = array("brand_id"=>$key, $this->metric=>$arr);
		}
		
		return array(array("@id"=>$_SERVER['REQUEST_URI'], "data"=>$data));
	}

	function getAggregated() {	
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
}