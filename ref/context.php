<?php

header('content-type: application/json');
require_once "../config-public.php";

?>{
	"@id": "<?php echo HOME ?>/ref/context.php",
	"@context": {
		"@base": "/api",
		
		"profile": {
			"@id": "https://tools.ietf.org/html/rfc6906",
			"@type": "@id"
		},
		
		"Link": {
			"@id": "/ref#api-data-link",
			"@type": "@id"
		},
		
		"user": {
			"@id": "/ref#user", 
			"@type": "Link"
		},
		
		"userAbout": {
			"@id": "/ref#userAbout", 
			"@type": "Link"
		},
		
		"userMemberships": {
			"@id": "/ref#userMemberships", 
			"@type": "Link"
		},

		"userAccounts": {
			"@id": "/ref#userAccounts", 
			"@type": "Link"
		},
		
		"userAccount": {
			"@id": "/ref#userAccount-item", 
			"@type": "Link"
		},
		
		"userCollection": {
			"@id": "/ref#userCollection", 
			"@type": "Link"
		},

		"account": {
			"@id": "/ref#account", 
			"@type": "Link"
		},
		
		"brandCollection": {
			"@id": "/ref#brandCollection", 
			"@type": "Link"
		},
		
		"brand": {
			"@id": "/ref#brand", 
			"@type": "Link"
		},
		
		"promoCollection": {
			"@id": "/ref#promoCollection", 
			"@type": "Link"
		},
		
		"items": {
			"@id": "/ref#items", 
			"@type": "Link"
		}
	}
}
