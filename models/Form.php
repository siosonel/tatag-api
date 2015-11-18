<?php

class Form {
	function get() {
		$defs = json_decode(file_get_contents("ref/defs.json"));
		
		foreach($defs AS $key=>$d) {
			if ($d->actions) {
				foreach($d->actions AS $a) {
					if ($a->{'@id'} == "/form/".	Router::$subresource) return array($a);
				}
			}
		} 
		
		Error::http(404, "The form='". Router::$subresource ."' was not found.");
	}
}