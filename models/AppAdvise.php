<?php

class AppAdvise extends Base {
	function __construct($data='') { //move to advisor
		$this->from_brand = ($data AND isset($data->from_brand)) ? $data->from_brand : $_GET['from_brand'];
		$this->to_brand = ($data AND isset($data->to_brand)) ? $data->to_brand : $_GET['to_brand'];

		if (!$this->from_brand OR !$this->to_brand) Error::http(400, 
			"A non-zero integer value must be specified for from_brand (specified '$this->from_brand') and to_brand ('$this->to_brand') as GET query parameters."
		);

		$this->consumer_id = ($data AND isset($data->from_brand)) ? 0 : $this->getID();
		$this->{"@id"} = ($this->consumer_id) ? "$this->root/app/$this->consumer_id/advise" : "$this->root/app/advise";
		$this->{"@type"} = "appAdvise";
	}	

	function initTally() {
		$sql = "SELECT $this->from_brand AS brand_id, 
			b.advisor AS advisor, 
			COALESCE(p_start_bal, 0) AS p_start_bal,
			COALESCE(n_start_bal, 0) AS n_start_bal, 
			COALESCE(issued, 0) AS issued, 
			COALESCE(inflow, 0) AS inflow, 
			COALESCE(outflow, 0) AS outflow, 
			COALESCE(intrause, 0) AS intrause, 
			COALESCE(num_members, 0) AS num_members, 
			COALESCE(member_hours, 0) AS member_hours
		FROM brands b
		LEFT JOIN tallies t ON t.brand_id=$this->from_brand
			AND t.year=2015
			AND week=(SELECT max(week) FROM tallies WHERE brand_id=$this->from_brand) 
		WHERE b.brand_id=$this->to_brand"; //echo $sql;

		$r = DBquery::get($sql)[0];
		
		$this->tally = new stdClass();
		
		foreach($r AS $k=>$v) {
			if ($k=='advisor') $this->advisor = $v;
			else $this->tally->$k = $v;
		}

		if (!$this->advisor) $this->advisor = $this->consumer_id ? $this->consumer_id : 0;
	}

	function get() {
		$this->initTally();
		$this->calc($this->tally);		

		$cls = "Advisor$this->advisor"; 
		require_once "advisors/$cls.php";

		$Advisor = new $cls($this);
		$this->advise = $Advisor->advise();
		
		unset($this->tally);
		return array($this);
	}

	function calc($tally) { 
		$tally->revBal = $tally->n_start_bal + $tally->issued - $tally->inflow - $tally->intrause;
		$tally->expBal = $tally->p_start_bal + $tally->issued - $tally->outflow - $tally->intrause;

		$tally->budgetGapAbs = $tally->revBal - $tally->plannedBudget;
		$tally->budgetGapRatio = $tally->budgetGapAbs / ($tally->revBal+1);

		$tally->ioRatio = $tally->inflow / ($tally->outflow+1);
		$tally->ioGapAbs = $tally->inflow - $tally->outflow;
		$tally->ioGapRatio = $tally->ioGapRatio / ($tally->inflow+1);
		
		$tally->pnRatio = $tally->expBal / ($tally->revBal+1);
		$tally->pnGapAbs = $tally->expBal - $tally->revBal;
		$tally->pnGapRatio = $pnGapAbs / ($tally->revBal+1);
	}
}
