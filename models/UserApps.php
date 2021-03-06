<?php

class UserApps extends Collection {
	function __construct($data='') {
		$this->user_id = $this->getID();
		if (!Requester::isUser($this->user_id)) Error::http(401, "The requester must be logged in as the requested user.");

		$this->{"@id"} = "/user/$this->user_id/apps";
		$this->{'@type'} = "userApps";
		$this->collectionOf = "app";
		$this->table = "consumers";

		$this->init($data);

		$this->okToAdd = array("name", "secret", "type", "redirect_url");
	}

	function get() {
		$graph = array($this);
		$this->app = array("/app/trial");

		$sql = "SELECT * FROM consumers WHERE user_id=?";
		$rows = DBquery::get($sql, array($this->user_id));

		foreach($rows AS $r) {
			if ($r['type']=='advisor') {
				$r['advise'] = "$this->root/app/$r[consumer_id]/advise";
				$r['config'] = "$this->root/app/$r[consumer_id]/config";
			}

			$r['@id'] = "$this->root/app/$r[consumer_id]/details";
			$this->app[] = $r["@id"];
			$graph[] = $r;
		}

		$this->paginate('consumer_id');
		return $graph;
	}

	function set() {
		$this->addKeyVal('redirect_url', 'NULL', 'ifMissing');
		$secret = $this->obj->secret;
		$this->obj->secret = password_hash($secret, PASSWORD_DEFAULT);

		$this->obj->consumer_id = $this->insert();
		
		$this->obj->secret = $secret;
		return array($this->obj);
	}
}

