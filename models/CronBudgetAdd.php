<?php

class CronBudgetAdd extends Base {
	function __construct() {

	}

	function get() {
		$brands = "SELECT brand_id
			FROM accounts a
			INNER JOIN records r ON a.account_id=r.from_acct AND txntype='np' AND TIMESTAMPDIFF(HOUR,r.updated,NOW()) > 168
			WHERE 1
			GROUP BY brand_id";

		$sql = "SELECT brand_id, SUM(by_all_limit*amount) AS plannedBudget
			FROM promos p 
			INNER JOIN relays r ON p.relay_id = r.relay_id
			WHERE p.brand_id IN ($brands)
			GROUP BY brand_id";

		$rows = DBquery::get($sql);

		$graph = array();
		
		foreach($rows AS $r) {
			$sql = "CALL budgetRevExp($r[brand_id])";
			$b = DBquery::get($sql)[0];
			$b['plannedBudget'] = $r['plannedBudget'];
			$b['plannedAdd'] = min($r['plannedBudget']-$b['revBal'], $r['plannedBudget']-$b['expBal']);
			$graph[] = $b;
		}

		return $graph;
	}
}

