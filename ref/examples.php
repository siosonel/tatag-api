<?php
include_once "../models/Utils.php";
header('content-type: application/json');

if ($_SERVER['REQUEST_METHOD']!='PUT') Error::http(405, "Only PUT methods are allowed.");
if (!isset($_GET['name']) OR !$_GET['name']) Error::http(400, "Missing or invalid query param value for name.");

$content = file_get_contents("php://input");
$content = json_decode($content);
if (!$content) Error::http(400, "Unable to process submitted content.");

file_put_contents("examples/". $_GET['name'] .".json", json_encode($content, JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES));

PhlatMedia::write(array($content));
 