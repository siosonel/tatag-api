<?php

class TeamOrders extends Collection {
	function __construct($data='') {
		$this->{"@type"} = "teamOrders";
		$this->brand_id = $this->getID();
		if (!Requester::isMember($this->brand_id)) Error::http(403, "Only members or admins of brand #$this->brand_id can view its orders.");
		
		$this->{'@id'} = "$this->root/team/$this->brand_id/orders";
		$this->table = "records";	
		$this->collectionOf = "order";	
		$this->init($data);		
		$this->okToFilterBy = array("record_id");
	}

	function set() {
		$this->okToSet = array("order_step");

		$this->update($_GET);
		return array($this->obj);
	}
		
	function get() {
		$graph = array($this);
		$tracked = array();
		$nestingRef = array(
			"promo_" =>  array(
				"@id" => "$this->root/promo/{id}", 
				"@type" => "promo"
			),
			"user_" =>  array(
				"@id" => "$this->root/user/{id}/about", 
				"@type" => "user"
			),
			"from_" => array(
				"@id" => "$this->root/brand/{id}/about", 
			)
		);

		$sql = "SELECT txntype, 
			record_id, 
			r.created, 
			bf.brand_id AS from_id,
			bf.name AS from_name,
			to_acct AS `to`, 
			r.amount,
			r.`note`, 
			r.order_step, 
			r.updated AS updated,
			r.promo_id AS promo_id,
			p.name AS promo_name,
			r.from_user AS user_id
		FROM records r 
		JOIN accounts af ON r.from_acct = af.account_id 
		JOIN brands bf ON af.brand_id = bf.brand_id
		JOIN accounts at ON r.to_acct = at.account_id
		LEFT JOIN promos p USING (promo_id)
		WHERE promo_id!=0 AND txntype='pn' AND at.brand_id=? AND order_step BETWEEN ? AND ?
		GROUP BY record_id
		ORDER BY record_id DESC LIMIT 50";
		$items = DBquery::get($sql, array($this->brand_id, 0, 9));

		foreach($items AS &$o) {
			$o['@id'] = "$this->root/team/$this->brand_id/orders?record_id=$o[record_id]";
			$o['update'] = "/form/order-update";
			
			$this->nestResources($o, $nestingRef, $graph, $tracked, $skip);

			$this->order[] = $o['@id'];
			$graph[] = $o;
		}
		
		$this->paginate('record_id');
		return $graph;
	}
}
