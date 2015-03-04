<?php
error_reporting(E_ALL ^ E_NOTICE);
header('content-type: text/plain');
chdir("../../");

require_once "config.php";
require_once "models/Requester.php";
require_once "models/UserCollection.php";
require_once "models/BrandCollection.php";
require_once "tools/sim/SimPerson.php";

Error::log("tools/sim/error.log");

//reset database
DBquery::init($dbs, array("tatagsim"));
$schema = file_get_contents("tools/db_schema.sql");
if (SITE=='dev') $schema = str_replace("InnoDB", "MEMORY", $schema);
DBquery::set($schema);


//settings
require_once "tools/sim/settings_1.php";

//create users and brands
//$sharedCoIDs = seedCoIDs();
$Brands = array();
$Persons = array();
for($p=0; $p<NUM_PERSONS; $p++) $Persons[] = new SimPerson(new stdClass());

//simulate transactions
$userIDs = range(0, NUM_PERSONS-1);
for($c=0; $c<CYCLE_MAX; $c++) {
	shuffle($userIDs);	
	for($u=0; $u<NUM_PERSONS; $u++) $Persons[$userIDs[$u]]->startCycle($c);	
	
	for($t=0; $t<TICK_MAX; $t++) {
		shuffle($userIDs);	
		for($u=0; $u<NUM_PERSONS; $u++) $Persons[$userIDs[$u]]->useBudget($c,$t);
	}
}

print_r(json_decode(json_encode($Persons)));

function seedCoIDs() {
	$IDs = range(0,NUM_PERSONS-1);
	shuffle($IDs);
	
	foreach($IDs AS $k=>$i) {
		$coIDs[] = $i;
		for($j=0; $j<$k; $j++) $coIDs[] = $i;
	} 
	
	shuffle($coIDs); //echo json_encode($this->coIDs) ."\n";
	return $coIDs;
}

