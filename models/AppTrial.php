<?php

class AppTrial extends Base {
	function __construct() {

	}

	function get() {
		return array(json_decode('{
            "@id": "/app/trial",
            "@type": "appResources",
            "budgetlog": "/report/addUse",
            "tally": "/tally/",
            "addedByWeek": "/added/byWeek",
            "inflowByWeek": "/inflow/byWeek",
            "outflowByWeek": "/outflow/byWeek",
            "intrauseByWeek": "/intrause/byWeek",
            "flowMatrix": "/flow/matrix"
        }'));
	}
}

