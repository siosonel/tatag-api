window.phlat = (function phlatCrawler() {
	var root, directions, headers;
	var get = {};
	var cache  = {};
	var inprocess = {};
	var testMode = "";
	
	function init(opts) {	
		headers = opts.headers;
	
		$.ajax({
			url: opts.url,
			header: opts.headers,
			success: function (resp) {
				resp['@graph'].map(addToCache);
				root = resp['@graph'][0];
				directions = root.navDirections;
				
				for(var audience in directions) get[audience] = {};
				
				if (opts.listeners) {
					for(var audience in opts.listeners) {
						for(var i=0; i<opts.listeners[audience].length; i++) {					
							for(var term in opts.listeners[audience][i]) {		
								addListener(audience, term, opts.listeners[audience][i][term]);
							}
						} 
					}
				} 
				
				if (typeof opts.postInit == 'function') opts.postInit();
			},
			error: errHandler
		});
	}
	
	function main(audience, term) {
		get[audience][term]();
	}
	
	function addListener(audience, term, fxn) {
		if (!(term in get[audience])) get[audience][term] = getFxn(term, audience, directions[audience][term], headers);
		get[audience][term].addListener(fxn);
	}
	
	function addToCache(obj) { 
		if (obj['@id'] in cache) $.extend(cache[obj['@id']], obj);
		else cache[obj['@id']] = obj;
	}
	
	function getFxn(term, audience, path, headers) {
		var url='';
		var pathIndex = 0, links, concurrent=1;
		var container; //the resource to which the current request is related
		var listeners = [];
		var currData;
		
		function main(resource) {
			if (!arguments.length) resource = root;
			container = resource;
			
			var intermediateTerm = path[pathIndex];
			links = resource[intermediateTerm];
			
			if (!Array.isArray(links)) links = [links];
			concurrent = 0;
			
			for(var i=0; i<links.length; i++) {
				var url = links[i];
				
				if (typeof url=='string' && url in cache) {
					processServerResponse(cache[url]);				
				}
				else if (typeof url != 'string') {
					processServerResponse(url);
				}
				else if (inprocess[url]) {
					//do not trigger duplicate requests in cases where the same
					//path segment is used for different terms
					inprocess[url].push(processServerResponse);
				} 
				else {
					inprocess[url] = [];
					
					$.ajax({
						url: url,
						headers: headers,
						success: processServerResponse,
						error: errHandler
					});
				}
			}
		}
		
		function processServerResponse(resp) { if (!resp) {console.log([term, audience, path, pathIndex, concurrent]); return;}
			if ('@graph' in resp) {
				resp['@graph'].map(addToCache);
				var resource = resp['@graph'][0];
			}
			else {
				var resource = resp;
			}
			
			concurrent++;
			links[links.indexOf(resource['@id'])] = resource; //substitute link url with response object
			
			if (concurrent < links.length) {}
			else if (pathIndex < path.length-1) {
				pathIndex++;
				main(links[0]);
			}
			else {
				var data = typeof container[path[pathIndex]]=='string' ? resource : links;
				for(var i=0; i<listeners.length; i++) listeners[i](data); 
				
				if (inprocess[resource['@id']]) {
					for(var i=0; i<inprocess[resource['@id']].length; i++) inprocess[resource['@id']][i](data); 
					inprocess[resource['@id']] = [];
				}
				
				pathIndex = 0;
				concurrent = 0;
				
				if (testMode=='save') save(data, audience+'-'+term);
				if (testMode=='compare') compare(data);
			}
		}
		
		function compare(data) {
			currData = data;
			$.ajax({
				url: 'examples.php?name='+ audience+'-'+term,
				type: "GET",
				contentType: 'json',
				success: compareEncoded,
				error: errHandler
			});
		}
		
		function compareEncoded(resp) {
			if (JSON.stringify(currData) != JSON.stringify(resp)) console.log('Failed match for '+audience+'-'+term+'.');
			else console.log('Passed: '+audience+'-'+term+'.');
		}
		
		main.addListener = function (fxn) {
			if (listeners.indexOf(fxn)==-1) listeners.push(fxn);
		}
		
		main.listeners = listeners;
		
		return main;
	}
	
	function errHandler(xhr, status, text) {
		console.log(status+': '+text);
	}
	
	
	function save(data, name) {
		$.ajax({
			url: 'examples.php?name='+ name,
			type: "POST",
			data: JSON.stringify(data),
			dataType: 'json',
			contentType: 'json',
			success: function () {console.log('saved '+name)},
			error: errHandler
		});
	}
	
	main.cache = cache;
	main.request = main;
	main.init = init;
	main.addListener = addListener;	
	
	main.test = function (d, mode) {
		testMode = arguments.length>1? mode: ""; console.log(testMode);
	
		for(var audience in directions) {
			if (d=='*' || d==audience) {
				for(var term in directions[audience]) {
					if (!(term in get[audience])) get[audience][term] = getFxn(term, audience, directions[audience][term], headers);
					get[audience][term]();
				}
			}
		}
	}	
	
	return main;
})();