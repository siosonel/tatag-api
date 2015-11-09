<?php 

if (isset($_GET['slate']) AND $_GET['slate']) {
	$content = file_get_contents("../_exclude/slate_content.html"); 
}
else {
	include_once "Parsedown.php";
	$Parsedown = new Parsedown();
	$content = $Parsedown->text(file_get_contents('docs.md'));

	$docs = json_decode(file_get_contents("docs.json"));
	foreach($docs AS $heading => $topic) {
		$h = strtolower($heading);
		$content = str_replace("<h1>$heading</h1>", "<h1 id='$h'>$heading</h1>", $content);
		
		if (isset($topic->concepts)) {
			foreach($topic->concepts AS $i => $term) {
				$content = str_replace("<h2>$term</h2>", "<h2 id='$term'>$term</h2>", $content);
				
				if (file_exists("examples/$h-$term.json")) {
					$r = json_decode(file_get_contents("examples/$h-$term.json"));					
					$thead = "<thead><tr><th>Property</th><th>Type</th><th>Description</th></tr></thead>";
					$trs = "";
					
					foreach($r AS $k=>$v) {
						$trs .= "<tr><td>$k</td><td>". gettype($v) ."</td><td>...</td></tr>";
					}
					
					$content = str_replace("$h-$term-table", "<table>$thead<tbody>$trs</tbody></table>", $content);
				}
			}
		}
	} 
}

?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<title>API Reference</title>

	<link href="/common2/lib/slate/screen.css" rel="stylesheet" type="text/css" media="screen" />
	<link href="/common2/lib/slate/print.css" rel="stylesheet" type="text/css" media="print" />
	
	<style>
	.tocify-subheader {
		.tocify-subheader {
			.tocify-item>a {
				// Styling here for a level 2 nesting. For example -> 
				padding-left: $nav-padding + $nav-indent * 2;
				font-size: 12px;
			}
		}
	}
	</style>
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<script src="/common2/lib/slate/all.js" type="text/javascript"></script>

		<script>
			$(function() {
				setupLanguages([/*"shell","ruby","python",*/"javascript"]);
			});
		</script>
</head>
<body class="index">
	<a href="#" id="nav-button">
		<span>
			NAV
			<!--<img src="images/navbar.png" />-->
		</span>
	</a>
	<div class="tocify-wrapper">
		<img src="/ui/css/logo5.png" />
			<div class="lang-selector">
						<!---<a href="#" data-language-name="shell">shell</a>
						<a href="#" data-language-name="ruby">ruby</a>
						<a href="#" data-language-name="python">python</a>-->
						<a href="#" data-language-name="javascript">javascript</a>
			</div>
			<div class="search">
				<input type="text" class="search" id="input-search" placeholder="Search">
			</div>
			<ul class="search-results"></ul>
		<div id="toc">
		</div>
			<ul class="toc-footer">
					<li><a href='#'>Sign Up for a Developer Key</a></li>
					<li><a href='http://github.com/tripit/slate'>Documentation Powered by Slate</a></li>
			</ul>
	</div>
	<div class="page-wrapper">
		<div class="dark-box"></div>
		<div class='content'>
			<?php echo $content ?>				
		</div>
		<div class="dark-box">
			<div class="lang-selector">
						<!--<a href="#" data-language-name="shell">shell</a>
						<a href="#" data-language-name="ruby">ruby</a>
						<a href="#" data-language-name="python">python</a>-->
						<a href="#" data-language-name="javascript">javascript</a>
			</div>
		</div>			
	</div>
</body>
</html>
