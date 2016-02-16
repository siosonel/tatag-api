<?php 

class Collection extends Base {	
	public $items = array();
	public $pageOrder;
	protected $ltgt;
	public $itemsLimit = 50;
	protected $minID = 999999999;
	protected $maxID = 0;
	protected $limitID;
	protected $limitIdArr;
	protected $minUpdated = 0;
	protected $cutoffID = 0;	
	public $collectionOf; //the items-equivalent link relation name 
	public $pageOf; //the @id of the collection home
	
	function setLimitID() {		
		//will not reset pageOrder if child class has set pageOrder
		if (!isset($this->pageOrder)) $this->pageOrder = isset($_GET['pageOrder']) ? strtolower($_GET['pageOrder']) : "asc";
		$this->ltgt = $this->pageOrder=="desc" ? "<" : ">";
		
		if (isset($_GET['itemsLimit'])) {
			if (!is_numeric($_GET['itemsLimit'])) Error::http(400, "Invalid value for the itemsLimit parameter in the URL.");
			$itemsLimit = $_GET['itemsLimit'];
			if ($itemsLimit < 101 AND $itemsLimit > 1) $this->itemsLimit = $itemsLimit;   
		}
		
		if (!isset($_GET['limitIDs'])) {
			$this->limitIdArr = array();
			$this->limitID = $this->pageOrder=="desc" ? 99999999 : 0;
		}
		else {
			$this->limitIdArr = explode(",", $_GET['limitIDs']);
			$this->limitID = $this->limitIdArr[count($this->limitIdArr)-1];
			unset($_GET['limitIDs']);
		}
		
		
		if (isset($_GET['minUpdated'])) {
			if (is_numeric($_GET['minUpdated'])) $this->minUpdated = $_GET['minUpdated']; 
			unset($_GET['minUpdated']);
		}
		
		if (isset($_GET['cutoffID'])) {
			if (is_numeric($_GET['cutoffID'])) $this->cutoffID = $_GET['cutoffID']; 
			unset($_GET['cutoffID']);
		}
	}
	
	function paginate($keyName, $items=null) {
		if (!$this->pageOf) $this->pageOf = $this->setPageOf();
		$maxUpdated = 0;
		
		if (!$items AND isset($this->{$this->collectionOf})) $items = $this->{$this->collectionOf}; 
		if (!$items) $items = $this->items; 
	
		foreach($items AS $r) {
			if (is_array($r)) {
				if ($r[$keyName] < $this->minID) $this->minID = $r[$keyName];
				if ($r[$keyName] > $this->maxID) $this->maxID = $r[$keyName];
				if ($r['updated'] > $this->minUpdated) $this->minUpdated = $r['updated'];
			}	
			else if ($r->$keyName) {
				if ($r->$keyName < $this->minID) $this->minID = $r->$keyName;
				if ($r->$keyName > $this->maxID) $this->maxID = $r->$keyName;
				if ($r->updated > $this->minUpdated) $this->minUpdated = $r->updated;
			}
		}
	
		$query = http_build_query($_GET);
		$this->pageDesc($items,$this->minID,$this->maxID,$query);
		//else $this->pageAsc($query);
		
		//reset @id as needed
		$currParams=array();
		if ($this->limitIdArr) $currParams[] = "limitIDs=". implode(",", $this->limitIdArr);
		if ($query) $currParams[] = $query;
		if ($currParams) $this->{"@id"} = $this->{"@id"} ."?". implode("&", $currParams);
	
		//embed forms only in the first page
		if ($this->limitIdArr OR $_GET['itemsLimit']) $this->embedForms = false;
		
		$this->setForms();
		
		//
		if (!count($items)) {
			$updatedItemsLink = "$this->pageOf?minUpdated=$this->minUpdated&cutoffID=$this->cutoffID";  
		}		
		else {
			if (!$this->cutoffID) $this->cutoffID = $this->pageOrder=='desc' ? $this->minID : $this->maxID;
			$updatedItemsLink = "$this->pageOf?minUpdated=$this->minUpdated&cutoffID=$this->cutoffID";  
		}
		
		PhlatMedia::$hints[$this->pageOf]['refresh'] = $updatedItemsLink;
	}
	
	function pageDesc($items,$min,$max,$query) {
		$prevNextVal = $this->pageOrder=='desc' ? $min : $max;
		$former = $this->pageOrder=='desc' ? "next" : "prev";
		$latter = $this->pageOrder=='desc' ? "prev" : "next";
		
		//next page, if any
		if ($this->limitIdArr) {
			if (count($this->limitIdArr)>1) {
				$nextVal = array_diff($this->limitIdArr, array($this->limitID));
				$nextParam = $nextVal ? "limitIDs=". implode(",", $nextVal) : "";
				$this->$former =  $this->{"@id"} ."?$nextParam";
				if ($query) $this->$former .= "&$query";
			}
			else $this->$former =  $query ? $this->{"@id"} ."?$query" : $this->{"@id"};
		}
		
		//previous page, if any
		if (count($items) >= $this->itemsLimit AND $prevNextVal != $this->limitID) {
			$prevNext = $this->limitIdArr;
			$prevNext[] = $prevNextVal;
			$this->$latter = $this->{"@id"} ."?limitIDs=". implode(",", $prevNext); 
			if ($query) $this->$latter .= "&$query";
		}
	}


	function setPageOf($okToUse=array()) {
		$params = array();
		foreach($_GET AS $k=>$v) {
			if (in_array($k,$okToUse)) $params[$k] = $v;
		}

		$delim = $params ? "?" : "";
		$this->pageOf = $this->{'@id'} . $delim . http_build_query($params);
	}
}