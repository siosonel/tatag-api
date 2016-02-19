<?php
/*
A configuration file would typically be named after the
intended audience, such as "config-public.php". See the 
utils/Requester.php->detectAudience() method.

Fill in any of the *_USER, *_PWD, *_SECRET values as needed
below.
*/



$SN = $_SERVER['SERVER_NAME'];
$PATH = $_SERVER['PHP_SELF'];

//most scripts uses the Requester class to determine the SITE value
if (!defined('SITE')) {	
	if (substr($SN,-4)==".dev" OR $SN=='localhost') define("SITE", "dev");
	else if ($SN=='stage.tatag.cc') define('SITE','stage');
	else if ($SN=='tatag.cc') define('SITE', 'live');
	else define('SITE', 'live');
}

define("TEST_EMAIL", "user21@email.org");
define('TEST_USER', 21);
define('TEST_PWD', '');
define('TALLY_USER','');


date_default_timezone_set('America/Los_Angeles');

// SITE or environment is detected in Requester class

//url of this app
if (SITE=='dev') define('HOME', 'http://tatag.dev/api');
else if (SITE=='stage') define('HOME', 'http://stage.tatag.cc/api');
else define('HOME', 'https://tatag.cc/api');


//twilio sid
define('SMS_SID', '');

//oauth, login service provider tokens
//if you only provide login by email, you can skip these 
//and even disable the recaptcha as needed
define('G_RECAPTCHA','');
define('GAPI_CLIENT_ID', "");
define('FB_CLIENT_ID', "");
define('TWITTER_CONSUMER_KEY','');
define('TWITTER_CONSUMER_SECRET','');


//open-access users
define('OPEN_ACCESS_USER', 'consumer-2');
define('OPEN_ACCESS_PW', '');


if ((SITE=='dev' OR SITE=='stage') AND (!isset($_SERVER['PHP_AUTH_USER']) AND isset($_GET['auth']))) {
	$_SERVER['PHP_AUTH_USER'] = $_GET['auth'];
	$_SERVER['PHP_AUTH_PW'] = $_GET['pwd'];	
	unset($_GET['auth']);
	unset($_GET['pwd']);
}

$dbs = array(
	'tatagtest' => array(
		"SERVER_NAME" => SITE=='dev' ? "localhost" : $SN,
		"DB_NAME" => SITE=='live' ? "tatag" : "tatagtestdtd",
		"USER" => "",
		"PWD" => ""
	),
	'tatagsim' => array(
		"SERVER_NAME" => SITE=='dev' ? "localhost" : $SN,
		"DB_NAME" => "tatagsim",
		"USER" => "",
		"PWD" => ""
	),
	'tatagtestdtd' => array(
		"SERVER_NAME" => "localhost",
		"DB_NAME" => "tatagtestdtd",
		"USER" => "",
		"PWD" => ""
	)
); 



?>