<?php
require "config.php";
include_once "models/Utils.php";
include_once "models/Base.php";
include_once "models/Tokentw.php";

session_start();
global $dbs;
$Token = new Tokentw(new stdClass());

if (
	!empty($_GET['oauth_verifier']) 
	AND !empty($_SESSION['oauth_token']) 
	AND !empty($_SESSION['oauth_token_secret'])
) {
	DBquery::init($dbs, array("tatagtest"));
	echo json_encode($Token->setTokenUserID());
}

else $Token->redirectToTwitter();