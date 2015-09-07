<?php 

require_once "models/Requester.php"; 
if (isset($_SERVER['PHP_AUTH_USER'])) Requester::init();
@header("content-type: text/html");

$provider = isset($_GET['provider']) ? $_GET['provider'] : '';
if ($provider=='tw') {require_once "login_tw.php";	exit();}
if ($provider=='email') {require_once "login_email.php";	exit();}

?><!DOCTYPE html>
<html>
<head>
	<title>Login</title>
	<link rel="icon" type="image/png" href="/ui/css/logo5.png">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1" />

  <link rel="stylesheet" href="/common2/lib/foundation-5.3.3/css/foundation.min.css">
	<link rel="stylesheet" href="/common2/lib/foundation-5.3.3/icons/foundation-icons.css">
	<style>
		body {
			background: #fff;
			color: #222;
			padding: 0;
			margin: 0;
			font-family: "Helvetica Neue","Helvetica",Helvetica,Arial,sans-serif;
			font-weight: normal;
			font-style: normal;
			line-height: 150%;
			position: relative;
			cursor: default;
			text-align: center;
			background-color: #ececec;
		}	
		
		#wrapper {
			background-color:#fff;
			margin-top: 30px;
			width: 80%;
			padding: 30px 10px 10px 10px;
			margin: 50px auto;
		}
	</style>


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
				success: function (resp) { console.log(resp); console.log(decodeURIComponent(params.next));					
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
		<h2>Log-in to tatag.cc using:</h2>
		<br />		
		<?php 
		if (!$provider) { ?>
			<p style='padding-left: 20px;'>
				<a href='#gp'>
					<span id='signinButton' 
		class='fi-social-google-plus large g-signin' 
		data-callback="signinCallback"
		data-clientid="272322507068-u9varoccgpb8f6ju88d3sk5df010osop.apps.googleusercontent.com"
		data-cookiepolicy="single_host_origin"
		data-requestvisibleactions="http://schema.org/AddAction"
		data-scope="https://www.googleapis.com/auth/plus.login"
		style='font-size: 49px; color: #dd4b39;'>&nbsp;&nbsp;</span>
				</a>
				<a href='#fb'>
					<span class='fi-social-facebook large' style='font-size: 50px; color: #3b5998;' onclick='triggerFBlogin()'>&nbsp;&nbsp;</span>
				</a>
				<a href='?<?php echo $_SERVER['QUERY_STRING'];?>&provider=tw'>
					<span class='fi-social-twitter large' style='font-size: 50px; color:#55acee;'>&nbsp;&nbsp;</span>
				</a>
				<a href='?<?php echo $_SERVER['QUERY_STRING'];?>&provider=email'>
					<span class='fi-mail large' style='font-size: 50px; color:#444'>&nbsp;&nbsp;</span>
				</a>
				<!--<span><br />-OR-<br /></span>
				<span>SMS text your email address to: <br /></span>
				<a href='sms:2564854476'>(256) 485-4476</a>-->
			</p>
		<?php }
		
		require_once "login_gp.php"; 		
		
		require_once "login_fb.php";
		?>
	</div>	
</body>
</html>