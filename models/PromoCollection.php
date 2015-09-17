<?php

class PromoCollection extends Collection {
	function __construct($data='') { 		
		$this->{"@type"} = "promoCollection";
		$this->{'@id'} = "$this->root/promo/collection".$params;
		$this->table = "promos";
		$this->idkey = 'promo_id';
		
		if (Router::$method=='add' OR Router::$method=='set') $this->translateInput($data);
		
		$this->pageOrder = "desc"; //prevents being reset
		$this->init($data);
		
		$this->okToFilterBy = array("brand_id", 'expires');
	}
	
	function translateInput($data) {
		if (!isset($data->holder_id)) Error::http(400, "A holder_id value is required in order to create a new brand promo.");
		if (!isset($data->amount) OR !is_numeric($data->amount)) Error::http(400, "Invalid amount value='$data->amount'.");
		
		$this->relay = json_decode('{
			"holder_id": '. $data->holder_id .',
			"amount_min": '. $data->amount .',
			"amount_max": '. $data->amount .',
			"txntype": "pn",
			"secret": "'. substr(dechex(mt_rand(0,100000)), 0, 5)  .'",
			"by_all_limit": 25, 
			"by_brand_limit": 5, 
			"by_user_limit": 2, 
			"by_user_wait": 24
		}');
		
		foreach($this->relay AS $prop=>&$val) {
			if (isset($data->$prop)) {
				$this->relay->$prop = $data->$prop;
				unset($data->$prop);
			}
		}
		
		$this->matchBrandToRelay();
	}
	
	function matchBrandToRelay() {
		$sql = "SELECT brand_id FROM accounts a JOIN holders USING (account_id) WHERE holder_id=?";
		$rows = DBquery::get($sql, array($this->relay->holder_id));
		if (!$rows) Error::http(403, "The holder_id value='$this->holder_id' was not found.");
		
		if (!$this->brand_id) $this->brand_id = $rows[0]['brand_id'];
		else if ($this->brand_id != $rows[0]['brand_id']) Error::http(403, "Brand# $this->brand_id does not own the account of holder #". $this->relay->holder_id .".");
	}
	
	function get() {	
		$this->setForms();
	
		$sql = "SELECT promo_id, p.brand_id AS brand_id, brands.name AS brand_name, 
				p.name AS name, p.description AS description, amount, imageURL, infoURL, p.created, p.updated, expires,
				by_all_limit, by_brand_limit, by_user_limit, by_user_wait
			FROM promos p
			JOIN relays USING (relay_id)
			JOIN brands USING (brand_id)
			WHERE promo_id $this->ltgt $this->limitID
			ORDER BY promo_id $this->pageOrder
			LIMIT $this->itemsLimit";
		
		$this->items = DBquery::get($sql);
		
		Requester::detectMemberships();
		
		foreach($this->items AS &$r) {
			$r['@id'] = "$this->root/promo/". $r['promo_id'];
			$r['@type'] = 'promo';
			$r['links']['payLink'] = Requester::$ProtDomain .'/pay?to=promo-'. $r['promo_id'] ."&amount=". $r['amount'] ."&brand=". urlencode($r['brand_name']);
			
			if (Requester::isMember($r['brand_id'])) {
				$r['links']['promo-edit'] = '/forms#promo-edit';
			}
		}
		
		$this->paginate('promo_id');
		return array($this);
	}
	
	function add() {
		require_once "models/HolderRelays.php";
		$HolderRelays = (new HolderRelays($this->relay))->add()[0];
		
		$this->okToAdd = array(
			"brand_id","name","description","amount","expires","imageURL","infoURL","relay_id"
		);
		
		$this->addKeyVal('brand_id',"$this->brand_id");
		$this->addKeyVal('expires','2016-12-31 00:00:00','ifMissing');
		$this->addKeyVal('imageURL','NULL','ifMissing');
		$this->addKeyVal('infoURL','NULL','ifMissing');
		$this->addKeyVal('relay_id',$HolderRelays->relay_id);
		
		$this->obj->promo_id = $this->insert();
		$this->obj->relay = $this->relay;
		$this->obj->relay_id = $HolderRelays->relay_id;
		return array($this->obj);
	}
}



