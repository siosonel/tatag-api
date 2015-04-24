<?php
include_once "../config.php";

header('content-type: text/plain');
$baseURL = "http://tatag.dev/api";
chdir("..");

if (!$r = $_GET['r']) exit("A resource value for GET parameter 'r' is required.");
$n = explode(',', $_GET['n']); //optional test-example index matcher

$contents = file_get_contents("ref/defs.json");
$contents = str_replace("{random}", 1*substr("".(time()/100), -6),$contents);
$contents = str_replace("{xtime}", 1*substr("".time(), -6),$contents);
$defs = json_decode($contents);

foreach($defs->$r->actions AS $a) {	
	echo "\n\n";
	echo "***$a->title***\n";
	echo "Method: $a->method\n";
	
	foreach($a->examples AS $i=>$q) {
		if (!$n OR in_array($i,$n)) {
			$url = $baseURL . $q->target;
			echo "\n";
			echo "    [$i]: $q->label\n";
			echo "    URL: $url\n";
			echo "    submitted: ". json_encode($q->inputs) ."\n";
			echo "    expected: $q->status\n"; //. json_encode();
			echo "    actual: ". request($url, $a->method, $q->inputs);
		}
	}
}


function request($domain,$method,$data) {	
	$context=stream_context_create(array("http" => array(
		"method" => $method,
		"content" => json_encode($data), 
		"header" => "Content-type: application/json\nAuthorization: Basic ". base64_encode(TEST_USER .":". TEST_PWD),
		"timeout"=>3, 
		"max_redirects"=>2,
		'ignore_errors' => TRUE
	)));
	
	$response = file_get_contents($domain,0,$context);
	echo "    response_status: ". $http_response_header[0] ."\n";
	return trim($response);
}

