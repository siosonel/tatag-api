<?php
include_once "../models/Utils.php";
header('content-type: application/json');

if (!isset($_GET['name']) OR !$_GET['name']) Error::http(400, "Missing or invalid query param value for name.");
$filename = "examples/". $_GET['name'] .".json";
$method = $_SERVER['REQUEST_METHOD'];

if ($method=='GET') {
	if (!file_exists($filename)) Error::http(404, "The example='$filename' was not found.");
	include $filename;
	exit();
}
else if ($method=='POST') {
	$content = file_get_contents("php://input");
	$content = json_decode($content);
	if (!$content) Error::http(400, "Unable to process submitted content.");

	file_put_contents($filename, json_encode($content, JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES));

	PhlatMedia::write(array($content));
}
else {
	Error::http(405, "The method='$method' is not supported.");
}

 