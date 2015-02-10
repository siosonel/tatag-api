assert = require('assert');
request = require('supertest')('http://localhost/tatag');
Q = require('q');

var help = require('helpers/helpers.js');

var api = require('helpers/ld-flat.js').api({
	'userid': '21', 
	'pass': 'pass2' 
});


before(help.initDB);


describe('Definitions', function () {
	it('provides contextual links at API root', function (done) {
		// for hypermedia clients, the root path should be the only known path out-of-band, 
		// all other resource locations are provided by API
		api.init('/').then(help.inspect(done), done);
	})
	
	it('provides definitions', function (done) {
		api.loadType('definitions').then(help.inspect(done), done)
	})
	
	it('provides testable resource views and actions', function (done) {
		api.byType.definitions.resourceTypes.map(testResource);
		done();
	})
})


function testResource(type) {
	var skip =[]; //skip = ['userAccounts', 'userMemberships', 'userAccounts', 'brandMembers', "brandAccounts", "brandHolders",  "userCollection", "brandCollection"];
	if (type.search('#') == 0 || skip.indexOf(type)!=-1) return;	
	
	var formIDs, currResource;
	
	describe(type+' resource', function () {
		it('should provide '+ type +' resource', function (done) {
			api.loadType(type).then(help.inspect(done), done)
		})
		
		it('should match '+type+" definitions", function (done) { //if (type=='budgetIssued') console.log(api.curr[type]);
			var defs = api.byType.definitions, props=defs[type].properties;
			assert.equal(undefined, help.compareKeys(api.curr[type], props.required, props.optional))
			done()
		})

		it('should provide '+type+' actions', function (done) {
			api.loadType(type).then(function (resource) {
				var mssg='';
				currResource = resource;
				if (!resource.actions) mssg = 'No '+resource['@type']+' actions.';
				formIDs = resource.actions;
				done(mssg ? new Error(mssg) : null);
			})
		})
		
		it('should follow documented action examples', function (done) {
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
		})
		
		it('may have dereferenceable links', function (done) {
			if (!currResource.links) done();
			else api.deref(currResource.links).then(help.inspect(done), done)			
		})
	})
}
	
	
/*
describe('records', function () {		
	it('should allow budget creation', function (done) {
		request.post('/budgetIssuance')
			.auth('21','pass2')
			.send({
				from: "41-abc",
				to: "42-abc",
				amount: 1000,
				comment: 'first budget'
			})
			.expect(200)
			.expect(help.inspect('body'))
			.end(done);
	});
	
	it.skip("should NOT allow budget creation if an account's authcode do not have a 'c' character", function (done) {
		request.post('/budgetIssuance')
			.auth('21','pass2')
			.send({
				from: "41-abc",
				to: "42-abc",
				amount: 1000,
				comment: 'first budget'
			})
			.expect(403)
			.expect(help.inspect('body'))
			.end(done);
	});
	
	it('should allow budget assignment', function (done) {
		request.post('/budgetTransfer')
			.auth('21','pass2')
			.send({
				from: "42-abc",
				to: "43-abc",
				amount: 35.87,
				comment: 'wages'
			})
			.expect(200)
			.expect(help.inspect('body'))
			.end(done);
	});
	
	it.skip("should NOT allow budget assignment from an account that does not have a 'f' authcode", function (done) {
		request.post('/budgetTransfer')
			.auth('21','pass2')
			.send({
				from: "42-abc",
				to: "43-abc",
				amount: 6.66,
				comment: 'returned pay',
				cart_id: 0
			})
			.expect(403)
			.expect(help.inspect('body'))
			.end(done);
	});
	
	it('should allow budget intrause', function (done) {
		request.post('/budgetUse')
			.auth('21','pass2')
			.send({
				from: "42-abc",
				to: "41-abc",
				amount: 2.05,
				comment: 'disounted employee purchase'
			})
			.expect(200)
			.expect(help.inspect('body'))
			.end(done);
	});	
	
	it('should NOT allow budget intrause when either from or to-account balance is insufficient', function (done) {
		request.post('/budgetUse')
			.auth('21','pass2')
			.send({
				from: "42-abc",
				to: "41-abc",
				amount: 20000000.05,
				comment: 'disounted employee purchase'
			})
			.expect(403)
			.expect(help.inspect('body'))
			.end(done);
	});
	
	it('should allow external budget use', function (done) {
		request.post('/budgetUse')
			.auth('21','pass2')
			.send({
				from: "42-abc",
				to: "44-abc",
				amount: 9.37,
				comment: 'first external budget use'
			})
			.expect(200)
			.expect(help.inspect('body'))
			.end(done);
	});
	
	it.skip("should NOT allow external budget use from an account that does not have an 'x' authcode", function (done) {
		request.post('/budgetUse')
			.auth('21','pass2')
			.send({
				from: 0,
				to: 0,
				amount: 6.69,
				comment: 'purchase'
			})
			.expect(403)
			.expect(help.inspect('body'))
			.end(done);
	});
		
	it.skip('should NOT allow external budget use if the relay is invalid', function (done) {
		request.post('/budgetUse')
			.auth('22','pass2')
			.send({
				from: 0,
				to: 0,
				amount: 9.37,
				comment: 'first external budget use',
				cart_id: 0
			})
			.expect(401)
			.expect(help.inspect('body'))
			.end(done);
	});
})
*/