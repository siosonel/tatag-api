<?php
header('content-type: text/plain');
set_time_limit(0);
error_reporting(error_reporting() & ~ E_NOTICE);

chdir("../");
include "utils/Utils.php";
include "config-public.php";

if (!isset($_GET['db'])) Error::http(400, "Please specify a db='name' value in the URL query parameter."); 

$targets = explode(",", $_GET['db']);
$targets = array_intersect($targets, array_keys($dbs));

if (!$targets) Error::http(403, "The db name value(s) did not match any of the $dbs keys in config file.");

DBquery::init($dbs, $targets);
	
foreach($targets AS $alias) {
	DBquery::select_db($alias);
	if (!file_exists("tools/data_$alias.sql")) Error::http(403, "Data file not found for db alias='$alias'."); 

	DBquery::set(file_get_contents("tools/db_schema.sql"));
	DBquery::set(file_get_contents("tools/data_$alias.sql"));
}


