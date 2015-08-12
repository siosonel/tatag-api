<?php

class ArRatioByBrand extends collection {
	function __construct() {
		$this->week = date('W');
	}
	
	function get() {
		$sql  = "select b.brand_id, 'approved' AS type,
			COALESCE(numApproved,0) AS total
		from brands b
		LEFT JOIN (
			SELECT brand_id, COUNT(*) as numApproved
			FROM records r
			JOIN accounts f ON f.account_id=r.from_acct
			WHERE status=7 and txntype='pn'
			GROUP BY brand_id
		) approved ON approved.brand_id = b.brand_id
		UNION ALL
		select b.brand_id, 'rejected' AS type, 
			COALESCE(numrejected,0) AS total
		from brands b
		LEFT JOIN (
			SELECT brand_id, COUNT(*) as numrejected
			FROM records r
			JOIN accounts f ON f.account_id=r.from_acct
			WHERE status=7 and txntype='pn'
			GROUP BY brand_id
		) rejected ON rejected.brand_id = b.brand_id";
		
		$rows = DBquery::get($sql);
		
		$d = [];
		
		foreach($rows AS &$r) {	
			$d[$r['brand_id']][$r['type']] = $r['total'];
		}
			
	
		$data = array();
		foreach($d AS $key=>&$arr) {
			$ratios = array();
			$ratios[] = array('brand'=>$key, 'amount'=>$arr['approved'] / (1+$arr['rejected']));
			$data[] = array('brand_id'=>$key, "arRatio"=>$ratios);
		}
		
		return array(array("@id"=>$_SERVER['REQUEST_URI'], "data"=>$data));
	}
}

?>