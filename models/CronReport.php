<?php

class CronReport extends Collection {
	function __construct() {
		if ($_SERVER['REMOTE_ADDR']!=$_SERVER['SERVER_ADDR'] AND $_SERVER['REMOTE_ADDR']!='127.0.0.1') {
			Error::http(403, "Requests for cron resources must originate from the localhost environment.");
		}

		$this->{"@id"} = "/cron/report";
		$this->{"@type"} = "report";
	}

	function get() {
		$graph = array($this);

		$sql = "INSERT INTO reports 
			(txntype, from_brand, to_brand, amount, max_id, max_updated, keyword, status)
			SELECT txntype, 
				f.brand_id AS from_brand, 
				t.brand_id AS to_brand, 
				SUM(r.amount) AS amount, 
				MAX(record_id) AS max_id,
				MAX(r.updated) AS max_updated,
				p.keyword AS keyword,
				r.status AS status
			FROM records r
			INNER JOIN accounts f ON f.account_id = r.from_acct
			INNER JOIN accounts t ON t.account_id = r.to_acct
			LEFT JOIN promos p ON p.promo_id = r.promo_id
			WHERE record_id > (SELECT COALESCE(MAX(max_id),0) AS max_id FROM reports)
				AND (status < 0 OR status > 6) 
				AND txntype IN ('np','pn') 
			GROUP BY txntype, from_brand, to_brand, keyword";

		$numInserted = DBquery::set($sql);

		//$this->sql = $sql;
		$this->numInserted = $success;
		return $graph;
	}
}
