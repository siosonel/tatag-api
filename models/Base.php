<?php
/*
Inherited class of common methods, including enforced input validation 
*/

class Base {
	protected $root='';
	protected $obj;
	protected $table;
	protected $id;
	protected $cols;
	
	protected $okToAdd=array();
	protected $okToSet=array(); //values that may be set by an admin or user
	protected $okToGet=array();
	protected $okToFilterBy=array(); //parameters that may be used to filter the affected or returned rows
	
	protected $idkey;
	
	protected $filters;
	protected $filterCond;
	protected $filterValArr;
	
	protected $removeFromInput=array();
	
	protected $keyArr = array();
	protected $valArr = array();
	protected $quotedValArr = array();
	protected $paramMarker = array();
	protected $keyMarkerArr = array();
	
	protected $forms = array();
	protected $embedForms = true;
	
	function init($data) {		
		if (!$data) return;  
		
		$this->validate($data);
		$this->obj = $data;
		
		foreach($data AS $prop=>$val) $this->addKeyVal($prop, $val);		
		$this->filters = $_GET;
		
		//for collections
		if (method_exists($this, 'setLimitID')) $this->setLimitID();
	}
	
	//generalize the handling of collection or instance identifier from a particular URL structure
	function getID($paramAlt="") {
		if (!isset(Router::$id) OR !is_numeric(Router::$id)) {
			if ($paramAlt AND isset($_GET[$paramAlt]) AND $_GET[$paramAlt]) {
				Router::$id = $_GET[$paramAlt];
				unset($_GET[$paramAlt]);
			}
			else return;
		}
		
		return str_replace("-",",",Router::$id);
	}
	
	function validate($data='') {		
		if (!$data) return;
		
		foreach($data AS $key=>$val) {
			
		} 
	}
	
	function addKeyVal($key, $val, $restrict='') {
		if ($restrict=='ifMissing' AND in_array($key, $this->keyArr)) return;
		
		$this->$key = $val;
		if (in_array($key, $this->removeFromInput)) return;
		
		$keypos = array_search($key, $this->keyArr);	
			
		if ($keypos!==false) {
			$this->valArr[$keypos] =($val=='NULL') ? NULL : $val;
			$this->quotedValArr[$keypos] = ($val=='NULL') ? $val : DBquery::$conn->quote($val);
		}
		else {
			$this->keyArr[] = $key;
			$this->valArr[] = ($val==='NULL') ? NULL : $val; 
			$this->quotedValArr[] = ($val==='NULL') ? $val : DBquery::$conn->quote($val);
			$this->paramMarker[] = "?";
			$this->keyMarkerArr[] = "$key=?";
		}
	}
	
	function insert() {
		if ($arr = array_diff($this->keyArr, $this->okToAdd)) Error::http(403, "Cannot insert values for these object properties: ". implode(",", $arr));
		if ($arr = array_diff($this->okToAdd, $this->keyArr)) Error::http(403, "Missing object properties: ". implode(",", $arr));
		
		$keyStr = implode(",", $this->keyArr);
		$valStr = implode(",", $this->paramMarker);
		
		$sql = "INSERT INTO $this->table ($keyStr,created) VALUES ($valStr,NOW())"; 
		$rowCount = DBquery::set($sql, $this->valArr);
		if (!$rowCount) Error::http(500, "Error: database query to create brand failed.");
		$id = DBquery::$conn->lastInsertId(); //echo " id=$id ";
		
		return $id;
	}
	
	function update($arr=array()) { 		
		if ($arr) $this->setFilters($arr);
		if (!$this->filterCond OR !$this->filterValArr) Error::http(403, "A filter key=value is required when updating $this->table information.");
		if ($bannedSet = array_diff($this->keyArr,$this->okToSet)) Error::http(403, "These parameters may not be set by the user: ". json_encode($bannedSet) .".");	
		
		$keyValStr = implode(",", $this->keyMarkerArr);
		$valArr = array_merge($this->valArr, $this->filterValArr);
		
		$sql = "UPDATE $this->table SET $keyValStr, updated=NOW() WHERE $this->filterCond"; //exit(json_encode($sql .'... '. json_encode($valArr)));
		$rowCount = DBquery::set($sql, $valArr);
		//if (!$rowCount) Error::http(500, "Affected rows=0.");	
	}
	
	function setFilters($arr) {
		$cond = array();
		$filterKeys = array_keys($arr);
		$valArr = array();
		$notOk = array_diff($filterKeys, $this->okToFilterBy);		
		if ($notOk) Error::http(403, "These filter keys are not allowed: ". json_encode($notOk) .".");
		
		foreach($arr AS $key=>$val) {
			$filterVals = explode(",", $val);
			$markers = implode(',', array_pad(array(), count($filterVals), "?"));
			$cond[] = "$key IN ($markers)";
			$valArr = array_merge($valArr, $filterVals);
		}
		
		$this->filterCond = implode(' AND ', $cond);
		$this->filterValArr = $valArr;
	}
	
	function getViewable($arr=array()) {	
		$cols = implode(",", $this->okToGet);
		$sql = "SELECT $cols FROM $this->table WHERE $this->filterCond"; 
		return DBquery::get($sql, $this->filterValArr);
	}
	
	function setForms($relatedType='', $relatedForms=array()) { 
		$type = ($relatedType) ? $relatedType : $this->{'@type'};
	
		$actions = Requester::$defs->$type->actions;
		if (!isset($this->actions)) $this->actions = array();
		
		if ($actions) {
			foreach($actions AS $form) {
				if ($relatedForms) unset($form->examples);
				$link = trim($form->{'@id'});
				
				if (!$relatedForms OR in_array($link,$relatedForms)) { 
					if (!$relatedType AND !in_array($link, $this->actions)) $this->actions[] = $link; 
					
					if ($this->embedForms AND !Requester::$graphRefs[$link]) {
						Requester::$graph[] = $form;
						Requester::$graphRefs[$link]++;
					}
				}
			}
		}
	
		if (!$relatedType AND Requester::$defs->$type->relatedActions) {
			foreach(Requester::$defs->$type->relatedActions AS $t => $a) $this->setForms($t, $a);
		}
	}
}
