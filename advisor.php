<?php

$brand_id = (isset($_GET['brand_id']) AND $_GET['brand_id']) ? $_GET['brand_id'] : 0;
$revBudget = (isset($_GET['revBudget']) AND $_GET['revBudget']) ? $_GET['revBudget'] : 0;
$expBudget = (isset($_GET['expBudget']) AND $_GET['expBudget']) ? $_GET['expBudget'] : 0;
$inflow = (isset($_GET['inflow']) AND $_GET['inflow']) ? $_GET['inflow'] : 0;
$outflow = (isset($_GET['outflow']) AND $_GET['outflow']) ? $_GET['outflow'] : 0;
$numMembers = (isset($_GET['numMembers']) AND $_GET['numMembers']) ? $_GET['numMembers'] : 0;
$totalMemberHours = (isset($_GET['totalMemberHours']) AND $_GET['totalMemberHours']) ? $_GET['totalMemberHours'] : 0;

$mssg = array();

if ($revBudget+$expBudget < -1000) 
	$mssg[] = "The net balance is below an advised minimum of -1000 units.";
	
if ($revBudget AND abs($expBudget/$revBudget) < 0.5) 
	$mssg[] = "The absolute ratio of expense to revenue budget is less than the advised minimum of 0.5.";

if ($outflow AND abs($inflow/$outflow)<	0.5)
	$mssg[] = "The absolute ratio of inflo to outflow is less than the advised minimum of 0.5.";
	
$advise = $mssg ? "reject" : "accept";

exit(json_encode(array(
	"context"=>"---test---",
	"@graph"=>array(
		array(
			"advise"=> $advise,
			"message"=> $mssg
		)
	)
))); 
 
 