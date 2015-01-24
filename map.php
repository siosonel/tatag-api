<?php
header('content-type: text/json');
$map = json_decode(file_get_contents("definitions/map.json")); print_r($map);
//exit(json_encode(, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));


