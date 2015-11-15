<?php
include_once "../models/Utils.php";
header('content-type: application/json');

if ((!isset($_GET['name']) OR !$_GET['name']) AND (!isset($_GET['deprecate']) OR !$_GET['deprecate'])) {
	Error::http(400, "Missing or invalid query param value for name.");
}

$method = $_SERVER['REQUEST_METHOD'];
$filename = isset($_GET['name']) ? "examples/". $_GET['name'] .".json" : "";
$deprecate = isset($_GET['deprecate']) ? $_GET['deprecate'] : "";


if ($method=='GET' AND !$deprecate) {
	if (!file_exists($filename)) Error::http(404, "The example='$filename' was not found.");
	include $filename;
	exit();
}
else if ($method=='POST' AND !$deprecate) {
	if ($_SERVER['SERVER_NAME'] != 'tatag.dev') Error::http(401, "Posting to this resource is allowed only in the dev site.");

	$content = file_get_contents("php://input");
	$content = json_decode($content);
	if (!$content) Error::http(400, "Unable to process submitted content.");
	
	file_put_contents($filename, json_encode($content, JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES));

	PhlatMedia::write(array($content));
}
else if (/*$method=='POST' AND*/ $deprecate) {
	if (!is_dir($deprecate)) mkdir("examples/$deprecate");
	$wip = array_diff(scandir("examples/wip"), array(".", ".."));
	
	foreach($wip AS $f) {
		$wipf = file_get_contents("examples/wip/$f");
	
		if (!file_exists("examples/$f")) file_put_contents("examples/$f", $wipf);
		else {
			$currf = file_get_contents("examples/$f");
			if ($wipf != $currf) {
				file_put_contents("examples/$deprecate/$f", $wipf);
				file_put_contents("examples/$f", $wipf);
			}
		}
	}
}
else {
	Error::http(405, "The method='$method' is not supported.");
}

 