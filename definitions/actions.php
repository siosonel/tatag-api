<?php

$actions = array(	
	"users" => array(
		"add" => array(
			"action"=> "/users",
			"fields"=> ["name", "email", "password"],
			"validation"=> []
		),
		
		"edit" => array(
			"action"=> "/users/{user_id}",
			"fields"=> ["name", "email", "password"],
			"validation"=> []
		),
		
		"deactivate" => array(
			"action"=> "/users/{user_id}",
			"fields"=> ["ended"],
			"validation"=> []
		)		
	),
	
	"brands" => array(		
		"add-brand" => array(
			"action"=> "/brands/",
			"fields"=> [],
			"validation"=> []
		),
		
		"edit-by-admin" => array(
			"action"=> "/brands/{brand_id}",
			"fields"=> ['name', 'mission', 'description'],
			"validation"=> []
		),
		
		"deactivate" => array(
			"action"=> "/brands/{user_id}",
			"fields"=> ["ended"],
			"validation"=> []
		)		
	),	
	
	"memberships" => array(
		"edit-by-member" => array(
			"action"=> "/members/{member_id}",
			"fields"=> ["hours"],
			"validation"=> "" 
		),
		
		"edit-by-admin" => array(
			"action"=> "/members/{member_id}",
			"fields"=> ["role", "hours"],
			"validation"=> "" 
		),
		
		"deactivate" => array(
			"action"=> "/members/{member_id}",
			"fields"=> ["ended"],
			"validation"=> ""
		)
	),
	
	"accounts" => array(
		"edit-by-admin" => array(
			"action"=> "/accounts/{account_id}",
			"fields"=> ["name", "authcode"],
			"validation"=> []
		),
		
		"add-holder" => array(
			"action"=> "/holders/",
			"fields"=> ["account_id", "user_id", "authcode"],
			"validation"=> []
		),
		
		"remove-holder" => "close" => array(
			"action"=> "/holders/{holder_id}",
			"fields"=> ["ended"],
			"validation"=> []
		)
	),
	
	"accountholdings" => array(
		"edit-by-holder" => array(
			"action"=> "/holders/{holder_id}",
			"fields"=> ["alias"],
			"validation"=> []
		),
		
		"edit-by-admin" => array(
			"action"=> "/holders/{holder_id}",
			"fields"=> ["authcode"],
			"validation"=> []
		),
		
		"close" => array(
			"action"=> "/holders/{holder_id}",
			"fields"=> ["ended"],
			"validation"=> []
		)
	)
);

