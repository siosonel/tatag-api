<?php include "config.php"; ?><!DOCTYPE html>
<html>
<head>
<title>Login</title>
<meta charset="UTF-8">
<script type="text/javascript" src="/common2/lib/jQuery/jquery-1.8.2.min.js"></script>

</head>
<body>
	<div style='width:100%; text-align: center;'>
		<h2>Log-In to Tatag Using</h2>
		<span id="signinButton">
			<span
				class="g-signin"
				data-callback="signinCallback"
				data-clientid="272322507068-u9varoccgpb8f6ju88d3sk5df010osop.apps.googleusercontent.com"
				data-cookiepolicy="single_host_origin"
				data-requestvisibleactions="http://schema.org/AddAction"
				data-scope="https://www.googleapis.com/auth/plus.login">
			</span>
		</span>
		<br /><br />
		<div id="fb-root" style='display: inline-block;'>
			<fb:login-button scope="public_profile,email" data-size='large' onlogin="checkLoginState();"></fb:login-button>
		</div>
		<br /><br />
		<a href='login_twitter.php?<?php echo $_SERVER['QUERY_STRING'];?>'><img src='https://g.twimg.com/Twitter_logo_blue.png' style='width: 25px;'/> Twitter sign-in</a>
	</div>

	<script type="text/javascript">	
		var baseURI = window.location.origin;
		var params={};
		var queryStr = window.location.search.substr(1).split('&').map(function (p) {var arr=p.split("="); params[arr[0]]=arr[1]});	
		
		var idProvider = {
			"fb": {tokenName: "accessToken", idType: "fb_id"},
			"gp": {tokenName: "access_token", idType: "gp_id"}
		};
	
		
		function getTatagTokenVal(auth, providerName) { console.log(auth); return;
			var provider = idProvider[providerName]; //console.log(auth[provider.tokenName]+' '+provider.idType); return;
			var data = {access_token:auth[provider.tokenName], id_type: provider.idType};
			if (data.id_type=='fb_id') data.fb_id = auth.userID;
			
			$.ajax({
				url: baseURI + "/tatag/token"+ providerName +"/" + params.token_id,
				type: "POST",
				dataType: 'json',
				contentType: 'json',
				headers: {
					"Authorization": "Basic " + btoa('token-'+params.token_id + ":" + params.otk)
				},
				data: JSON.stringify(data),
				success: function (resp) { console.log(resp); //console.log(decodeURIComponent(params.next));					
					var token = resp['@graph'][0];
					window.location.href = decodeURIComponent(params.next) +'?token_id='+ token.token_id+'&otk='+ token.otk;
				}, 
				error: function (xhr, status, text) {
					console.log(status+' '+text)
				}
			})
		}
	</script>
	
	
	<!--	
	**********************************************
		* g+ login function(s), 
			in combination with client:plusone.js script
			and signinButton span
	***********************************************
	-->
	<script type="text/javascript" src="https://apis.google.com/js/client:plusone.js?"></script>
	<script>		
		 function signinCallback(authResult) { console.log(authResult); //return;
			if (authResult['error'] || !authResult['status']['signed_in']) {console.log('There was an error: ' + authResult['error']); return;}
			
			if (authResult['status']['signed_in']) {
				// Update the app to reflect a signed in user
				// Hide the sign-in button now that the user is authorized, for example:
				//document.getElementById('signinButton').setAttribute('style', 'display: none');
				getTatagTokenVal(authResult, 'gp');
			} 
		}		
	</script>
	
	
	<!--
	**********************************************
		* fb login function(s), 
			in combination with sdk.js script	
			and fb:login elements
	***********************************************
	-->
	<script type="text/javascript" src="//connect.facebook.net/en_US/sdk.js?"></script>
	<script>		
		window.fbAsyncInit = function() {
			FB.init({
				appId      : '1468346626773625',
				cookie     : true,  // enable cookies to allow the server to access the session
				xfbml      : true,  // parse social plugins on this page
				version    : 'v2.1' // use version 2.1
			});
		};
		
		function statusChangeCallback(authResult) { console.log(authResult);
			if (authResult.status === 'connected') 
				getTatagTokenVal(authResult.authResponse, 'fb'); 
				
			else if (authResult.status === 'not_authorized') // The person is logged into Facebook, but not your app.
				document.getElementById('status').innerHTML = 'Please log into this app.';
				
			else // The person is not logged into Facebook, so we're not sure if they are logged into this app or not.
				document.getElementById('status').innerHTML = 'Please log into Facebook.';
			
		}
		
		// This function is called when someone finishes with the fb Login Button.  
		function checkLoginState() {
			FB.getLoginStatus(statusChangeCallback);
		}
	</script>
	
</body>
</html>