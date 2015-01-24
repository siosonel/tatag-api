<?php

$forms = array(	
	"users" => array(
		"add" => array(
			"@id"=> "",
			"action"=> "/users",
			"fields"=> ["name", "email", "password"],
			"validation"=> []
		),
		
		"edit" => array(
			"@id"=> "",
			"action"=> "/users/{user_id}",
			"fields"=> ["name", "email", "password"],
			"validation"=> []
		),
		
		"deactivate" => array(
			"@id"=> "",
			"action"=> "/users/{user_id}",
			"fields"=> ["ended"],
			"validation"=> []
		)		
	),
	
	"brands" => array(		
		"add-brand" => array(
			"@id"=> "",
			"action"=> "/brands/",
			"fields"=> [],
			"validation"=> []
		),
		
		"edit-by-admin" => array(
			"@id"=> "",
			"action"=> "/brands/{brand_id}",
			"fields"=> ['name', 'mission', 'description'],
			"validation"=> []
		),
		
		"deactivate" => array(
			"@id"=> "",
			"action"=> "/brands/{user_id}",
			"fields"=> ["ended"],
			"validation"=> []
		)		
	),	
	
	"memberships" => array(
		"edit-by-member" => array(
			"@id"=> "",
			"action"=> "/members/{member_id}",
			"fields"=> ["hours"],
			"validation"=> "" 
		),
		
		"edit-by-admin" => array(
			"@id"=> "",
			"action"=> "/members/{member_id}",
			"fields"=> ["role", "hours"],
			"validation"=> "" 
		),
		
		"deactivate" => array(
			"@id"=> "",
			"action"=> "/members/{member_id}",
			"fields"=> ["ended"],
			"validation"=> ""
		)
	),
	
	"accounts" => array(
		"edit-by-admin" => array(
			"@id"=> "",
			"action"=> "/accounts/{account_id}",
			"fields"=> ["name", "authcode"],
			"validation"=> []
		),
		
		"add-holder" => array(
			"@id"=> "",
			"action"=> "/holders/",
			"fields"=> ["account_id", "user_id", "authcode"],
			"validation"=> []
		),
		
		"remove-holder" => array(
			"@id"=> "",
			"action"=> "/holders/{holder_id}",
			"fields"=> ["ended"],
			"validation"=> []
		)
	),
	
	"accountholdings" => array(
		"edit-by-holder" => array(
			"@id"=> "",
			"action"=> "/holders/{holder_id}",
			"fields"=> ["alias"],
			"validation"=> []
		),
		
		"edit-by-admin" => array(
			"@id"=> "",
			"action"=> "/holders/{holder_id}",
			"fields"=> ["authcode"],
			"validation"=> []
		),
		
		"close" => "/actions/#accounts.remove-holder"
	)
);

