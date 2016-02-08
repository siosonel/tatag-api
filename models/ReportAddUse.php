<?php

class ReportAddUse extends Collection {
	function __construct($data='') {
		$this->{'@id'} = "$this->root/report/addUse";
		$this->{'@type'} = "dataset";
		$this->collectionOf = "data";
		
		$this->itemsLimit = 1000;
		$this->pageOrder = "desc";

		$this->init($data);
	}

	function get() {
		$sql = "SELECT report_id, txntype, from_brand, to_brand, amount, UNIX_TIMESTAMP(max_updated) AS updated, keyword, status
			FROM reports 			
			WHERE report_id $this->ltgt $this->limitID
			ORDER BY report_id $this->pageOrder
			LIMIT $this->itemsLimit";

		$rows = DBquery::get($sql);
		$this->data = $rows;
		$this->paginate('report_id');
		return array($this);
	}
}

