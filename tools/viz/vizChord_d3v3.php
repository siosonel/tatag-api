<!DOCTYPE html>
<html>
<head>
	<title>VizChord</title>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
	<!--<link type="text/css" rel="stylesheet" href="style.css"/>-->
	<style type="text/css">

path.arc {
  cursor: move;
  fill: #fff;
}

.node {
  font-size: 10px;
}

.node:hover {
  fill: #1f77b4;
}

.link {
  fill: none;
  stroke: #1f77b4;
  stroke-opacity: .4;
  pointer-events: none;
}

.link.source, .link.target {
  stroke-opacity: 1;
  stroke-width: 2px;
}

.node.target {
  fill: #d62728 !important;
}

.link.source {
  stroke: #d62728;
}

.node.source {
  fill: #2ca02c;
}

.link.target {
  stroke: #2ca02c;
}

</style>
</head>
<body>
	<div>
		<br />
		<label for='tickNum'>Tick Number&nbsp;&nbsp;</label><input type='text' name='tickNum' id='tickNum' value="1" onchange='updateChord()' />
	</div>
	<h2>Transactions between brands</h2>
	<div style="position:absolute;bottom:0;font-size:18px;">tension: <input style="position:relative;top:3px;" type="range" min="0" max="100" value="85"></div>
	<script type="text/javascript" src="/common2/lib/d3/d3.v3.min.js"></script>
	<script type="text/javascript" src="vizChord_packages.js"></script>
	<script type="text/javascript">

	var packages;
	var params = {tickNum: 1};
	var serverData = {};
		
	var diameter = 600,
	radius = diameter / 2,
	innerRadius = radius - 120;

	var cluster = d3.layout.cluster()
			.size([360, innerRadius])
			.sort(null)
			.value(function(d) { return d.size; });

	var bundle = d3.layout.bundle();

	var line = d3.svg.line.radial()
			.interpolate("bundle")
			.tension(.85)
			.radius(function(d) { return d.y; })
			.angle(function(d) { return d.x / 180 * Math.PI; });

	var svg = d3.select("body").append("svg")
			.attr("width", diameter)
			.attr("height", diameter)
		.append("g")
			.attr("transform", "translate(" + radius + "," + radius + ")");

	var link = svg.append("g").attr('id', 'linkGrp').selectAll(".link"),
			node = svg.append("g").attr('id', 'nodeGrp').selectAll(".node");


	function mouseovered(d) {
		node
				.each(function(n) { n.target = n.source = false; });

		link
				.classed("link--target", function(l) { if (l.target === d) return l.source.source = true; })
				.classed("link--source", function(l) { if (l.source === d) return l.target.target = true; })
			.filter(function(l) { return l.target === d || l.source === d; })
				.each(function() { this.parentNode.appendChild(this); });

		node
				.classed("node--target", function(n) { return n.target; })
				.classed("node--source", function(n) { return n.source; });
	}

	function mouseouted(d) {
		link
				.classed("link--target", false)
				.classed("link--source", false);

		node
				.classed("node--target", false)
				.classed("node--source", false);
	}

	d3.select(self.frameElement).style("height", diameter + "px");

	// Lazily construct the package hierarchy from class names.
	function packageHierarchy(classes) {
		var map = {};

		function find(name, data) {
			var node = map[name], i;
			if (!node) {
				node = map[name] = data || {name: name, children: []};
				if (name.length) {
					node.parent = find(name.substring(0, i = name.lastIndexOf(".")));
					node.parent.children.push(node);
					node.key = name.substring(i + 1);
				}
			}
			return node;
		}

		classes.forEach(function(d) {
			find(d.name, d);
		});

		return map[""];
	}

	// Return a list of imports for the given array of nodes.
	function packageImports(nodes) {
		var map = {},
				imports = [];

		// Compute a map from name to node.
		nodes.forEach(function(d) {
			map[d.name] = d;
		});

		// For each import, construct a link from the source to target node.
		nodes.forEach(function(d) {
			if (d.imports) d.imports.forEach(function(i) {
				imports.push({source: map[d.name], target: map[i]});
			});
		});

		return imports;
	}

	function updateChord(e) {
		var val = d3.select('#tickNum').property('value');
		if (!val) return;
		params.tickNum = val;
		
		if (serverData[params.tickNum]) processData(null, serverData[params.tickNum]); 
		else d3.json("vizChord_data.php?tickNum="+ params.tickNum, processData);
	}
	
	function processData(error, classes) {
		serverData[params.tickNum] = classes;
		
		var nodes = cluster.nodes(packageHierarchy(classes)),
				links = packageImports(nodes);

		var path = d3.select('#linkGrp')
				.selectAll(".link")
				.data(bundle(links))
		
		path.exit().remove();
		
		link = path.enter().append("path")
				.each(function(d) { d.source = d[0], d.target = d[d.length - 1]; })
				.attr("class", "link")
				.attr("d", line);
		
		var n = d3.select('#nodeGrp')
				.selectAll(".node")
				.data(nodes.filter(function(n) { return !n.children; }))
		
		n.exit().remove();
		
		node = n.enter().append("text")
				.attr("class", "node")
				.attr("dy", ".31em")
				.attr("transform", function(d) { return "rotate(" + (d.x - 90) + ")translate(" + (d.y + 8) + ",0)" + (d.x < 180 ? "" : "rotate(180)"); })
				.style("text-anchor", function(d) { return d.x < 180 ? "start" : "end"; })
				.text(function(d) { return d.key; })
				.on("mouseover", mouseovered)
				.on("mouseout", mouseouted);
		
		d3.select("input[type=range]").on("change", function() {
			line.tension(this.value / 100);
			path.attr("d", function(d, i) { return line(splines[i]); });
		});
	}

	updateChord();

    </script>
  </body>
</html>