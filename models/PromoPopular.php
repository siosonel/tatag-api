<?php

require_once "models/PromoCollection.php";

class PromoPopular extends PromoCollection {
	private $cond;
	private $condVals;
	protected $relay;
	
	function __construct($data='') { 		
		$this->{"@type"} = "promoPopular";
		$this->{'@id'} = "$this->root/promo/popular";
		$this->table = "promos";
		$this->idkey = 'promo_id';
		$this->collectionOf = "promo";
		
		if (Router::$method=='add' OR Router::$method=='set') $this->translateInput($data);
		
		$this->pageOrder = "desc"; //prevents being reset
		$this->init($data);
		
		$this->okToFilterBy = array("brand_id", 'expires');
	}
	
	function get() {	
		$this->setAddlCond(); 
		//if (!$this->cond) $this->setForms();

		$graph = array($this);
		$tracked = array();
		$nestingRef = array(
			"brand_" => array(
				"@id" => "$this->root/brand/{id}/about", 
				"@type" => "brand"
			)
		);
	
		$sql = "SELECT promo_id, 
				p.brand_id AS brand_id, 
				brands.name AS brand_name, 
				p.name AS name, 
				p.description AS description, 
				amount, 
				imageURL, 
				infoURL, 
				p.created, 
				p.updated, 
				p.expires, 
				keyword,
				by_all_limit, 
				by_brand_limit, 
				by_user_limit, 
				by_user_wait,
				numUsers
			FROM promos p
			JOIN (
				SELECT promo_id, COUNT(DISTINCT from_user) as numUsers
				FROM records
				where status=7 AND TIMESTAMPDIFF(DAY,created,NOW())<100
				GROUP BY promo_id
				ORDER BY numUsers DESC
			) records USING (promo_id)
			JOIN relays USING (relay_id)
			JOIN brands USING (brand_id)
			WHERE promo_id $this->ltgt $this->limitID $this->cond AND MOD(promo_id,2)=1
				AND (expires IS NULL OR expires>NOW())
				AND by_user_limit > 0 AND by_brand_limit > 0 AND by_all_limit > 0
			ORDER BY promo_id $this->pageOrder
			LIMIT $this->itemsLimit";
		
		$items = DBquery::get($sql, $this->condVals);		
		Requester::detectMemberships();

		foreach($items AS &$r) {
			$r['id'] = $r['promo_id'];
			$r['@id'] = "$this->root/promo/". $r['promo_id'];
			$r['@type'] = 'promo';

			if (Requester::isMember($r['brand_id'])) {
				$r['edit'] = '/form/promo-edit';
			}

			$this->nestResources($r, $nestingRef, $graph, $tracked);
						
			$r['promoPage'] = Requester::$ProtDomain ."/ad/$r[promo_id]";
			$r['code'] = "$r[keyword]-$r[promo_id]";
			$r['payURL'] = Requester::$ProtDomain ."/for/$r[code]";
			$r['amount'] = round($r['amount'],2);
			
			if (!$r['imageURL']) {
				//$r['imageURL'] = "/ui/logo.php?brand=". $r['brand_name'];
				//$images[] = BrandLogo::wrap($r['brand_name'], $r['imageURL'], 'base64svg');
				//$r['imageURL'] = BrandLogo::base64svg($r['brand_name'], $r['imageURL']);
				//$r['imageURL'] = BrandLogo::dataURL($r['brand_name']);
				$r['imageTemplate'] = "/ui/logo.php";
			}
			
			if (!$r['expires']) $r['expires'] = "2019-12-31 11:59:59";

			$this->{$this->collectionOf}[] = $r['@id'];
			$p[] = $r;
			$graph[] = $r;
		}
		
		$this->setPageOf(array("brand_id", "for", "keyword", "id"));
		$this->paginate('promo_id', $p);
		
		if ($this->cond) return $graph;
		return array_merge($graph, array(BrandLogo::svgTemplate()));
	}
}



