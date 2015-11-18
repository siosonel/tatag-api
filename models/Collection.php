<?php 

class Collection extends Base {	
	public $items = array();
	public $pageOrder;
	protected $ltgt;
	public $itemsLimit = 50;
	protected $minID;
	protected $limitID;
	protected $limitIdArr;
	
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
	}
	
	function paginate($keyName, $items=null) {
		if (!$items AND isset($this->{$this->collectionOf})) $items = $this->{$this->collectionOf}; 
		if (!$items) $items = $this->items;
		
		$min = 99999999;
		$max = 0;
		
		foreach($items AS $r) {
			if ($r[$keyName] < $min) $min = $r[$keyName];
			if ($r[$keyName] > $max) $max = $r[$keyName];
		} //echo "[$min][$this->minID][$max][$this->limitID]";
		
	
		$query = http_build_query($_GET);
		$this->pageDesc($items,$min,$max,$query);
		//else $this->pageAsc($query);
		
		//reset @id as needed
		$currParams=array();
		if ($this->limitIdArr) $currParams[] = "limitIDs=". implode(",", $this->limitIdArr);
		if ($query) $currParams[] = $query;
		if ($currParams) $this->{"@id"} = $this->{"@id"} ."?". implode("&", $currParams);
	
		//embed forms only in the first page
		if ($this->limitIdArr OR $_GET['itemsLimit']) $this->embedForms = false;
		
		$this->setForms();
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
}