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
	var skip =[]; skip = [];
	if (type.search('#') == 0 || skip.indexOf(type)!=-1) return;	
	
	var formIDs, currResource;
	
	describe(type+' resource', function () {
		it('should provide '+ type +' resource', function (done) {
			api.loadType(type).then(help.inspect(done), done)
		})
		
		it('should match '+type+" definitions", function (done) {
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
			var skip = []; //skip = ['user', 'userMemberships', 'userAccounts', 'brand', 'brandMembers', 'brandAccounts'];
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
		
	

	describe.only('/user/collection', function () {	
		it('should register a user', function (done) {
			request.post('/user/collection')
				.send({
					email: "user"+Date.now()+"@email.org", 
					name: "User One", 
					password: "pass2"
				})
				.expect(200)
				.end(help.inspect(done));
		});
		
		it('should not register a user if email is missing', function (done) {
			request.post('/user/collection')
				.send({
					name: "Another User", 
					password: "pass2"
				})
				.expect(400)
				.end(help.inspect(done));
		});
		
		it('should not register a user if name is missing', function (done) {
			request.post('/user/collection')
				.send({
					email: "user-"+Date.now()+"@email.org", 
					password: "pass2"
				})
				.expect(400)
				.end(help.inspect(done));
		});
		
		it('should not allow shared emails between users', function (done) {
			request.post('/user/collection')
				.send({
					email: "user22@email.org", //already registered in tools/testdata.sql
					name: "User With Non-Unique Email", 
					password: "pass2"
				})
				.expect(500)
				.end(help.inspect(done));
		});
		
		it('should give summary info about the users collection', function (done) {
			request.get('/user/collection')
				.expect(function (res) {
					if (!res || !res.body) return;
					if (typeof res.body.numUsers!='number') console.log(res.body);
				})
				.expect(200)
				.end(help.inspect(done));
		});
	})*/

/*
describe('brand', function () {		
	describe('/brandcollection', function () {
		it('should register a brand', function(done) {
			request.post('/brands')			
				.auth('21','pass2')
				.send({
					name: 'abc-'+ Date.now(), 
					mission: 'to be the first brand', 
					description: "for testing",
					rating_min: 0,  rating_formula: 0
				})
				.expect(200)
				.end(inspect(done));
		});
	})
	
	describe('brandSelf')
	it('should give detailed brand-info to a member', function (done) {
		request.get('/brands/104')
			.auth('21','pass2')
			.expect(function (res) {
				if (!res || !res.body) return;
				if (!Array.isArray(res.body[0].accounts)) return JSON.stringify(res.body);
			})
			.expect(200)
			.end(inspect(done));
	});
	
	it('should give general info about a brand to a non-member', function (done) {
		request.get('/brands/104')
			.expect(function (res) {
				if (!res || !res.body) return;
				if (typeof res.body[0].numMembers!='number') return JSON.stringify(res.body);
			})
			.expect(200)
			.end(inspect(done));
	});
	
	it("should allow an admin to change a brand's mission and description", function (done) {
		request.post('/brands/104')
			.auth('21','pass2')
			.send({
				mission: 'To test change in brand mission' + Date.now(),
				description: 'To test change in brand description' + Date.now()
			})
			.expect(200)
			.end(inspect(done));
	});
})


describe('members', function () {		
	var member={};
	
	it('should allow an admin to add a member', function (done) {
		request.post('/members')			
			.auth('21','pass2')
			.send({
				brand_id: 104,
				user_id: 22,
				role: 'joat', 
				hours: 1,
			})
			.expect(200)
			.expect(function (res) {member=res.body})
			.end(help.inspect(done));
	});
	
	it('should not allow an admin to re-add a current member', function (done) {
		request.post('/members')			
			.auth('21','pass2')
			.send({
				brand_id: 104,
				user_id: 21,
				role: 'joat', 
				hours: 1,
			})
			.expect(409)
			.end(help.inspect(done));
	});
	
	it("should allow an admin to edit a member's role", function (done) {
		request.post('/members/'+ member.member_id)			
			.auth('21','pass2')
			.send({
				role: 'edited-'+ Date.now()
			})			
			.expect(200)
			.end(help.inspect(done));
	});
	
	it("should allow a member to edit his hours", function (done) {
		request.post('/members/'+ member.member_id)			
			.auth('22','pass2')
			.send({
				hours: 10
			})
			.expect(200)
			.end(help.inspect(done));
	});
	
	it('should allow an admin to deactivate a current member', function (done) {
		request.post('/members/'+ member.member_id)			
			.auth('21','pass2')
			.send({
				ended: Math.round(Date.now()/1000)
			})
			.expect(200)
			.end(help.inspect(done));
	});	
	
	it('should not allow an admin to deactivate himself', function (done) {
		request.post('/members/53')			
			.auth('21','pass2')
			.send({
				ended: Math.round(Date.now()/1000)
			})
			.expect(403)
			.end(help.inspect(done));
	});	
})

describe('accounts', function () {
	var account = {};
		
	it('should allow account creation', function (done) {
		request.post('/accounts')
			.auth('21','pass2')
			.send({
				brand_id: 104,
				name: 'Personal Expense'+ Date.now(),
				authcode: 'ftix',
				sign: 1
			})
			.expect(200)
			.expect(function (res) {
				account=res.body; 
				if (!account.account_id) return 'Invalid account_id (null).'; 
			}) 
			.end(help.inspect(done));
	});
	
	it('should allow an admin to change an account name and authcode', function (done) {
		request.post('/accounts/'+ account.account_id)
			.auth('21','pass2')
			.send({
				name: 'edited-'+ Date.now(),
				authcode: 'ft'
			})
			.expect(200)
			.end(help.inspect(done));
	});
	
	
	it("should NOT allow an admin to change an account's unit or sign", function (done) {
		request.post('/accounts/'+ account.account_id)
			.auth('21','pass2')
			.send({
				unit: 'ft',
				sign: -1
			})
			.expect(403)
			.end(help.inspect(done));
	});
	
	it('should allow an account holder to view account details with limited holder info', function (done) {
		request.get('/accounts/92')
			.auth('21','pass2')
			.expect(200)
			.end(help.inspect(done));
	});
	
	it('should allow an admin to view account details with more detailed holder info', function (done) {
		request.get('/accounts/92')
			.auth('21','pass2')
			.expect(200)
			.expect(function (res) {
				if (!res || !res.body || !Array.isArray(res.body) || !res.body.length) return 'Invalid response body. '+ JSON.stringify(res.body);
				
				var info = res.body[0];
				if (!info.unit || !info.holders || !info.holders.length || !info.holders[0].holder_id
				) return JSON.stringify(res.body); 
			})
			.end(help.inspect(done));
	});
	
	it("should allow non-holders to view an account's balance and unit, but not other details", function (done) {
		request.get('/accounts/92')
			.expect(200)
			.expect(function (res) {
				if (!res || !res.body || !Array.isArray(res.body) || !res.body.length) return 'Invalid response body. '+ JSON.stringify(res.body);
				
				var info = res.body[0]; //should not show account holders
				if (!info.unit || info.holders) return JSON.stringify(res.body);				
			})
			.end(help.inspect(done));
	});
})

describe('holders', function () {		
	var holder;
	
	it('should assign account holder', function (done) {
		request.post('/holders')
			.auth('21','pass2')
			.send({
				account_id: 97,
				user_id: 23,
				authcode: 'ftix'
			})
			.expect(200)
			.expect(function (res) {holder=res.body})
			.end(help.inspect(done));
	});
	
	it("should allow an account holder to change the assigned alias", function (done) {
		request.post('/holders/'+ holder.holder_id)
			.auth('23','pass2')
			.send({
				alias: 'alias-' + Date.now()
			})
			.expect(200)
			.end(help.inspect(done));
	});
	
	it("should NOT allow a non-account holder to change the assigned alias", function (done) {
		request.post('/holders/'+ holder.holder_id)
			.auth('21','pass2')
			.send({
				alias: 'admin-' + Date.now()
			})
			.expect(403)
			.end(help.inspect(done));
	});
	
	it("should NOT allow edits to created value", function (done) {
		request.post('/holders/'+ holder.holder_id)
			.auth('21','pass2')
			.send({
				created: Math.round(Date.now()/1000)
			})
			.expect(403)
			.end(help.inspect(done));
	});
	
	it("should allow an account holders to view holding details including limkey", function (done) {
		request.get('/holders/'+ holder.holder_id)
			.auth('23','pass2')
			.send({
				created: Math.round(Date.now()/1000)
			})
			.expect(200)
			.expect(function (res) {
				if (!res || !res.body || !Array.isArray(res.body) || !res.body.length) return 'Invalid response body. '+ JSON.stringify(res.body);
				
				var info = res.body[0];
				if (!info.limkey) return JSON.stringify(res.body);
			})
			.end(help.inspect(done));
	});
	
	it("should allow a non-holder admin to view holding details except limkey", function (done) {
		request.get('/holders/'+ holder.holder_id)
			.auth('21','pass2')
			.expect(200)
			.expect(function (res) {
				if (!res || !res.body || !Array.isArray(res.body) || !res.body.length) return 'Invalid response body. '+ JSON.stringify(res.body);
				
				var info = res.body[0];
				if (info.limkey) return JSON.stringify(res.body);
			})
			.end(help.inspect(done));
	});
	
	it("should NOT allow a non-holder, non-admin to view any details", function (done) {
		request.get('/holders/42')
			.auth('22','pass2')
			.expect(200)
			.expect(function (res) {
				if (!res.body) return 'Invalid response body. '+ JSON.stringify(res.body);
			})
			.end(help.inspect(done));
	});
})

describe('records', function () {		
	it('should allow budget creation', function (done) {
		request.post('/records')
			.auth('21','pass2')
			.send({
				from_acct: 92,
				to_acct: 93,
				amount: 1000,
				comment: 'first budget',
				cart_id: 0
			})
			.expect(200)
			.end(help.inspect(done));
	});
	
	it("should NOT allow budget creation if an account's authcode do not have a 'c' character", function (done) {
		request.post('/records')
			.auth('21','pass2')
			.send({
				from_acct: 92,
				to_acct: 94,
				amount: 1000,
				comment: 'first budget',
				cart_id: 0
			})
			.expect(403)
			.end(help.inspect(done));
	});
	
	it('should allow budget assignment', function (done) {
		request.post('/records')
			.auth('21','pass2')
			.send({
				from_acct: 93,
				to_acct: 94,
				amount: 35.87,
				comment: 'wages',
				cart_id: 0
			})
			.expect(200)
			.end(help.inspect(done));
	});
	
	it("should NOT allow budget assignment from an account that does not have a 'f' authcode", function (done) {
		request.post('/records')
			.auth('21','pass2')
			.send({
				from_acct: 94,
				to_acct: 93,
				amount: 6.66,
				comment: 'returned pay',
				cart_id: 0
			})
			.expect(403)
			.end(help.inspect(done));
	});
	
	it('should allow budget intrause', function (done) {
		request.post('/records')
			.auth('21','pass2')
			.send({
				from_acct: 94,
				to_acct: 92,
				amount: 2.05,
				comment: 'disounted employee purchase',
				cart_id: 0
			})
			.expect(200)
			.end(help.inspect(done));
	});	
	
	it('should NOT allow budget intrause when either from or to-account balance is insufficient', function (done) {
		request.post('/records')
			.auth('21','pass2')
			.send({
				from_acct: 94,
				to_acct: 92,
				amount: 20000000.05,
				comment: 'disounted employee purchase',
				cart_id: 0
			})
			.expect(403)
			.end(help.inspect(done));
	});
	
	it('should allow external budget use', function (done) {
		request.post('/records')
			.auth('22','pass2')
			.send({
				from_acct: 96,
				to_acct: '41-abc',
				amount: 9.37,
				comment: 'first external budget use',
				cart_id: 0
			})
			.expect(200)
			.end(help.inspect(done));
	});
	
	it("should NOT allow external budget use from an account that does not have an 'x' authcode", function (done) {
		request.post('/records')
			.auth('21','pass2')
			.send({
				from_acct: 94,
				to_acct: '44-abc',
				amount: 6.69,
				comment: 'purchase',
				cart_id: 0
			})
			.expect(403)
			.end(help.inspect(done));
	});
		
	it('should NOT allow external budget use if the relay is invalid', function (done) {
		request.post('/records')
			.auth('22','pass2')
			.send({
				from_acct: 96,
				to_acct: '41-fake',
				amount: 9.37,
				comment: 'first external budget use',
				cart_id: 0
			})
			.expect(401)
			.end(help.inspect(done));
	});
})
*/