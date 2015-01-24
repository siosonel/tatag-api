<?php
/*
Inherited class of common methods, including enforced input validation 
*/

class Base {
	protected $obj;
	protected $table;
	protected $id;
	protected $cols;
	protected $okToAdd=array();
	protected $okToSet=array(); //values that may be set by an admin or user
	protected $okToGet=array();
	protected $okToFilterBy=array(); //parameters that may be used to filter the affected or returned rows
	protected $idkey;
	protected $filterVal;
	
	protected $keyArr = array();
	protected $valArr = array();
	protected $quotedValArr = array();
	protected $paramMarker = array();
	protected $keyMarkerArr = array();
	
	protected $forms = array();
	
	function init($data) {		
		if (!$data) return;  
		if (isset($data->ended) AND $data->ended + 300 > time()) $data->ended = date("Y-m-d H:i:s", $data->ended);
		
		$this->validate($data);
		$this->obj = $data;
		
		foreach($data AS $prop=>$val) $this->addKeyVal($prop, $val);		
	}
	
	//generalize the handling of collection or instance identifier from a particular URL structure
	function getID() {
		if (!isset(Router::$id) OR !is_numeric(Router::$id)) return;
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
		$keypos = array_search($key, $this->keyArr);	
			
		if ($keypos!==false) {
			$this->valArr[$keypos] = $val;
			$this->quotedValArr[$keypos] = ($val=='NULL') ? $val : DBquery::$conn->quote($val);
		}
		else {
			$this->keyArr[] = $key;
			$this->valArr[] = $val; 
			$this->quotedValArr[] = ($val=='NULL') ? $val : DBquery::$conn->quote($val);
			$this->paramMarker[] = "?";
			$this->keyMarkerArr[] = "$key=?";
		}
	}
	
	function insert() {
		if ($arr = array_diff($this->keyArr, $this->okToAdd)) Error::http(400, "Cannot insert values for these object properties: ". implode(",", $arr));
		if ($arr = array_diff($this->okToAdd, $this->keyArr)) Error::http(400, "Missing object properties: ". implode(",", $arr));
		
		$keyStr = implode(",", $this->keyArr);
		$valStr = implode(",", $this->paramMarker);
		
		$sql = "INSERT INTO $this->table ($keyStr,created) VALUES ($valStr,NOW())";
		$rowCount = DBquery::set($sql, $this->valArr);
		if (!$rowCount) Error::http(500, "Error: database query to create brand failed.");
		$id = DBquery::$conn->lastInsertId(); //echo " id=$id ";
		
		return $id;
	}
	
	function update($filter="", $vars=array()) { 
		if (!$filter) Error::http(500, "A filter key=value is required when updating $this->table.");
		if ($bannedSet = array_diff($this->keyArr,$this->okToSet)) Error::http(403, "These parameters may not be set by the user: ". json_encode($bannedSet) .".");	
		//if (!in_array($this->idkey,$this->okToFilterBy)) Error::halt("Invalid filter key: '$this->idkey'.");
		
		$keyValStr = implode(",", $this->keyMarkerArr);
		$valArr = array_merge($this->valArr, $vars);
		
		//$sql = "UPDATE $this->table SET ($this->keyStr) VALUES ($this->valStr) WHERE $this->idkey IN ($this->filterVals)";
		$sql = "UPDATE $this->table SET $keyValStr $filter";
		$rowCount = DBquery::set($sql, $valArr);
		if (!$rowCount) Error::http(500, "Affected rows=0.");	
	}
	
	function getViewable($idKey="", $idVals=array(), $relkey='') {
		if (!$idKey OR !$idVals) return array(); 
		
		foreach($idVals AS $v) {
			if (!is_numeric($v) OR !is_int(1*$v)) Error::http(500, "Queried IDs must be integers.");
		}
		
		$idVals = implode(",", $idVals);
		$cols = implode(",", $this->okToGet);
	
		$sql = "SELECT $cols FROM $this->table WHERE $idKey IN ($idVals)"; //echo "\n$sql\n";
		$rows =  DBquery::get($sql);
		
		$rekeyed = array();
		$relVals = array();
		
		foreach($rows AS &$r) {
			$rekeyed["/$this->table/". $r[$this->idkey]] = $r;
			if ($relkey AND is_numeric($r[$relkey])) $relVals[] = 1*$r[$relkey];
		}
		
		return array($rekeyed, $relVals);
	}
	
	function setForms() {
		$actions = Requester::$defs->{$this->{'@type'}}->actions;
		if (!$actions) return;
		if (!$this->actions) $this->actions = array();
		
		foreach($actions AS $form) {
			if (!in_array($form->{'@id'}, $this->actions)) $this->actions[] = $form->{"@id"};
			
			if (!Requester::$graphRefs[$link]) {				
				Requester::$graph[] = $form;
				Requester::$graphRefs[$form->{"@id"}]++;
			}
		} 
	}
	
	function logChange($id='') {
		if (in_array($this->table, array('records'))) return;
	
		$cols = str_replace('created','NOW()',$this->cols);
		$idkey = $this->idkey;
		$filterVal = $id ? $id : $this->$idkey;
	
		$sql = "INSERT INTO x_$this->table ($this->cols) SELECT $cols FROM $this->table WHERE $idkey IN ($filterVal)";
		$rowCount = DBquery::insert($sql);
		if (!$rowCount) Error::http(500, "Affected rows=0.");		
		$this->logChange();
	}
	
	function getChanges($from,$to) {
		
	}
}

?>