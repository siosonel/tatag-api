var assert = require('assert');
var request = require('supertest');
var hasDB = 1;

request = request('http://localhost/tatag');

describe('users', function () {	
	before(initDB);	
	
	it.skip('should register a user', function (done) {
		request.post('/users')
			.send({
				email: "user"+Date.now()+"@email.org", 
				name: "User One", 
				password: "pass2"
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
	
	it.only('should allow a user to change his email', function (done) {
		request.post('/users/21')
			.auth('21','pass2')
			.send({
				email: "user-new-addr@email.org"
			})
			.expect(logBody)
			.expect(200, done)
	});
	
	it('should not register a user if email is missing', function (done) {
		request.post('/users')
			.send({
				name: "Another User", 
				password: "pass2"
			})
			.expect(logBody)
			.expect(400, done)
	});
	
	it('should not register a user if name is missing', function (done) {
		request.post('/users')
			.send({
				email: "user-"+Date.now()+"@email.org", 
				password: "pass2"
			})
			.expect(logBody)
			.expect(400, done)
	});
	
	it('should not allow shared emails between users', function (done) {
		request.post('/users')
			.send({
				email: "user21@email.org", //already registered in tools/testdata.sql
				name: "User With Non-Unique Email", 
				password: "pass2"
			})
			.expect(logBody)
			.expect(500, done)
	});
})

describe('brands', function () {	
	before(initDB);	
	
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
})

describe('members', function () {	
	before(initDB);	
	
	it('should add a member');
})

describe('accounts', function () {	
	before(initDB);	
	
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
})

describe('holders', function () {	
	before(initDB);	
	
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
})

describe('records', function () {	
	before(initDB);	
	
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
})

function initDB(done) {
	if (hasDB) done();
	else {	
		hasDB=1;	
		request.post('/tools/db_init.php?step=upload&data=testdata.sql')				
			.expect(logBody)
			.expect(200, done)
	}
}

function logBody(res) { console.log(res.body); return;
	if (res && (typeof res.body=='string' || (res.body.status && res.body.status=='error'))) console.log(res.body); 
}
