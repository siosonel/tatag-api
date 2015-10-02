<html>
<head>
	<title>API viewer</title>
	<link rel="icon" type="image/png" href="/ui/css/logo5.png">
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<style>
		#navtab {
			position: absolute;
			top: 1rem;
			right: 1 rem;
		}
		
		#preview {
			min-height: 80%;
			width: 80%;
			padding: 1rem;
			background-color: #ececec;
			overflow-x: scroll;
		}
		
		.link {
			color: blue;
			text-decoration: underline;
			cursor: pointer;
		}
		
		#cache {
			position: absolute;
			top: 3rem;
			right: 1rem;
			width: 12%;
			min-height: 80%;
			padding: 0.5rem;
			text-align: center;
			background-color: #efefef;
		}
		
		#cache button {
			margin: 0.25rem auto;
		}
	</style>
</head>
<body>
	<!--<div id='navtab'>
		<button id='backBtn'>Back</button> | 
		<button id='fwdBtn'>Forward</button> 
	</div>-->
	
	<h2>Preview</h2>
	<pre id='preview'></pre>
	<div id='cache'></div>
</body>

<script type="text/javascript" src="/common2/lib/jQuery/jquery-1.8.2.min.js"></script>
<script type="text/javascript" src="/api/ref/phlatClient.js"></script>
<script type="text/javascript" src="/api/ref/viewer.js"></script>
<script>
	var Viewer = PhlatViewer();
	Viewer.init();
</script>
	
</script>
</html>