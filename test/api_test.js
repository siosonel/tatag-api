assert = require('assert');
request = require('supertest')('http://tatag.dev/api');
Q = require('q');

var help = require('helpers/helpers.js');

var api = require('helpers/ld-flat.js').api({
	'userid': '21', 
	'pass': 'pass2',
	'baseURL': '',
	'request': request
});


before(help.initDB);


describe('Definitions', function () {
	it('provides contextual links at API root', function (done) {
		// for hypermedia clients, the root path should be the only known path out-of-band, 
		// all other resource locations are provided by API
		api.init('/').then(help.inspect(done), done);
	});
	
	it('provides definitions', function (done) {
		api.loadType('definitions').then(help.inspect(done), done)
	});
	
	it('provides testable resource views and actions', function (done) {
		api.byType.definitions.resourceTypes.map(testResource);
		done();
	});
})


function testResource(type) {
	var skip =[]; //skip = ['userAccounts', 'userMemberships', 'userAccounts', 'brandMembers', "brandAccounts", "brandHolders",  "userCollection", "brandCollection"];
	if (type.search('#') == 0 || skip.indexOf(type)!=-1) return;	
	
	var formIDs, currResource;
	var currDef = api.byType.definitions[type];
	
	describe(type+' resource', function () {
		it('should provide '+ type +' resource', function (done) {
			this.timeout(5000);
			if (currDef.testURL) api.loadId(currDef.testURL).then(help.inspect(done), done);
			else api.loadType(type).then(help.inspect(done), done)
		});
		
		it('should match '+type+" definitions", function (done) {
			this.timeout(5000);
			var props= currDef.properties;
			assert.equal(undefined, help.compareKeys(api.curr[type], props.required, props.optional))
			done()
		});

		it('should provide '+type+' actions', function (done) {
			this.timeout(5000);
			api.loadType(type).then(function (resource) {
				var mssg='';
				currResource = resource;
				if (!resource.actions && !currDef.relatedActions && !currDef.readOnly) mssg = 'No '+resource['@type']+' actions.';
				formIDs = resource.actions;
				done(mssg ? new Error(mssg) : null);
			})
		});
		
		it('should follow documented action examples', function (done) {
			this.timeout(5000);
			var skip = []; //skip = ['user', 'userAccounts', 'brand', 'brandMembers', "brandAccounts", 'brandHolders', 'userCollection',  'brancCollection'];
			if (!formIDs || !formIDs.length || skip.indexOf(currResource['@type'])!=-1) done();
			else {
				// this helper maintains the action context within each api request, simpler than Q.all approach?
				help.wait.reset(api, done);
				
				formIDs.map(function (id) {
					var form = api.byId[id];
					if (form && form.examples) form.examples.map(help.wait); //else console.log(form);
				});
			
				help.wait.orNot();
			}
		});
		
		it('may have dereferenceable links', function (done) {
			this.timeout(5000);
			if (!currResource.links) done();
			else api.deref(currResource.links).then(help.inspect(done), done)			
		});
	})
}
	