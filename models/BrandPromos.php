<?php

require_once "models/PromoCollection.php";

class BrandPromos extends PromoCollection {
	function __construct($data='') { 		
		$this->brand_id = $this->getID();
		
		if (!Requester::isMember($this->brand_id)) Error::http(403, "The requester is not a member of brand #$this->brand_id.");
		
		$this->{"@type"} = "brandPromos";
		$params = $_GET ? '?'. http_build_query($_GET) : '';
		$this->{'@id'} = "$this->root/brand/$this->brand_id/promos".$params;
		$this->table = "promos";
		$this->idkey = 'promo_id';
		$this->collectionOf = "promo";
		
		if (Router::$method=='add' OR Router::$method=='set') $this->translateInput($data);
		
		$this->pageOrder = "desc"; //prevents being reset
		$this->init($data);
		
		$this->okToFilterBy = array("brand_id", "promo_id", 'expires');
	}
	
	function set() {	
		return $this->add();
	}
	
	function get() {
		$graph = array($this);
		$tracked = array();
		$this->add = "$this->root/form/promo-add";
		$this->setFilters($_GET);		
		
		$nestingRef = array(
			"brand_" => array(
				"@id" => "$this->root/team/{id}", 
				"@type" => "brand"
			),
			"relay_" => array(
				"@id" => "$this->root/relay/{id}", 
				"@type" => "relay",
				"edit" => "/form/relay-edit"
			)
		);
		
		$sql = "SELECT promo_id AS id, brand_id, brands.name AS brand_name, 
				p.name AS name, p.description AS description, amount, imageURL, infoURL, 
				p.created, p.updated, expires, 
				relay_id, keyword, 
				by_all_limit, by_brand_limit, by_user_limit, by_user_wait
			FROM promos p
			JOIN relays r USING (relay_id)
			JOIN brands USING (brand_id)
			WHERE brand_id=$this->brand_id $this->filterCond AND promo_id $this->ltgt $this->limitID
			ORDER BY id ASC
			LIMIT $this->itemsLimit";
		
		$items = DBquery::get($sql, $this->filterValArr);		
		
		foreach($items AS &$r) {
			$this->nestResources($r, $nestingRef, $graph, $tracked);
			
			$r['@id'] = "$this->root/promo/". $r['id'];
			$r['@type'] = 'promo';
			$r['payLink'] = Requester::$ProtDomain ."/for/$r[keyword]-$r[id]";
			$r['recipientToken'] = "$r[keyword]-$r[id]";
			
			$r['promoPage'] = Requester::$ProtDomain ."/ad/$r[id]";
			if (!$r['infoURL']) $r['infoURL'] = $r['promoPage'];
			
			$r['edit'] = '/form/promo-edit';
			
			$relayHoldings = array();
			
			if (in_array($r['relay_id'],$relayHoldings) OR Requester::isRelayHolder($r['relay_id'])) {
				$relayHoldings[] = $r['relay_id'];
				$r['relay-edit'] = '/form/relay-edit';
				$r['relay-edit-target'] = "/relay/".$r['relay_id'];
			}
			
			$r['brand'] = "$this->root/brand/$this->brand_id";
			$this->{$this->collectionOf}[] = $r;
		}
		
		$this->paginate('promo_id');
		return $graph;
	}
}

