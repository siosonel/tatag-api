<html>
<head>
	<title>Link Map</title>
</head>
<body>
	<div id='rawMapDiv'>
		<pre id='rawMapPre'></pre>
	</div>
	<div id='transformedDiv'>
		<pre id='transformedPre'></pre>
	</div>
</body>
	<script type="text/javascript" src="/common2/lib/jQuery/jquery-1.8.2.min.js"></script>
	<script src='linkMap.js'></script>
	<script>
		var mapper = linkMap();
		init();
		
		function init() {
			$.ajax({
				url: "link_map.json",
				success: render,
				error: function (xhr, status, text) {console.log(status+': '+text);}
			})
		}
		
		function render(resp) {
			$('#rawMapPre').html(JSON.stringify(resp, null, '     '));
			$('#transformedPre').html(JSON.stringify(mapper.byViewType(resp, 'collapse'), null, '     '));
		}
	</script>
</html>