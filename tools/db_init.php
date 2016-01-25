<?php
header('content-type: text/plain');
set_time_limit(0);
error_reporting(error_reporting() & ~ E_NOTICE);

$dumpfile = 'nplite.sql'; 
$step = isset($_GET['step']) ? $_GET['step'] : '';

chdir("../");
include "utils/Utils.php";
include "config.php";
	

if ($step=='dump') { 
	echo shell_exec("mysqldump --user=$USER --password=$PWD --host=127.0.0.1 nplite > $dumpfile");
}
else if ($step=='upload') { 
	DBquery::init($dbs, array("tatagtest"));
	$schema = file_get_contents("tools/db_schema.sql");
	if (SITE=='dev') $schema = str_replace("InnoDB", "MEMORY", $schema);
	
	DBquery::set($schema);
	if (SITE != 'live' AND isset($_GET['data']) AND $data = $_GET['data']) DBquery::set(file_get_contents("tools/$data"));
	exit('{"status":"ok"}');
} 
else if ($step=="restore") {
	$db = $_GET['db'];
	DBquery::init($dbs, array($db));
	$schema = file_get_contents("tools/$db.sql");
	if (SITE=='dev') $schema = str_replace("InnoDB", "MEMORY", $schema);
	
	DBquery::set($schema);
	exit('{"status":"ok"}');
}
else exit("Invalid step='$step'.");

