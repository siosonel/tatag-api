function PhlatViewer(url) {
	if (!url) var url = "/api";
	var $preview = $('#preview');
	var $navtab = $('#navtab');
	var $cache = $('#cache');
	var lastView;
	var context;
	
	var api = PhlatClient({url: url, userid:21, pass:'pass2'});
	
	function main(e) {
		if (!e) return;
		if (e.target && e.target.className=='link' && e.target.innerHTML) url = e.target.innerHTML;
		else if (typeof e=='string') url = e;
		else return;		
		
		api.loadURL(url)
			.then(render, api.errHandler);
	}
	
	function initRender(resp) {
		context = api.context;
		render(resp);
	}
	
	function render(resp) { //console.log(resp);
		var copy = resp; //JSON.parse(JSON.stringify(resp));
		
		if (copy['@graph']) copy['@graph'].map(highlightLinks);	
		else if (Array.isArray(copy)) copy.map(highlightLinks)
		else highlightLinks(copy);
		
		$preview.html(JSON.stringify(copy, null, '     '));
	}
	
	function highlightLinks(obj) {		
		for(var prop in obj) {
			if (prop=='items') obj.items.map(highlightLinks);
			else if (prop=="@id" 
				|| (typeof obj[prop]=='string' &&
						context[prop] && (
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
	
	function init() {	
		api
			.onEnter(
				'@type', 
				['root', 'user', 'userAbout', 'userMemberships', 
					'userAccounts', 'userAccount', 'account',
					'brand', 'brandCollection', 
					'brandAbout', 'promoCollection'], 
				[displayCachedTypes]
			)
			.init(initRender);
			
		$preview.on('click', main);
		$cache.on('click', function (e) {api.loadType(e.target.innerHTML).then(render)});
	}
	
	main.init = init;	
	main.api = api; //expose for now
	
	
	main.testCopy = function testCopy(type) {
		if (!type) {console.log('missing type'); return;}
		$preview.html(JSON.stringify(api.copy('/user/21/'+type), null, '     '));
	}
	
	return main;
}