<!--	
	**********************************************
		* g+ login function(s), 
			in combination with client:plusone.js script
			and signinButton span
	***********************************************
	-->

<!--<span id="signinButton" style='display: none'>
	<span
		class="g-signin"
		data-callback="signinCallback"
		data-clientid="272322507068-u9varoccgpb8f6ju88d3sk5df010osop.apps.googleusercontent.com"
		data-cookiepolicy="single_host_origin"
		data-requestvisibleactions="http://schema.org/AddAction"
		data-scope="https://www.googleapis.com/auth/plus.login"
    data-approvalprompt="force">
	</span>
</span>-->
<br /><br />
<script type="text/javascript" src="https://apis.google.com/js/client:plusone.js?"></script>
<script>
	 var uninit=1;
	 
	 function signinCallback(authResult) { //console.log(authResult); //return;
		if (uninit) {
			gapi.auth.signOut();
			uninit=0;
			return;
		}
		
		if (authResult['error'] || !authResult['status']['signed_in']) {console.log('There was an error: ' + authResult['error']); return;}
		
		if (authResult['status']['signed_in']) {
			// Update the app to reflect a signed in user
			// Hide the sign-in button now that the user is authorized, for example:
			//document.getElementById('signinButton').setAttribute('style', 'display: none');
			getTatagTokenVal(authResult, 'gp');
		} 
	}
</script>