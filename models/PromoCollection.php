<?php

class PromoCollection extends Collection {
	function __construct($data='') { 		
		$this->{"@type"} = "promoCollection";
		$this->{'@id'} = "$this->root/promo/collection".$params;
		$this->table = "promos";
		$this->idkey = 'promo_id';
		
		$this->pageOrder = "desc"; //prevents being reset
		$this->init($data);
		
		//$this->okToFilterBy = array("brand_id", 'expires');
	}
	
	function get() {	
		$this->setForms();
	
		$sql = "SELECT promo_id, brand_id, name, description, amount, qty, imageURL, infoURL, created, updated, expires
			FROM promos p
			WHERE promo_id $this->ltgt $this->limitID AND qty>0
			ORDER BY promo_id $this->pageOrder
			LIMIT $this->itemsLimit";
		
		$this->items = DBquery::get($sql);
		
		foreach($this->items AS &$r) {
			$r['@id'] = "$this->root/promo/". $r['promo_id'];
			$r['@type'] = 'promo';
		}
		
		$this->paginate('promo_id');
		return array($this);
	}
}



