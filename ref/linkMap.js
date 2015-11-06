function linkMap(m) {
	var concepts = {};
	var ignored = {};
	var rawMap = {};
	var relArr = [];
	var refArr = []; //used when the cocenpt reference is not equal to the relation type 
	var mappedTerms = {rel:{}, ref:{}};
	var directions = {};
	var collapsePath = 0;
	var untrackedTerms = {};
	var termFreq = {};
	
	function main(m, arr) {
		if (!m) return;
		
		for(var link in m) {
			if (link=="#") trackConceptRef(m[link], arr);
			else trackConceptRel(m, link, arr);
		}
		
		return relArr;
	}
	
	function trackConceptRef(termArr, arr) {
		if (!arr) arr = [];
		if (typeof termArr == 'string') termArr = [termArr]; 
		
		for(var i=0; i<termArr.length; i++) {
			refArr.push(arr.concat(termArr[i]));
			trackFreq(termArr[i]);					
		}
	}
	
	function trackConceptRel(m, link, arr) {	
		if (!(link in concepts)) trackFreq(link);	
		
		if (!arr) arr = [];			
		relArr.push(arr.concat([link]));
		
		var audience = arr.length ? arr[0] : link;
		
		if (typeof m[link] === "object" 
			&& m[link] !== null 
			&& !Array.isArray(m[link])
		) {
			main(m[link], arr.concat([link]));
		}
	}
	
	function trackFreq(term) {
		if (!termFreq[term]) termFreq[term] = 0;
		termFreq[term]++;
	}
	
	function mapRelsByAudience(arr) {
		if (!arr.length) return;
		var audience = arr.shift();
		var term = arr[arr.length-1];
		
		if (!(audience in mappedTerms.rel)) {
			mappedTerms.rel[audience] = {};
		}
		else if (concepts[audience].indexOf(term)!=-1) {
			mappedTerms.rel[audience][term] = collapsePath ? arr.join(",") : arr;
		}
		else {
			if (!untrackedTerms[audience]) untrackedTerms[audience] = [];
			if (untrackedTerms[audience].indexOf(term)==-1) untrackedTerms[audience].push(term);
		}
	}
	
	function mapRefsByAudience(arr) {
		if (!arr.length) return;
		var audience = arr.shift();
		var term = arr.pop();
		
		if (!(audience in mappedTerms.ref)) {
			mappedTerms.ref[audience] = {};
		}
		
		if (concepts[audience].indexOf(term)!=-1) {
			mappedTerms.ref[audience][term] = collapsePath ? arr.join(",") : arr;
		}
		else {
			if (!untrackedTerms[audience]) untrackedTerms[audience] = [];
			if (untrackedTerms[audience].indexOf(term)==-1) untrackedTerms[audience].push(term);
		}
	}
	
	function sortDesc(a,b) {
		return b[1] - a[1];
	}
	
	main.byAudience = function (resp, collapse) {
		collapsePath = arguments.length==2 ? collapse : 0;
		main(resp);
		
		relArr.map(mapRelsByAudience);
		refArr.map(mapRefsByAudience);
		directions = {};
		
		for(var refOrRel in mappedTerms) {
			for(var audience in mappedTerms[refOrRel]) {				
				for(var term in mappedTerms[refOrRel][audience]) {
					if (!(audience in directions)) directions[audience] = {};
					directions[audience][term] = mappedTerms[refOrRel][audience][term];
				}
			}
		}
		
		return directions;
	}
	
	main.concepts = function (resourceConcepts) {
		if (!arguments.length) return concepts;
		concepts = resourceConcepts;
		return main;
	}
	
	main.ignored = function (resourceIgnored) {
		if (!arguments.length) return ignored;
		ignored = resourceIgnored;
		return main;
	}
	
	main.rawMap = function (map) {
		if (!arguments.length) return rawMap;
		rawMap = map;
		return main;
	}
	
	main.termFreq = function () {
		var arr = [], arr1 = [];
		for(var term in termFreq) arr.push([term, termFreq[term]]);
		arr.sort(sortDesc);
		for(var i=0; i<arr.length; i++) arr1.push(arr[i].join(": "));
		return arr1;
	}
	
	main.untrackedTerms = function () {
		var obj = {_total_: 0}, total=0; 
		for(var audience in untrackedTerms) {
			obj[audience] = [];
			
			for(var i=0; i<untrackedTerms[audience].length; i++) {
				var term = untrackedTerms[audience][i];
				if (ignored['*'].indexOf(term)==-1 && (!(audience in ignored) || ignored[audience].indexOf(term)==-1)) {
					obj[audience].push(term);  
					total++;
				}
			}
		}
		
		obj._total_ = total;
		return obj;
	}
	
	main.unreachableTerms = function () {
		var obj = {_total_: 0}, total=0;
		
		for(var audience in concepts) {
			for(var i=0; i<concepts[audience].length; i++) { 
				var term = concepts[audience][i];
				if (!(audience in directions) || !(term in directions[audience])) {
					if (!(audience in obj)) obj[audience] = [];
					obj[audience].push(term);
					total++;
				}
			}
		}
		
		obj._total_ = total;		
		return obj;
	}
	
	main.directions = function () {
		for(var audience in directions) {
			for(var term in directions[audience]) {
				directions[audience][term] = directions[audience][term].split(",");
			}
		}
		
		return directions;
	}
	
	return main;
}