<?php

class UserRatings extends Collection {
	function __construct($data='') {
		$this->user_id = $this->getID();
		if (Requester::$user_id != $this->user_id) Error::http(403, "User #$this->user_id ratings are not visible to other users.");
		
		$this->{"@type"} = 'userRatings';			
		$this->{"@id"} = "$this->root/user/$this->user_id/ratings";
		$this->table = "ratings";	
		$this->idkey = 'rating_id';
		$this->collectionOf = "rating";
		
		if (isset($data->brand_id)) $this->prepOther($data);
		else if (Router::$method=='POST') Error::http(400, "Missing value for brand_id parameter.");
		
		$this->pageOrder = "desc";
		$this->init($data);
		
		$this->okToAdd = array("brand_id", "user_id", "score", "reason");	
		$this->okToSet = array("score", "reason", "ended");	
		$this->okToFilterBy = array("rating_id");
	}
	
	function prepOther($data) {
		if (is_numeric($data->brand_id)) return;
		if (substr($data->brand_id,0,1) != "~") $data->brand_id = "~". $data->brand_id;
		$data->brand_id = substr($data->brand_id,0,200);
		
		$sql = "SELECT brand_id FROM brands WHERE name LIKE ? LIMIT 1";
		$rows = DBquery::get($sql, array($data->brand_id)); 
		
		if ($rows) {
			$data->brand_id = $rows[0]['brand_id'];
		}
		else {
			require_once "models/BrandCollection.php";
			
			$Brand = (new BrandCollection(json_decode('{
				"name": "'. $data->brand_id .'",
				"mission": "simulate a well-known brand for whitelisting or blacklisting",
				"description": "This is a simulated brand to be used for testing the tatag system.",
				"type_system": "sim"
			}')))->add()[0];
			
			$data->brand_id = $Brand->brand_id;
			if (!$data->brand_id) Error::http(500, "Failed to create or use a new brand_id for what you are rating.");
		}
	}
	
	function add() {	
		$this->addKeyVal('user_id',$this->user_id);
		$this->rating_id = $this->insert();
		return array($this);
	}
	
	function set() {	
		if (!$_GET AND !$this->detectExisting()) return $this->add();
		else {
			$this->delKeyVal('brand_id');
			$this->update($_GET);
			return array($this);
		}
	}
	
	function detectExisting() {
		$sql = "SELECT rating_id FROM ratings WHERE brand_id=? AND user_id=? AND ended IS NULL";
		$row = DBquery::get($sql, array($this->brand_id, $this->user_id));
		if ($row) {
			$_GET['rating_id'] = $row[0]['rating_id'];
			unset($this->brand_id);
			return 1;
		}
	}
	
	function get() {			
		$this->add = "/form/rating-add";
	
		$sql = "SELECT rating_id, r.brand_id AS brand_id, b.name AS brand_name, score, reason, r.created, r.ended
			FROM ratings r
			JOIN brands b ON b.brand_id = r.brand_id 
			WHERE r.user_id=? AND rating_id $this->ltgt $this->limitID
			ORDER BY rating_id $this->pageOrder
			LIMIT $this->itemsLimit";
		
		$items = DBquery::get($sql, array($this->user_id));
		foreach($items AS &$item) {
			$item['@id'] = $this->{"@id"} ."?rating_id=". $item['rating_id'];
			$item['edit'] = "$this->root/form/rating-edit";
		}
		
		$this->{$this->collectionOf} = $items;
		
		$this->paginate('rating_id');
		return array($this);
	}
}

?>