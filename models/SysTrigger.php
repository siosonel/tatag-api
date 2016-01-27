<?php

class SysTrigger extends Base {
	function __construct() {

	}

	function get() {
		return array(json_decode('{
            "@id": "/sys/trigger",
            "@type": "systemTrigger",
           	"approve": "/cron/records",
           	"add": "/cron/budgetAdd",
           	"report": "/cron/report",
           	"tally": "/cron/tally"
        }'));
	}
}

