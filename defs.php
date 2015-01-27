<?php
header('content-type: text/json');
$defs = json_decode(file_get_contents("definitions/defs.json")); 
exit(json_encode($defs, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));


