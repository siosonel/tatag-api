<?php

require_once "models/Tally.php";

class InflowByWeek extends Tally {
	protected $metric = 'inflow';
	protected $txntype = 'pn';
	protected $ft = 't';
	protected $extraJoinCond = "JOIN brands b ON t.brand_id = b.brand_id";
	protected $interBrandCond = 'AND f.brand_id != t.brand_id';
	protected $groupByCond = "week, t.brand_id";
}