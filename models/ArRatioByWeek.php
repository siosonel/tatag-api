<?php

class ArRatioByWeek extends collection {
	function __construct() {
		$this->week = date('W');
	}
	
	function get() {
		$sql  = "select b.brand_id, week, 'approved' AS type,
			COALESCE(numApproved,0) AS total
		from brands b
		LEFT JOIN (
			SELECT brand_id, WEEKOFYEAR(r.updated) AS week, COUNT(*) as numApproved
			FROM records r
			JOIN accounts f ON f.account_id=r.from_acct
			WHERE status=7 and txntype='pn'
			GROUP BY brand_id, week
		) approved ON approved.brand_id = b.brand_id
		UNION ALL
		select b.brand_id, week, 'rejected' AS type, 
			COALESCE(numrejected,0) AS total
		from brands b
		LEFT JOIN (
			SELECT brand_id, WEEKOFYEAR(r.updated) AS week, COUNT(*) as numrejected
			FROM records r
			JOIN accounts f ON f.account_id=r.from_acct
			WHERE status=7 and txntype='pn'
			GROUP BY brand_id, week
		) rejected ON rejected.brand_id = b.brand_id";
		
		$rows = DBquery::get($sql);
		
		$d = [];
		$weekMin = 54;
		$weekMax = 0;
		$hasData = array();
		
		foreach($rows AS &$r) {			
			if (!$r['week']) $r['week'] = $this->week;	
			if ($r['week']<$weekMin) $weekMin = $r['week'];
			if ($r['week']>$weekMax) $weekMax = $r['week'];
			
			$hasData[$r['brand_id']][] = $r['week']; 
			$d[$r['brand_id']][$r['week']][$r['type']] = $r['total'];
		}
			
		$expectedWeeks = range($weekMin, $weekMax);
		foreach($hasData AS $brand_id=>$hasWeeks) {
			foreach($hasWeeks AS $wk) {
				if (!isset($d[$brand_id][$wk]['approved'])) $d[$brand_id][$wk]['approved'] = 0;
				if (!isset($d[$brand_id][$wk]['rejected'])) $d[$brand_id][$wk]['rejected'] = 0;
			}
			
			$missingWeeks = array_diff($expectedWeeks, $hasWeeks);
			
			foreach($missingWeeks AS $wk) {
				$d[$brand_id][$wk]['approved'] = 0;
				$d[$brand_id][$wk]['rejected'] = 0;
			}
		}
	
		$data = array();
		foreach($d AS $key=>&$arr) {
			$ratios = array();
			foreach($arr AS $week=>$r) {
				$ratios[] = array('brand'=>$key, 'week'=>$week, 'amount'=>$r['approved'] / (1+$r['rejected']));
			}
			
			$data[] = array('brand_id'=>$key, "arRatio"=>$ratios);
		}
		
		return array(array("@id"=>$_SERVER['REQUEST_URI'], "data"=>$data));
	}
}

?>