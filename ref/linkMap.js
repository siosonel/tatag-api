function linkMap(m) {
	var linksArr = [];
	var linksObj = {};
	var collapsePath = 0;
	
	function main(m, arr) {
		if (!m) return;
		
		for(var link in m) {
			if (!arr) arr = [];
			linksArr.push(arr.concat([link]));
			if (typeof m[link] === "object" && m[link] !== null && !Array.isArray(m[link])) {
				main(m[link], arr.concat([link]));
			}
		}
		
		return linksArr;
	}
	
	function setByViewType(arr) {
		if (!arr.length) return;
		var viewType = arr.shift();
		
		if (!(viewType in linksObj)) linksObj[viewType] = [];
		else linksObj[viewType].push(collapsePath ? arr.join(",") : arr);
	}
	
	main.byViewType = function (resp, collapse) {
		collapsePath = arguments.length==2 ? collapse : 0;
		main(resp);
		
		linksArr.map(setByViewType);		
		return linksObj;
	}
	
	return main;
}