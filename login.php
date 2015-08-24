<?php 

require_once "models/Requester.php"; 
Requester::init();
header("content-type: text/html");

$provider = isset($_GET['provider']) ? $_GET['provider'] : '';
if ($provider=='tw') {require_once "login_tw.php";	exit();}
if ($provider=='email') {require_once "login_email.php";	exit();}

?><!DOCTYPE html>
<html>
<head>
<title>Login</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1" />

<script type="text/javascript" src="/common2/lib/jQuery/jquery-1.8.2.min.js"></script>

<script type="text/javascript">	
		var baseURI = window.location.origin;
		var params={};
		var queryStr = window.location.search.substr(1).split('&').map(function (p) {var arr=p.split("="); params[arr[0]]=arr[1]});	
		
		var idProvider = {
			"fb": {tokenName: "accessToken", idType: "fb_id"},
			"gp": {tokenName: "access_token", idType: "gp_id"}
		};
	
		
		function getTatagTokenVal(auth, providerName) { //console.log(auth); return;
			if (!params.token_id) {console.log("Missing value for query parameter token id."); return;}
			if (!(providerName in idProvider)) {console.log("Invalid login provider value."); return;}
			
			var provider = idProvider[providerName]; //console.log(auth[provider.tokenName]+' '+provider.idType); return;
			var data = {access_token:auth[provider.tokenName], id_type: provider.idType};
			if (data.id_type=='fb_id') data.fb_id = auth.userID;
			
			$.ajax({
				url: "./token"+ providerName +"/" + params.token_id,
				type: "POST",
				dataType: 'json',
				contentType: 'json',
				headers: {
					"Authorization": "Basic " + btoa('token-'+params.token_id + ":" + params.otk)
				},
				data: JSON.stringify(data),
				success: function (resp) { console.log(resp); //console.log(decodeURIComponent(params.next));					
					var token = resp['@graph'][0];
					var url = decodeURIComponent(params.next);
					var separator = url.search("/\?/")!=-1 ? "&" : "?"; 
					window.location.href = url + separator + 'token_id='+ token.token_id+'&otk='+ token.otk;
				}, 
				error: function (xhr, status, text) {
					console.log(status+' '+text)
				}
			})
		};
		
		$(document).ready(function () {$('body').delay(800).animate({opacity:1},{duration:1000})});
	</script>	
</head>
<body style='opacity:0;'>
	<div style='width:100%; text-align: center;'>
		<br />
		<h2>Log-In to Tatag Using:</h2>
		<br />
		
		<?php 
		if (!$provider OR $provider=="gp") require_once "login_gp.php"; 		
		
		if (!$provider OR $provider=="fb") require_once "login_fb.php";
		
		if (!$provider OR $provider=="tw") { ?>
		<a href='login_tw.php?<?php echo $_SERVER['QUERY_STRING'];?>' style='text-decoration: none; font-size:18px;'>
			<img src='https://g.twimg.com/Twitter_logo_blue.png' style='width: 22px;'/>
			<span style='vertical-align:top;'>Sign-in</span>
		</a>
		<?php }
		
		
		if (0 AND !$provider)	{ ?>
		<a href='?provider=email'><span class='fi-social-mail large' style='font-size: 50px; color:#55acee;'>&nbsp;&nbsp;</span></a>			
		<?php } ?>
	</div>	
</body>
</html>