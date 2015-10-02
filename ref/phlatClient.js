function PhlatClient(conf) {
	var cache = {};
	var hints = {};
	var root, context;
	var on = {enter:{}, update:{}, exit: {}};
	var initListener;
	var tracked = {};
	var responseHandler;
	var listeners = {
		enter: setListeners('enter'), 
		update: setListeners('update'), 
		exit: setListeners('exit')
	};
	
	function main() {}
	
	function init(fxn) {
		initListener = fxn;
		
		tracked[conf.url]={enter:[], update: [], exit: []};
		responseHandler = setRoot;
		request(conf.url);
	}
	
	function request(url) {		console.log(url)
		$.ajax({
			url: url,
			headers: {
				"Authorization": "Basic " + btoa(conf.userid + ":" + conf.pass)
			},
			success: responseHandler,
			error: errHandler
		});
	}
	
	function setRoot(resp) { console.log(resp);
		root = resp;
		responseHandler = setContext;
		request(root['@context']);	
	}
	
	function setContext(resp) {
		context = resp;	
		main.context = resp;
		responseHandler = processResponse;
		
		processObj(root['@graph'][0],0,conf.url);
		tracked[conf.url].enter.map(listeners.enter)
		initListener(root);
	}
	
	function processResponse(resp) {
		var g = resp['@graph'], fromURL = g[0]['@id'];
		
		for(var i=0; i<g.length; i++) {
			processObj(g[i], i, fromURL);
		} //console.log(tracked);		
		
		if (tracked[fromURL]) {
			for(var eventType in tracked[fromURL]) {tracked[fromURL][eventType].map(listeners[eventType]);}
		}
		
		return (new Promise(function (resolve, reject) {
			resolve(resp);
		}));
	}
	
	function processObj(obj,i,fromURL) {
		var id = obj['@id'];
		
		if (!id) return;
		
		if (!cache[id]) {
			cache[id] = {};
			if (fromURL) tracked[fromURL][obj ? 'enter' : 'exit'].push(obj);
		}
		
		if (i==0) cache[id] = obj;
		else {
			for(var prop in obj) {
				if (fromURL && cache[id][prop] != obj[prop]) tracked[fromURL].update.push(obj);
			
				cache[id][prop] = obj[prop];
			}			
		}
	}
	
	function setListeners(eventType) {
		return function (obj) { 
			for(var alias in on[eventType]) { 
				var o = on[eventType][alias]; //console.log([alias, o.key, o.val, obj[o.key]]);
				
				if (obj[o.key] == o.val) {
					for(var i=0; i<o.listeners.length; i++) o.listeners[i](obj);
				}
			}
		}
	}
	
	function errHandler(xhr, status, text) {
		console.log(status+' '+text);
	}
	
	function notifyListeners() {
		var types={};
		for(var t=0; t<updatedTypes.length; t++) {
			var type = updatedTypes[t]['@type'];
			if (type) {
				if (!types[type]) types[type] = [];
				types[type].push(updatedTypes[t]);
			}
		}
	}
	
	function onFxnSetter(eventType) {
		return function setListeners(key, val, fxnArr) { console.log(eventType);
			if (typeof val=='string') on[eventType][key+'='+val] = {key: key, val: val, listeners: fxnArr};
			else if (Array.isArray(val)) {
				for(var i=0; i<val.length; i++) {
					on[eventType][key+'='+val[i]] = {key: key, val: val[i], listeners: fxnArr};
				}
			} else console.log(val);
			
			return main;
		}
	}
	
	main.errHandler = errHandler;
	
	main.init = init;
		
	main.onEnter = onFxnSetter('enter');
	
	main.onUpdate = onFxnSetter('update');

	main.onExit = onFxnSetter('exit');	
		
	main.loadURL = function (url) {
		tracked[url]={enter:[], update: [], exit: []};
		
		return (Promise.resolve($.ajax({
			url: url,
			headers: {
				"Authorization": "Basic " + btoa(conf.userid + ":" + conf.pass)
			},
			error: errHandler
		}))).then(processResponse, errHandler);
	}
	
	main.loadType = function (type) {
		var arr = [];
		for(var id in cache) {
			if (cache[id]['@type']==type) arr.push(cache[id]);
		}
		
		return Promise.resolve(arr);
	}
	
	return main;
}