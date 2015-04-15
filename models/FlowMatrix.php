<?php

class FlowMatrix {
	function get() {		
		$week = isset($_GET['weekNum']) ? $_GET['weekNum'] : 10;
		if (!is_numeric($week)) Error::http(400, "Invalid week='$week' value - must be numeric.");
		
		$sql = "SELECT f.brand_id as source, t.brand_id as target, SUM(amount) as amount
		FROM records r
		JOIN accounts f ON r.from_acct=f.account_id
		JOIN accounts t ON r.to_acct=t.account_id
		WHERE txntype='pn' AND WEEKOFYEAR(r.updated)=$week
		GROUP BY f.brand_id, t.brand_id";

		$rows = DBquery::get($sql, array($tickNum));
		if (!$rows) return array(array("@id"=>$_SERVER['REQUEST_URI'], "brands" => array(), "matrix" => array()));

		$brands = array();

		//uniquely index brand_id's for reference
		foreach($rows AS $r) {
			if (!in_array($r['source'], $brands)) $brands[] = $r['source'];
			if (!in_array($r['target'], $brands)) $brands[] = $r['target'];
		}

		//order ascending numerically
		sort($brands);

		//create square matrix with zero values
		$matrix = array_fill(0, count($brands), array()); 
		foreach($matrix AS &$m) $m = array_fill(0, count($brands), 0); 

		//fill-in matrix cells with transaction amounts, as applicable
		foreach($rows AS $r) {
			$s = array_search($r['source'], $brands);
			$t = array_search($r['target'], $brands);
			if ($s!=$t) $matrix[$s][$t] = $r['amount'];
		} 

		return array(array("@id"=>$_SERVER['REQUEST_URI'], "brands" => $brands, "matrix" => $matrix));
	}
}

