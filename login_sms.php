<?php 
error_reporting(error_reporting() & ~ E_NOTICE);

require_once "config.php";
require_once "utils/Router.php";
require_once "models/TokenSms.php"; //require_once "/_exclude/data_tokenSms.php"; //echo HOME; exit();

Error::$format = 'sms';
DBquery::init($dbs, array("tatagtest"));
session_start();

$Token = new TokenSMS(new stdClass());
$Token->getByPhoneNum();
$url = str_replace('/tatag', '', HOME) ."/ui/?token_id=". $Token->token_id ."&amp;otk=". $Token->otk;


header("content-type: text/xml");

?><?xml version="1.0" encoding="UTF-8"?>
<Response>
    <Message><?php echo $url ?></Message>
</Response>

