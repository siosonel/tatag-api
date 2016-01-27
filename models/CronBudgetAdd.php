<?php

class CronBudgetAdd extends Base {
	function __construct() {
		if ($_SERVER['REMOTE_ADDR']!=$_SERVER['SERVER_ADDR'] AND $_SERVER['REMOTE_ADDR']!='127.0.0.1') {
			Error::http(403, "Requests for the cron/report resource must originate from the server environment.");
		}
	}

	function get() {
		$brands = "SELECT brand_id
			FROM accounts a
			INNER JOIN (
				SELECT from_acct, MAX(created) AS created
				FROM records
				WHERE txntype='np'
				GROUP BY from_acct
			) r ON a.account_id = r.from_acct AND TIMESTAMPDIFF(HOUR,r.created,NOW()) > 168";

		$sql = "SELECT brand_id, SUM(by_all_limit*amount) AS weeklyBudget
			FROM promos p 
			INNER JOIN relays r ON p.relay_id = r.relay_id
			WHERE p.brand_id IN ($brands)
			GROUP BY brand_id";

		$rows = DBquery::get($sql);

		$graph = array();
		
		foreach($rows AS $r) {
			$sql = "CALL budgetRevExp($r[brand_id])";
			$b = DBquery::get($sql)[0];
			$b['weeklyBudget'] = $r['weeklyBudget'];
			$b['plannedAdd'] = min($r['weeklyBudget']-$b['revBal'], $r['weeklyBudget']-$b['expBal']);
			
			$sql = "INSERT INTO records (txntype,from_acct, to_acct, amount, note, status) VALUES ('np', $b[revAcct], $b[expAcct], $b[plannedAdd], 'auto-add', 0);";
			$b['sql'] = $sql;
			$b['numInserted'] = DBquery::set($sql);

			$graph[] = $b;
		}

		return $graph;
	}
}

