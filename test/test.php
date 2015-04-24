<?php
set_time_limit(120);
include_once "../config.php";

header('content-type: text/plain');
$baseURL = "http://tatag.dev/api";
chdir("..");

$r = isset($_GET['r']) ? explode(',', $_GET['r']) : array();
$n = isset($_GET['n']) ? explode(',', $_GET['n']) : array(); //optional test-example index matcher

$contents = file_get_contents("ref/defs.json");
$contents = str_replace("{random}", 1*substr("".(time()/100), -6),$contents);
$contents = str_replace("{xtime}", date('Y-m-d h:i:s'),$contents);
$defs = json_decode($contents);

if (!$r) {
	echo "\n\n***Database Setup: ";
	$q = json_decode('{"label":"testdata.sql", "inputs":{}, "status":0}'); 
	$dbsetup = json_decode(request("http://tatag.dev/api/tools/db_init.php?step=upload&data=testdata.sql","POST",$q));
	if ($dbsetup->status != 'ok') exit(json_encode($dbsetup));
	else echo "$dbsetup->status***\n\n";
}

$warnings = 0;
$failed = 0;
$passed = 0;
$out = "";
echo "\nRESOURCES:\n";

foreach($defs->resourceTypes AS $resourceName) {
	if (!$r OR in_array($resourceName,$r)) {
		echo "\n$resourceName .....";
		$out .= "\n\n*** $resourceName ***\n";
		
		if (!isset($defs->$resourceName->actions)) {
			$warnings++;
			echo "\n-   No defined actions.\n";
			$out .= "\n-   No defined actions.";
		}
		else {
			foreach($defs->$resourceName->actions AS $a) {	
				$out .= "[ $a->title ]\n";
				$out .= "Method: $a->method\n";
				
				if (isset($a->examples)) {
					foreach($a->examples AS $i=>$q) {
						if (!$n OR in_array($i,$n)) {
							$url = $baseURL . $q->target;
							if (isset($q->query)) $url .= "?". http_build_query($q->query);
							
							$out .= "\n";
							$out .= "    [$i]: $q->label\n";
							$out .= "    URL: $url\n";
							$out .= "    submitted: ". json_encode($q->inputs) ."\n";
							$out .= "    expected: $q->status\n"; //. json_encode();
							$out .= "    actual: ". request($url, $a->method, $q) ."\n\n";
						}
					}
				}
			}
		}
	}
}

echo "\n\n\nRESULTS:\n     Warnings: $warnings\n     Failed: $failed\n     Passed: $passed\n";
echo "\n\n\n\nDETAILS:\n$out";

function request($url,$method,$q) {	
	global $warnings; global $failed; global $passed; global $out;
	
	if (gettype($q) != "object") $q = json_decode('{"label":"undefined", "inputs":{}, "status":0}'); 
	
	$context=stream_context_create(array("http" => array(
		"method" => $method,
		"content" => json_encode($q->inputs), 
		"header" => "Content-type: application/json\nAuthorization: Basic ". base64_encode(TEST_USER .":". TEST_PWD),
		"timeout"=>3, 
		"max_redirects"=>2,
		'ignore_errors' => TRUE
	)));
	
	$response = file_get_contents($url,0,$context);
	$status = substr($http_response_header[0], 9);
	if ($q->status AND strpos($status, "$q->status")===false) {
		echo "\n!   $q->label\n-   expecting: $q->status, actual: $status\n";
		$failed++;
	}
	else $passed++;
	
	return $q->status ? trim($status ." ". $response) : $response;
}

