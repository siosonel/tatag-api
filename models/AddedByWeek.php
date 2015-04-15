<?php

require_once "models/Tally.php";

class AddedByWeek extends Tally {
	protected $metric = 'added';
	protected $txntype = 'np';
	protected $ft = 'f';
	protected $extraJoinCond = "JOIN brands b ON f.brand_id = b.brand_id";
	protected $interBrandCond = 'AND f.brand_id = f.brand_id';
	protected $groupByCond = "week, f.brand_id";
}