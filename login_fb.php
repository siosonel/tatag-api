<!--
**********************************************
	* fb login function(s), 
		in combination with sdk.js script	
		and fb:login elements
***********************************************
-->
<div id='status'></div>
<!--<div id="fb-root" style='display: inline-block;'>
	<fb:login-button scope="public_profile,email" data-size='large' onlogin="checkLoginState();"></fb:login-button>
</div>-->
<br /><br />

<script type="text/javascript" src="//connect.facebook.net/en_US/sdk.js?"></script>
<script>		
	window.fbAsyncInit = function() {
		FB.init({
			appId      : '1468346626773625',
			cookie     : true,  // enable cookies to allow the server to access the session
			xfbml      : true,  // parse social plugins on this page
			version    : 'v2.1', // use version 2.1
			status     : false
		});
	};
	
	function statusChangeCallback(authResult) { console.log(authResult);
		if (authResult.status === 'connected') 
			getTatagTokenVal(authResult.authResponse, 'fb'); 
			
		else if (authResult.status === 'not_authorized') // The person is logged into Facebook, but not your app.
			document.getElementById('status').innerHTML = 'Please log into this app.';
			
		else // The person is not logged into Facebook, so we're not sure if they are logged into this app or not.
			FB.login(function (d) {console.log(d);}); //document.getElementById('status').innerHTML = 'Please log into Facebook.';
		
	}
	
	// This function is called when someone finishes with the fb Login Button.  
	function checkLoginState() {
		FB.getLoginStatus(statusChangeCallback);
	}
	
	function triggerFBlogin() {
		FB.login(checkLoginState);
		return false;
	}
</script>