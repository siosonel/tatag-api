<?php
require_once "utils/Router.php";
Requester::init();

if (!Requester::$user_id) {
	require_once "login.php";
}
else {
	require_once "models/UserAccounts.php";
	$UserAccounts = new UserAccounts(json_decode('{"user_id": '. Requester::$user_id .'}')); 
	$UserAccounts->get(); //print_r($UserAccounts);
	
	header('content-type: text/html');
?><html>
<head>
	<title>Pay XTH</title>
	<script type="text/javascript" src="/common2/lib/jQuery/jquery-1.8.2.min.js"></script>
	<style>
		#payDiv .inputLabel {
			display: inline-block;
			width: 5rem;
		} 
		
		#from, #to, #amount {
			width: 12rem;
		}
	</style>
</head>
<body>
	<div id='payDiv'>
		<h2 id='payDivTitle'></h2>
		<div class='inputLabel'>From: </div>
		<select id='from'></select><br /><br />
		
		<div class='inputLabel'>To: </div>
		<input type='text' id='to' value='' /><br /><br />
		
		<div class='inputLabel'>Amount: </div>
		<input type='text' id='amount' value='' /><br /><br />
	</div>
</body>
<script>
(function (UserAccounts) { console.log(UserAccounts);
	var Invoker;
	
	UserAccounts.items.map(function (acct) {
		$('#from').append("<option value='"+ acct.account_id +"'>"
			+ acct.brand_name+': '+acct.account_name +", Bal="+ acct.balance
		+"</option>");
	});
	
	if (window.opener) Invoker = window.opener;
	if (window.parent) Invoker = window.parent; console.log(Invoker.location); 
	
	 if (Invoker 
	 	&& (
	 		Invoker.location.origin != window.location.origin 
	    	|| Invoker.location.pathName != window.location.pathname
	    )
	 ) { console.log(Invoker.location); 
	 	Invoker.postMessage("wallet-pay finished loading", Invoker.location.origin);
	 	$('#payDivTitle').html("Pay for product at "+ Invoker.location.origin + Invoker.location.pathname);
	 }
	 else console.log('No Invoker found.');
	 
	 $(window).on('message', function (e) { console.log(e);
	 	var mssg = JSON.parse(e.originalEvent.data); console.log(mssg);
	 	$('#to').val(mssg.to);
	 	$('#amount').val(mssg.amount);
	 });
 	
})(<?php echo json_encode($UserAccounts) ?>)
</script>
</html>
<?php
}
