<?php

class BrandCollection extends Collection {
	function __construct($data='') {
		$this->{"@id"} = "/brand/collection";
		$this->{'@type'} = "brandCollection";
		$this->table = "brands";
		
		$this->init($data);
		
		$this->okToGet = array("brand_id", "name", "mission", "description");
		$this->okToAdd = array('name','mission','description','rating_min','rating_formula');
	}
	
	function add() { 
		include_once "models/Members.php";
		include_once "models/Accounts.php";
		include_once "models/Holders.php";
		
		$this->okToAdd = array('name','mission','description');
		$Brand =  $this->obj;
		$Brand->brand_id = $this->insert();		//print_r($Brand); print_r(Requester);
				
		$Brand->members[] = (new Members(json_decode('{
			"brand_id":'.$Brand->brand_id.', 
			"user_id":'. Requester::$user_id.', 
			"role":"admin",
			"hours":0
		}')))->add();
		
		$MainRev = (new Accounts(json_decode('{
			"brand_id": '. $Brand->brand_id .',
			"name": "Main Revenue",
			"authcode": "cftix",
			"unit": "hour",
			"sign": -1
		}')))->add();
		
		$MainExp = (new Accounts(json_decode('{
			"brand_id": '. $Brand->brand_id .',
			"name": "Main Expense",
			"authcode": "cftix",
			"unit": "hour",
			"sign": 1
		}')))->add();
		
		$Brand->accounts = array($MainRev, $MainExp);
		
		$Brand->holders[] =  (new Holders(json_decode('{
			"account_id": '. $MainRev->account_id .', 
			"user_id":'.Requester::$user_id.',
			"authcode": "cftix"
		}')))->add();
		
		$Brand->holders[] =  (new Holders(json_decode('{
			"account_id": '. $MainExp->account_id .', 
			"user_id":'.Requester::$user_id.',
			"authcode": "cftix"
		}')))->add();
		
		return array($Brand);
	}
	
	function get() { 		
		$sql = "SELECT COUNT(*) AS numBrands, MIN(created) AS earliest, MAX(created) AS latest FROM brands";
		$row = DBquery::get($sql);		
		if (!$row) return array($this);				
		foreach($row[0] AS $key=>$val) $this->$key = $val;
	
		$sql = "SELECT brand_id, name, created, updated, ended 
			FROM brands 
			WHERE brand_id $this->ltgt $this->limitID
			ORDER BY brand_id $this->pageOrder
			LIMIT $this->itemsLimit";
	
		$this->items = DBquery::get($sql);
		foreach($this->items AS &$b) $b['@id'] = "/brand/". $b['brand_id'] ."/about";
		
		$this->paginate("brand_id");
		return array($this);
	}
}

?>