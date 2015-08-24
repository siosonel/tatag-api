<html>
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
		
		#recoverDiv {
			display: none;
		}
		
		#recoverDiv button {
			display: none;
			position: relative;
			left: 80%;
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
			<label for='action-recover'>I forgot my password.</label>
		</div>
		
		<div id='registerDiv'>
			<input type='radio' name='action' id='action-register' value='register'/>
			<label for='action-register'>I don't have a password and need to register.</label>
		</div>
		
		<div id='recoverDiv' >
			<label for='newPwd'>New Password: </label>
			<input type='password' name='newPwd' id='newPwd' value='' />
			<br />
			<br />			
			<label for=''>Security Question: </label><br />
			<textarea name='question' id='question' rows='3'></textarea>
			<br />
			<br />
			<label for=''>Recovery Answer: </label><br />
			<textarea name='answer' id='answer' rows='3'></textarea>
			<br />
			<br />			
			<button id='recoverBtn'>Submit</button>
			<button id='registerBtn'>Submit</button>
		</div>
	</div>
	
	<script>	
	(function () {
		var baseURI = window.location.origin;
		var params = {};
		var queryStr = window.location.search.substr(1).split('&').map(function (p) {var arr=p.split("="); params[arr[0]]=arr[1]});	
		var securityQuestion="";
		
		$(document).ready(function () {
			$('#email').val('user21@email.org'); $('#pwd, #newPwd').val('pass2'); $('#question, #answer').val('test');
			
			$('#action-register, #action-login, #action-recover').click(toggleActionDiv);
			$('#loginBtn').click(login);
			$('#recoverBtn').click(recover);
			$('#registerBtn').click(register);
		});
		
		function toggleActionDiv(e) {
			var action = e.target.id.split('-')[1];
			
			$('#pwd, #loginBtn').prop('disabled', action=='login' ? '' : 'disabled');
			$('#recoverDiv').css('display', action=='login' ? 'none' : 'block');
			$('#recoverBtn').css('display', action=='recover' ? 'block' : 'none');
			$('#registerBtn').css('display', action=='register' ? 'block' : 'none');
		}
				
		function login(res) {
			var recapCode = 1;//grecaptcha.getResponse();
			//if (!recapCode) {alert("You must answer a recaptcha challenge first."); return;}
			
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
					access_token: recapCode, 
					id_type: 'email', 
					email: email, 
					pwd: pwd,
					action: 'login'	
				}),
				success: function (resp) { console.log(resp); console.log(decodeURIComponent(params.next));		return;			
					var token = resp['@graph'][0];
					var url = decodeURIComponent(params.next);
					var separator = url.search("/\?/")!=-1 ? "&" : "?"; 
					window.location.href = url + separator + 'token_id='+ token.token_id+'&otk='+ token.otk;
				}, 
				error: function (xhr, status, text) {
					console.log(status+' '+text)
				}
			})
		}
		
		function register(res) {
			var recapCode = 1;//grecaptcha.getResponse();
			//if (!recapCode) {alert("You must answer a recaptcha challenge first."); return;}
			
			var email = $('#email').val(), pwd = $('#pwd').val();
			var question = $('#question').val(), answer = $('#answer').val();
			
			if (!email || !pwd) {alert("Missing email and/or password."); return;}
			if (!question || !answer) {alert("Missing email and/or password."); return;}
			
			$.ajax({
				url: "./tokenEmail/" + params.token_id,
				type: "POST",
				dataType: 'json',
				contentType: 'json',
				headers: {
					"Authorization": "Basic " + btoa('token-'+params.token_id + ":" + params.otk)
				},
				data: JSON.stringify({
					access_token: recapCode, 
					id_type: 'email', 
					email: email, 
					pwd: pwd,
					action: 'register',
					question: question,
					answer: answer
				}),
				success: function (resp) { console.log(resp); console.log(decodeURIComponent(params.next));		return;			
					var token = resp['@graph'][0];
					var url = decodeURIComponent(params.next);
					var separator = url.search("/\?/")!=-1 ? "&" : "?"; 
					window.location.href = url + separator + 'token_id='+ token.token_id+'&otk='+ token.otk;
				}, 
				error: function (xhr, status, text) {
					console.log(status+' '+text)
				}
			})
		}
		
		function recover() { // TO-DO
		
		}

	})();
	</script>
</body>
</html>
 