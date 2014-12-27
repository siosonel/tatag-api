<?php
header('content-type: text/plain');
set_time_limit(0);
error_reporting(error_reporting() & ~ E_NOTICE);

$dumpfile = 'nplite.sql'; 
$step = isset($_GET['step']) ? $_GET['step'] : '';

chdir("../");
include "models/Utils.php";
include "config.php";

DBquery::init($dbs, array("tatagtest"));	

if ($step=='dump') { 
	echo shell_exec("mysqldump --user=$USER --password=$PWD --host=127.0.0.1 nplite > $dumpfile");
}
else if ($step=='upload') { 
	$sql = file_get_contents("tools/tatag.sql");
	DBquery::post($sql);
} 
else exit("Invalid step='$step'.");

