<?php

class BrandFilters extends Collection {
	function __construct($data='') { 
		$this->brand_id = $this->getID();
		if (!Requester::isBrandAdmin($this->brand_id)) Error::http(403, "Only members of brand #$this->brand_id can view or set its filters.");
		
		$this->{"@type"} = 'brandFilters';			
		$this->{"@id"} = "$this->root/brand/$this->brand_id/filter";
		$this->table = "filters";	
		$this->idkey = 'filter_id';
		
		$this->init($data);
		
		$this->okToAdd = array("brand_id", "other_id", "accept", "reason");	
		$this->okToSet = array("accept", "reason", "ended");	
		$this->okToFilterBy = array("filter_id");
	}
	
	function add() {		
		$this->addKeyVal('brand_id',$this->brand_id);
		$this->filter_id = $this->insert();
		return array($this);
	}
	
	function set() {		
		if (!$_GET) return $this->add();
		else {
			$this->update($_GET);
			return array($this);
		}
	}
	
	function get() {		
		$sql = "SELECT filter_id, f.brand_id as brand_id, other_id, o.name as other_name, accept, reason, f.created, f.ended
			FROM filters f
			JOIN brands o ON o.brand_id = f.other_id 
			WHERE f.brand_id=?
			ORDER BY filter_id $this->pageOrder
			LIMIT $this->itemsLimit";
		
		$this->items = DBquery::get($sql, array($this->brand_id));
		$this->paginate('filter_id');
		return array($this);
	}
}

?>