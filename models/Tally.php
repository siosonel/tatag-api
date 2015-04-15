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
		$sql = "SELECT WEEKOFYEAR(r.updated) AS week, $this->ft.brand_id as brand, SUM(amount) as amount
		FROM records r
		JOIN accounts f ON r.from_acct=f.account_id
		JOIN accounts t ON r.to_acct=t.account_id
		$this->extraJoinCond
		WHERE txntype='$this->txntype' AND status=7 $this->interBrandCond $this->countryCode $this->areaCode $this->typeSystem
		GROUP BY $this->groupByCond
		ORDER BY week ASC, amount DESC";
		
		$rows = DBquery::get($sql, $this->params);
		if (!$rows) return array(new stdClass()); 

		foreach($rows AS $r) {
			$d["".$r['brand']][] = $r;
		}
		
		foreach($d AS $key=>$arr) {
			$data[] = array("brand_id"=>$key, $this->metric=>$arr);
		}

		return array(array("@id"=>$_SERVER['REQUEST_URI'], "data"=>$data));	
	}
}