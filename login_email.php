<?php include_once "config-public.php" ?><html>
<head>
	<title>tatag.cc login</title>
	<link rel="icon" type="image/png" href="/ui/css/logo5.png">
	
	<style>
		#mainDiv {
			padding: 20px;
		}
		
		#mainDiv div {
			margin: 10px;
		}
		
		#mainDiv textarea {
			width: 90%;
			margin: auto;
		}
		
		#verifyDiv, #newPwdDiv {
			display: none;
		}
		
		#newPwdDiv button {
			position: relative;
			left: 80%;
		}
		
		td {
			padding: 10px;
		}
	</style>
	
	<script src="https://www.google.com/recaptcha/api.js" async defer></script>
	<script type="text/javascript" src="/common2/lib/jQuery/jquery-1.8.2.min.js"></script>
</head>
<body>
	<div id='mainDiv'>
		<h3>Log in to tatag.cc</h3>
		
		<div id='emailDiv'>
			<label for='email'>E-mail: </label>
			<input type='text' name='email' id='email' value='' />
		</div>
		
		<div class="g-recaptcha" data-sitekey="6Lf4rwsTAAAAAOccKg4CY02kQhEi5lCCTB_DsQFL" data-callback='reverify' style='margin-left: -10px;'>
		</div>
		
		<h3>Choose one: </h3>
		
		<div id='loginDiv'>
			<input type='radio' name='action' id='action-login' value='login'  checked='checked'/>
			<label for='action-login'>I have a password: </label>
			<input type='password' name='pwd' id='pwd' value='' /> 
			<button id='loginBtn'>Login</button>
		</div>
		
		<div id='forgotDiv'>
			<input type='radio' name='action' id='action-recover' value='recover' />
			<label for='action-recover'>I forgot my password and need a verification code to
				<button id='sendRecoveryCode' disabled='disabled'>recover</button>.
			</label>
		</div>
		
		<div id='registerDiv'>
			<input type='radio' name='action' id='action-register' value='register'/>
			<label for='action-register'>I don't have a password and need a verification code to
				<button id='sendRegistrationCode' disabled='disabled'>register</button>.</label>
		</div>
		
		<div id='verifyDiv'>
			<p id='verifyInstruction'>Check your inbox AND spam folder for a <b>verification code</b> from a tatag.cc email.</p>
		</div>
		
		<div id='newPwdDiv'>
			<table>
			<tr>
				<td><label for='verification_code'>Verification Code: </label>&nbsp;</td>
				<td><input type='text' name='verCode' id='verCode' value='' /></td>
			</tr>
			<tr>
				<td><label for='newPwd'>New Password: </label></td>
				<td><input type='password' name='newPwd' id='newPwd' value='' /></td>
			</tr>
			<tr>
				<td><label for='newPwdCopy'>Confirm Password: </label></td>
				<td><input type='password' name='newPwd' id='newPwdCopy' value='' /></td>
			</tr>
			<tr>
				<td colspan='2'>
					<button id='setPassword'>Submit</button>
				</td>
			</tr>
		</div>
	</div>
	
	<script>	
	(function (conf) {
		if (!arguments.length) var conf={};
		var baseURI = window.location.origin;
		var params = {};
		var queryStr = window.location.search.substr(1).split('&').map(function (p) {var arr=p.split("="); params[arr[0]]=arr[1]});	
		var securityQuestion="";
		
		
		$(document).ready(function () {			
			$('#action-register, #action-login, #action-recover').click(toggleActionDiv);
			$('#loginBtn').click(login);
			
			$('#sendRegistrationCode, #sendRecoveryCode').click(getVerificationCode);
			
			$('#setPassword').click(setPassword);
			
			if (conf.mode=='test') {
				$('#email').val(conf.email); 
				$('#pwd, #newPwd, #newPwdCopy').val(conf.pwd);
			}
		});
		
		function toggleActionDiv(e) {
			var action = e.target.id.split('-')[1];
			params.action = action;
			
			$('#pwd, #loginBtn').prop('disabled', action=='login' ? '' : 'disabled');
			//$('#recoverDiv').css('display', action=='login' ? 'none' : 'block');
			
			$('#sendRecoveryCode').prop('disabled', action=='recover' ? '' : 'disabled');
			$('#sendRegistrationCode').prop('disabled', action=='register' ? '' : 'disabled');
		}
				
		function login(res) {
			var access_token = conf.mode=='test' ? conf.recap : grecaptcha.getResponse();
			if (!access_token) {alert("You must answer a recaptcha challenge first."); return;}
			
			var email = $('#email').val(), pwd = $('#pwd').val();
			if (!email || !pwd) {alert("Missing email and/or password."); return;}
			
			$.ajax({
				url: "./tokenEmail/" + params.token_id,
				type: "POST",
				dataType: 'json',
				contentType: 'json',
				headers: {
					"Authorization": "Basic " + btoa('token-'+params.token_id + ":" + params.otk)
				},
				data: JSON.stringify({
					access_token: access_token, 
					id_type: 'email', 
					email: email, 
					pwd: pwd,
					action: 'login'	
				}),
				success: function (resp) { //console.log(resp['@graph'][0]); console.log(decodeURIComponent(params.next));		return;			
					var token = resp['@graph'][0];
					var url = decodeURIComponent(params.next);
					var separator = url.search("/\?/")!=-1 ? "&" : "?"; 
					window.location.href = url + separator + 'token_id='+ token.token_id+'&otk='+ token.otk;
				}, 
				error: httpErrFunction
			});
		}
		
		function getVerificationCode(e) {		
			var access_token = conf.mode=='test' ? conf.recap : grecaptcha.getResponse();
			if (!access_token) {alert("You must answer a recaptcha challenge first."); return;}
			
			var email = $('#email').val();
			if (!email) {alert("Missing email."); return;}
			
			$.ajax({
				url: "./tokenEmail/"+ params.token_id,
				type: "POST",
				dataType: 'json',
				contentType: 'json',
				headers: {
					"Authorization": "Basic " + btoa('token-'+params.token_id + ":" + params.otk)
				},
				data: JSON.stringify({ 
					access_token: access_token,
					email: email, 
					action: e.target.id
				}),
				success: function (resp) {
					$('#verifyDiv, #newPwdDiv').css('display', 'block');
				}, 
				error: httpErrFunction
			});			
		}
		
		function setPassword(res) {
			//recaptcha challenge should have been met while getting the verification code
			
			var email = $('#email').val(), 
				pwd = $('#newPwd').val(), pwdCopy=$('#newPwdCopy').val(),
				verCode = $('#verCode').val().toLowerCase().trim();
				
			if (!email || !pwd) {alert("Missing email and/or password."); return;}
			if (pwd!=pwdCopy) {alert("The passwords do not match."); return;}
			if (!verCode || verCode.length!=6) {alert("Invalid verificaiton code (must be 6-characters long."); return;}
			
			$.ajax({
				url: "./tokenEmail/" + params.token_id,
				type: "POST",
				dataType: 'json',
				contentType: 'json',
				headers: {
					"Authorization": "Basic " + btoa('token-'+params.token_id + ":" + params.otk)
				},
				data: JSON.stringify({
					id_type: 'email', 
					email: email, 
					pwd: pwd,
					action: 'setPassword',
					ver_code: verCode
				}),
				success: function (resp) { console.log(JSON.stringify(resp)); console.log(decodeURIComponent(params.next));				
					var token = resp['@graph'][0]; console.log(token); //return;
					var url = decodeURIComponent(params.next);
					var separator = url.search("/\?/")!=-1 ? "&" : "?"; 
					window.location.href = url + separator + 'token_id='+ token.token_id+'&otk='+ token.otk;
				}, 
				error: httpErrFunction
			});
		}
		
		function httpErrFunction(xhr, status, text) {
			var resp = JSON.parse(xhr.responseText); console.log(status+' '+text+' '+resp.error);
			alert(status+' '+text+' '+resp.error); 
		}
	})(<?php echo '{
		"email": "'. ((SITE=='dev' AND TEST_EMAIL) ? TEST_EMAIL : '') .'",
		"pwd": "'. ((SITE=='dev' AND TEST_PWD) ? TEST_PWD : '') .'",
		"mode": "'. ((SITE=='dev') ? 'test' : '') .'",
		"recap": "'. ((SITE=='dev') ? 1 : 0) .'"
	}'; ?>); 
	</script>
</body>
</html>
 