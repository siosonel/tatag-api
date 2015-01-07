<?php
/*
Inherited class of common methods, including enforced input validation 
*/

class Base {
	protected $obj;
	protected $table;
	protected $cols;
	protected $okToAdd=array();
	protected $okToSet=array(); //values that may be set by an admin or user
	protected $okToFilterBy=array(); //parameters that may be used to filter the affected or returned rows
	protected $filterKey;
	protected $filterVal;
	
	protected $keyArr = array();
	protected $valArr = array();
	protected $quotedValArr = array();
	protected $paramMarker = array();
	
	function init($data) {	
		if (!$data) return;  
		
		$this->validate($data);
		$this->obj = $data;
		
		foreach($data AS $prop=>$val) $this->addKeyVal($prop, $val);		
	}
	
	//generalize the handling of collection or instance identifier from a particular URL structure
	function getID() {
		if (!isset(Router::$id)) return;
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
	
	function update() {
		//if (!$filter) Error::halt("A filter key=value is required when updating $this->table.");
		if ($bannedSet = array_diff($this->keyArr,$this->okToSet)) Error::halt("These parameters may not be set by the user: ". json_encode($bannedSet) .".");	
		//if (!in_array($this->filterKey,$this->okToFilterBy)) Error::halt("Invalid filter key: '$this->filterKey'.");
		
		$keyValStr = implode(",", $this->keyMarkerArr);
	
		//$sql = "UPDATE $this->table SET ($this->keyStr) VALUES ($this->valStr) WHERE $this->filterKey IN ($this->filterVals)";
		$sql = "UPDATE $this->table SET $keyValStr WHERE user_id=". Requester::$user_id;
		$rowCount = DBquery::set($sql, $this->valArr);
		if (!$rowCount) Error::http(500, "Affected rows=0.");	
	}
	
	function logChange($id='') { echo " logID=$id ";
		if (in_array($this->table, array('records'))) return;
	
		$cols = str_replace('created','NOW()',$this->cols);
		$filterKey = $this->filterKey;
		$filterVal = $id ? $id : $this->$filterKey;
	
		$sql = "INSERT INTO x_$this->table ($this->cols) SELECT $cols FROM $this->table WHERE $filterKey IN ($filterVal)";
		$rowCount = DBquery::insert($sql);
		if (!$rowCount) Error::http(500, "Affected rows=0.");		
		$this->logChange();
	}
	
	function getChanges($from,$to) {
		
	}
}

?>