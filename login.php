<?php include "config.php"; ?><!DOCTYPE html>
<html>
<head>
<title>Login</title>
<meta charset="UTF-8">
<script type="text/javascript" src="/common2/lib/jQuery/jquery-1.8.2.min.js"></script>
<script type="text/javascript" src="https://apis.google.com/js/client:plusone.js?"></script>

</head>
<body>
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
	<div id='responseContainer'></div>
	<script type="text/javascript">	
		var g_np;
		var baseURI = window.location.origin;
		var params={};
		var queryStr = window.location.search.substr(1).split('&').map(function (p) {var arr=p.split("="); params[arr[0]]=arr[1]}); console.log(params); console.log(decodeURIComponent(params.next));
	
		 
		 function signinCallback(authResult) { console.log(authResult); //return;
			if (authResult['error'] || !authResult['status']['signed_in']) {alert('There was an error: ' + authResult['error']); return;}
			
			if (authResult['status']['signed_in']) {
				// Update the app to reflect a signed in user
				// Hide the sign-in button now that the user is authorized, for example:
				document.getElementById('signinButton').setAttribute('style', 'display: none');
				$.ajax({
					url: baseURI + "/tatag/token/" + params.token_id,
					type: "POST",
					dataType: 'json',
					contentType: 'json',
					headers: {
						"Authorization": "Basic " + btoa('token-'+params.token_id + ":" + params.otk)
					},
					data: JSON.stringify({access_token:authResult['access_token']}),
					success: function (resp) { console.log(resp); console.log(decodeURIComponent(params.next));					
						//window.location.href = decodeURIComponent(params.next);
					}, 
					error: function (xhr, status, text) {
						console.log(status+' '+text)
					}
				})
			} 
		}


		function handleResponse(response) { console.log(response); return;
			if (response.objectType!='person') {alert("The logged-in google profile must belong to a person."); return;}
			
			$.ajax({
				url: baseURI + "/tatag/token/" + params.token_id,
				type: "POST",
				dataType: 'json',
				contentType: 'json',
				headers: {
					"Authorization": "Basic " + btoa('token-'+params.token_id + ":" + params.otk)
				},
				data: JSON.stringify({user_id:response.id}),
				success: function (resp) { console.log(resp); console.log(decodeURIComponent(params.next));					
					//window.location.href = decodeURIComponent(params.next);
				}, 
				error: function (xhr, status, text) {
					console.log(status+' '+text)
				}
			})
		}
		
		//handleResponse({id:"5bb40e75eb61a8000", objectType: 'person'});
	</script>
	
	<script>
/*	var fbnp;

  // This is called with the results from from FB.getLoginStatus().
  function statusChangeCallback(response) {
    console.log('statusChangeCallback');
    console.log(response);
    // The response object is returned with a status field that lets the
    // app know the current login status of the person.
    // Full docs on the response object can be found in the documentation
    // for FB.getLoginStatus().
    if (response.status === 'connected') {
      // Logged into your app and Facebook.
      testAPI();
    } else if (response.status === 'not_authorized') {
      // The person is logged into Facebook, but not your app.
      document.getElementById('status').innerHTML = 'Please log ' +
        'into this app.';
    } else {
      // The person is not logged into Facebook, so we're not sure if
      // they are logged into this app or not.
      document.getElementById('status').innerHTML = 'Please log ' +
        'into Facebook.';
    }
  }

  // This function is called when someone finishes with the Login
  // Button.  See the onlogin handler attached to it in the sample
  // code below.
  function checkLoginState() {
    FB.getLoginStatus(function(response) {
      statusChangeCallback(response);
    });
  }

  window.fbAsyncInit = function() {
  FB.init({
    appId      : '1468346626773625',
    cookie     : true,  // enable cookies to allow the server to access 
                        // the session
    xfbml      : true,  // parse social plugins on this page
    version    : 'v2.1' // use version 2.1
  });

  // Now that we've initialized the JavaScript SDK, we call 
  // FB.getLoginStatus().  This function gets the state of the
  // person visiting this page and can return one of three states to
  // the callback you provide.  They can be:
  //
  // 1. Logged into your app ('connected')
  // 2. Logged into Facebook, but not your app ('not_authorized')
  // 3. Not logged into Facebook and can't tell if they are logged into
  //    your app or not.
  //
  // These three cases are handled in the callback function.

  FB.getLoginStatus(function(response) {
    statusChangeCallback(response);
  });

  };
	
  // Load the SDK asynchronously
  (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));

  // Here we run a very simple test of the Graph API after login is
  // successful.  See statusChangeCallback() for when this call is made.
  function testAPI() {
    console.log('Welcome!  Fetching your information.... ');
    FB.api('/me', function(response) { fbnp = response;
      console.log('Successful login for: ' + response.name);
      document.getElementById('status').innerHTML =
        'Thanks for logging in, ' + response.name + '!';
    });
  }*/
</script>

<!--
  Below we include the Login Button social plugin. This button uses
  the JavaScript SDK to present a graphical Login button that triggers
  the FB.login() function when clicked.
-->

<!--<fb:login-button scope="public_profile,email" onlogin="checkLoginState();">
</fb:login-button>-->

<div id="status"></div>
</body>
</html>