var assert = require('assert');
var request = require('supertest');
var hasDB = 0;

request = request('http://localhost/tatag');

describe('API test', function () {	
	before(function (done) {
		if (hasDB) done();
		else request.post('/tools/db_init.php?step=upload&data=testdata.sql')				
				.expect(logBody)
				.expect(200, done)
	});	
	
	it('should register a user', function (done) {
		request.post('/users')
			.send({
				email: "user"+Date.now()+"@email.org", 
				name: "User One", 
				password: "pass2"
			})
			.expect(logBody)
			.expect(200, done)
	});
	
	it('should register a brand', function(done) {
		request.post('/brands')			
			.auth('21','pass2')
			.send({
				name: 'abc'+ Date.now(), 
				mission: 'to be the first brand', 
				description: "for testing",
				rating_min: 0,  rating_formula: 0
			})
			.expect(logBody)
			.expect(200, done)
	})
	
	it('should allow account creation', function (done) {
		request.post('/accounts')
			.auth('21','pass2')
			.send({
				brand_id: 104,
				name: 'Personal Expense'+ Date.now(),
				authcode: 'ftix',
				sign: 1
			})
			.expect(logBody)
			.expect(200, done)
	});
	
	it('should assign account holder', function (done) {
		request.post('/holders')
			.auth('21','pass2')
			.send({
				account_id: 97,
				user_id: 21,
				authcode: 'ftix'
			})
			.expect(logBody)
			.expect(200, done)
	});
	
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
			.expect(logBody)
			.expect(200, done)
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
			.expect(logBody)
			.expect(200, done)
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
			.expect(logBody)
			.expect(200, done)
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
			.expect(logBody)
			.expect(200, done)
	});
	
	it('should give detailed info to a logged-in user', function (done) {
		request.get('/users/21')
			.auth('21','pass2')
			.expect(logBody)
			.expect(200, done)
	});
})

function logBody(res) {
	if (res && (typeof res.body=='string' || (res.body.status && res.body.status=='error'))) console.log(res.body); 
}
