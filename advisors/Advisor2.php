<?php

class Advisor2 {
	function __construct($info='') {
		$this->configFile = "advisors/config/$info->advisor.json";
		$this->config = file_exists($this->configFile) ? json_decode(file_get_contents($this->configFile)) : new stdClass();
	}

	function advise() {
		$t = $this->tally;
		if ($t->revBal+$t->expBal < $this->config->maxBalDiff) 
			$mssg[] = "The net balance is below an advised minimum of -1000 units.";
			
		if ($t->revBal AND abs($t->expBal/$t->revBal) < $this->config->maxNPratio) 
			$mssg[] = "The absolute ratio of expense to revenue budget is less than the advised minimum of 0.5.";

		if ($t->outflow AND abs($t->inflow/$t->outflow) < $this->config->maxIOratio)
			$mssg[] = "The absolute ratio of inflo to outflow is less than the advised minimum of 0.5.";

		return $mssg ? array("status"=>-20, "message"=>$mssg) : array("status"=>7);
	}
}

