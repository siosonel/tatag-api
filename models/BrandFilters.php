<?php

class BrandFilters extends Collection {
	function __construct($data='') { 
		$this->brand_id = $this->getID();
		if (!Requester::isBrandAdmin($this->brand_id)) Error::http(403, "Only members of brand #$this->brand_id can view or set its filters.");
		
		$this->{"@type"} = 'brandFilters';			
		$this->{"@id"} = "$this->root/brand/$this->brand_id/filters";
		$this->table = "filters";	
		$this->idkey = 'filter_id';
		
		if (isset($data->other_id)) $this->prepOther($data);
		
		$this->init($data);
		
		$this->okToAdd = array("brand_id", "other_id", "accept", "reason");	
		$this->okToSet = array("accept", "reason", "ended");	
		$this->okToFilterBy = array("filter_id");
	}
	
	function prepOther($data) {
		if (is_numeric($data->other_id)) return;
		
		$sql = "SELECT brand_id FROM brands WHERE name=?";
		$rows = DBquery::get($sql, array($data->other_id));
		
		if ($rows) {
			$data->other_id = $rows[0]['brand_id'];
		}
		else {
			require_once "models/BrandCollection.php";
			
			$Brand = (new BrandCollection(json_decode('{
				"name": "'. substr($data->other_id,0,45) .'",
				"mission": "simulate a well-known brand for whitelisting or blacklisting",
				"description": "This is a simulated brand to be used for testing the tatag system."
			}')))->add()[0];
			
			$data->other_id = $Brand->brand_id;
		}
	}
	
	function add() {	
		$this->addKeyVal('brand_id',$this->brand_id);
		$this->filter_id = $this->insert();
		return array($this);
	}
	
	function set() {	
		if (!$_GET AND !$this->detectExisting()) return $this->add();
		else {
			$this->delKeyVal('other_id');
			$this->update($_GET);
			return array($this);
		}
	}
	
	function detectExisting() {
		$sql = "SELECT filter_id FROM filters WHERE brand_id=? AND other_id=?";
		$row = DBquery::get($sql, array($this->brand_id, $this->other_id));
		if ($row) {
			$_GET['filter_id'] = $row[0]['filter_id'];
			unset($this->other_id);
			return 1;
		}
	}
	
	function get() {		
		$sql = "SELECT filter_id, f.brand_id AS brand_id, o.name AS other_id, accept, reason, f.created, f.ended
			FROM filters f
			JOIN brands o ON o.brand_id = f.other_id 
			WHERE f.brand_id=?
			ORDER BY filter_id $this->pageOrder
			LIMIT $this->itemsLimit";
		
		$this->items = DBquery::get($sql, array($this->brand_id));
		foreach($this->items AS &$item) $item['@id'] = $this->{"@id"} ."?filter_id=". $item['filter_id']; 
		$this->paginate('filter_id');
		return array($this);
	}
}

?>