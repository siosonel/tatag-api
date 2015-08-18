<?php

class BrandPromos extends Collection {
	function __construct($data='') { 		
		$this->brand_id = $this->getID();
		
		if (!Requester::isMember($this->brand_id)) Error::http(403, "The requester is not a member of brand #$this->brand_id.");
		
		$this->{"@type"} = "brandPromos";
		$params = $_GET ? '?'. http_build_query($_GET) : '';
		$this->{'@id'} = "$this->root/brand/$this->brand_id/promos".$params;
		$this->table = "promos";
		$this->idkey = 'promo_id';
		
		$this->pageOrder = "desc"; //prevents being reset
		$this->init($data);
		
		$this->okToFilterBy = array("brand_id", "promo_id", 'expires');
	}
	
	function add() {
		if (!Requester::isBrandAdmin($this->brand_id)) Error::http(403, "The requester is not an admin of brand #$this->brand_id.");
		
		$this->okToAdd = array(
			"brand_id","name","description","amount","qty","expires","imageURL","infoURL"
		);
		
		$this->addKeyVal('brand_id',"$this->brand_id");
		$this->addKeyVal('expires','2016-12-31 00:00:00','ifMissing');
		$this->addKeyVal('imageURL','NULL','ifMissing');
		$this->addKeyVal('infoURL','pn','ifMissing');
		
		$this->promo_id = $this->insert();		
		return array($this->promo);
	}
	
	function set() {	
		return $this->add();
	}
	
	function get() {
		$this->setFilters($_GET);		
	
		$sql = "SELECT promo_id, brand_id, created, updated, expires
			FROM promos p
			WHERE brand_id=$this->brand_id $this->filterCond AND promo_id < $this->limitID
			ORDER BY promo_id ASC
			LIMIT $this->itemsLimit";
		
		$this->items = DBquery::get($sql, $this->filterValArr);
		
		foreach($this->items AS &$r) {
			$r['@id'] = "$this->root/promo/". $r['promo_id'];
			$r['@type'] = 'promo';
			$r['links']['promo-edit'] = '/forms#promo-edit';
		}
		
		$this->paginate('promo_id');
		$this->links['promo-add'] = "/forms#brand-promos-add";
		return array($this);
	}
}

