var definitions={
	"general": {
		"requester": "the user for whom an API request is submitted, authorized via email/passowrd or user_id/token",
		"input": "the request body, submitted as a json-encoded object"
	},
	
	"requester-roles": {
		"default": "a requester who does not submit authorization information in the request header or does not fall into a member, holder, or admin definition",
		
		"self": "an authorized requester whose authorized user_id equals the input user_id",
		
		"member": "an authorized requester who is a current member in a given brand",
		
		"holder": "an authorized requester who is a current holder of a given account",
		
		"admin": "an authorized requester who has the role of admin for a given brand"
	},
	
	"resources": {
		"accounts-collection": {
			"endpoint": "/accounts",
			
			"properties": {
				"name": {
					"type": "string", "min": 1, "max": 255
				}
			},
			
			"filters": {
				"default": [],
				"holder": [],
				"admin": []
			},
			
			"viewable": {
				"default": [],
				"holder": [],
				"admin": []
			},
			
			"editable": {
				"admin": ["name", "alias", "balance", "unit", "authcode"]
			},
		},

		"account-instance": {
			"endpoint": "/accounts/{account_id}", 
		
			"properties": {
				"name": {
					"type": "string", "min": 1, "max": 255
				}
			},
			
			"viewable": {
				"default": ["balance","unit"],
				"holder": ["name", "alias", "balance", "unit", "authcode", "created", "holders"],	
				"admin": ["name", "alias", "balance", "unit", "authcode", "created", "holders"]
			}, 
				
			"editable": {				
				"holder": ["alias"],		
				"admin": ["name", "authcode", "ended"]
			}
		},
		
		"brands-collection": {
			"endpoint": "/brands",
		
			"properties": {
				"name": {
					"type": "string", "min": 1, "max": 255
				}
			},
		
			"filters": {
				"default": []
			},
			
			"viewable": {
				"default": [],
				"member": [],
				"admin": []
			},
			
			"editable": {				
				"admin": ["name", "mission", "description"]
			}
		},
		
		"brand-instance": {
			"endpoint": "/brands/{brand_id}",
			
			"properties": {
				"name": {
					"type": "string", "min": 1, "max": 255
				}
			},
			
			"viewable": {
				
			},
			
			"editable": {				
				"admin": ["name", "mission", "description"]
			}
		},
		
		"holders-collection": {
			"endpoint": "/holders",
			
			"properties": {
				"account_id":
				"user_id":				
				"alias": 
				"authcode": 
			},
			
			"filters": {
				"default": [""]
			},
			
			"viewable": {
				"default": []
			},
			
			"editable": {
				"default": []
			}
		},
		
		"holders-instance": {
			"endpoint": "/holders/{holder_id}",
			
			"properties": {
				"account_id":
				"user_id":				
				"alias": 
				"authcode": 
			},
			
			"filters": {
				"default": []
			},
			
			"viewable": {
				"holder": ["account_id", "name", "holder_id", "alias", "created", ""]
			},
			
			"editable": {
				"holder": ["alias"],
				"admin": ["authcode", "ended"]
			}
		},
		
		"users-collection": {
			"endpoint": "/users",
			
			"properties": {
				"user-instance": 
			},
			
			"filters": {
				"default": [""]
			},
			
			"viewable": {
				"default": []
			},
			
			"editable": {
				"default": ["email","name","profileImg","bannerImg"]
			}
		},
		
		"users-instance": {
			"endpoint": "/users/{user_id}",
			
			"properties": {
				"created":
				"user_id":				
				"name": 
				"email": 
				"password":
			},
			
			"filters": {
				"default": []
			},
			
			"viewable": {
				"default": ["created", "ended"]
			},
			
			"editable": {
				"self": ["email","name","profileImg","bannerImg","ended"]
			}
		}
	}
};