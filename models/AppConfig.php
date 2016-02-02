<?php

class AppConfig extends Base {
	protected $consumer_id;
	protected $configFile;
	protected $config;
	public $id;

	function __construct($data='') {
		$this->consumer_id = $this->getID();
		$this->{"@id"} = "$this->root/app/$this->consumer_id/config";
		$this->{"@type"} = "appConfig";

		$this->init($data);

		$this->configFile = "advisors/config/$this->consumer_id.json";
		$this->config = file_exists($this->configFile) ? json_decode(file_get_contents($this->configFile)) : new stdClass();
		foreach($this->config AS $k=>$v) $this->$k = $v;
	}

	function get() {
		$this->id = $this->consumer_id;
		if ($this->consumer_id!==0) $this->edit = "$this->root/form/app-config-edit";
		return array($this);
	}

	// set consumer-specific config file to be used as input when getting advise
	// assume config, including whitelist and blacklist, would change much more frequently
	// than the advise algorithm
	function set() {
		if ($this->obj) {
			foreach($this->obj AS $k=>$v) {
				$this->config->$k = $v;
			}

			file_put_contents($this->configFile, json_encode($this->config));
		}
		
		return array($this->config);
	}
}
