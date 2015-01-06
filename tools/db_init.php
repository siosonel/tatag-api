<?php
header('content-type: text/plain');
set_time_limit(0);
error_reporting(error_reporting() & ~ E_NOTICE);

$dumpfile = 'nplite.sql'; 
$step = isset($_GET['step']) ? $_GET['step'] : '';

chdir("../");
include "models/Utils.php";
include "config.php";
	

if ($step=='dump') { 
	echo shell_exec("mysqldump --user=$USER --password=$PWD --host=127.0.0.1 nplite > $dumpfile");
}
else if ($step=='upload') { 
	DBquery::init($dbs, array("tatagtest"));
	DBquery::set(file_get_contents("tools/db_schema.sql"));
	if (isset($_GET['data']) AND $data = $_GET['data']) DBquery::set(file_get_contents("tools/$data"));
} 
else exit("Invalid step='$step'.");

