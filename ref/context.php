<?php

header('content-type: application/json');
require_once "../config-public.php";

?>{
	"@context": "<?php echo HOME ?>/ref/context.php",
	"@base": "/api",
	
	"profile": {
		"@id": "https://tools.ietf.org/html/rfc6906",
		"@type": "@id"
	},
	
	"user": {
		"@id": "/ref#user", 
		"@type": "@id"
	},
	
	"userMemberships": {
		"@id": "/ref#userMemberships", 
		"@type": "@id"
	},

	"userAccounts": {
		"@id": "/ref#userAccounts", 
		"@type": "@id"
	},
	
	"userCollection": {
		"@id": "/ref#userCollection", 
		"@type": "@id"
	},
	
	"brandCollection": {
		"@id": "/ref#brandCollection", 
		"@type": "@id"
	},
	
	"promoCollection": {
		"@id": "/ref#promoCollection", 
		"@type": "@id"
	}
}
