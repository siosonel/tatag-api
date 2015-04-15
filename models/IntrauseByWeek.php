<?php

require_once "models/Tally.php";

class IntrauseByWeek extends Tally {
	protected $metric = 'intrause';
	protected $txntype = 'pn';
	protected $ft = 'f';
	protected $extraJoinCond = "JOIN brands b ON f.brand_id = b.brand_id";
	protected $interBrandCond = 'AND f.brand_id = t.brand_id';
	protected $groupByCond = "week, f.brand_id";
}