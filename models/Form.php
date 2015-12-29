<?php

class Form {
	function get() {   
		if (isset($_GET['save'])) $this->set();
		$f = "ref/forms/".	Router::$subresource .".json";
		
		if (!file_exists($f)) Error::http(404, "The form='". Router::$subresource ."' was not found.");
		return array(json_decode(file_get_contents($f)));
	}
	
	function set() {
		$defs = json_decode(file_get_contents("ref/defs.json"));
		
		foreach($defs AS $key=>$d) {
			if ($d->actions) {
				foreach($d->actions AS $a) {
					file_put_contents(
						"ref/forms/". str_replace('/form/', '', $a->{'@id'}) .".json", 
						json_encode($a, JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT )
					);
				}
			}
		}
	}
}