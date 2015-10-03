function PhlatViewer(url) {
	if (!url) var url = "/api";
	var $preview = $('#preview');
	var $navtab = $('#navtab');
	var $cache = $('#cache');
	var lastView;
	var context;
	
	var Phlat = PhlatClient({url: url, userid:21, pass:'pass2'});
	
	function main(e) {
		if (!e) return;
		if (e.target && e.target.className=='link' && e.target.innerHTML) url = e.target.innerHTML;
		else if (typeof e=='string') url = e;
		else return;		
		
		Phlat.loadURL(url)
			.then(render, Phlat.errHandler);
	}
	
	function initRender(resp) {
		context = Phlat.context;
		render(resp);
	}
	
	function render(resp) { //console.log(resp);
		var copy = JSON.parse(JSON.stringify(resp));
		
		if (copy['@graph']) copy['@graph'].map(highlightLinks);	
		else if (Array.isArray(copy)) copy.map(highlightLinks)
		else highlightLinks(copy);
		
		$preview.html(JSON.stringify(copy, null, '     '));
	}
	
	function highlightLinks(obj) {		
		for(var prop in obj) {
			if (prop=='items') obj.items.map(highlightLinks);
			else if (prop=="@id" 
				|| (context[prop] && (
						context[prop] == "Link" || context[prop]["@type"] == "Link"
						//|| context[prop] == "@id" || context[prop]["@type"] == "@id"
				))
			) obj[prop] = "<span class='link'>"+ obj[prop] + "</span>";
		}
	}
	
	function nav(e) {
		if (!e) return;
	}
	
	function errHandler(xhr, status, text) {
		console.log(status+' '+text);
	}
	
	function displayCachedTypes(obj) { //console.log(obj['@type']);
		var id = 'v_'+ obj['@type'];
		if ($('#'+id).length) return;
		$cache.append("<button id='"+id+"'>"+ obj['@type'] +"</button>");
	}
	
	function loadByType(type) {
		
	}
	
	main.init = function () {	
		Phlat
			.onEnter(
				'@type', 
				['root', 'user', 'userAbout', 'userMemberships', 'userAccounts', 'brandCollection', 'brandAbout', 'promoCollection'], 
				[displayCachedTypes]
			)
			.init(initRender);
			
		$preview.on('click', main);
		$cache.on('click', function (e) {Phlat.loadType(e.target.innerHTML).then(render)});
	}
	
	main.Phlat = Phlat; //expose for now
	
	return main;
}