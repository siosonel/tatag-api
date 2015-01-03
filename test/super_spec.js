var assert = require('assert');
var request = require('supertest');

request = request('http://localhost/tatag');


describe('API test', function () {		
	var inspect = inspector();
	
	it('should set up a database', function (done) {
		request.post('/tools/db_init.php?step=upload')
			.expect(200)
			.end(inspect('body', done)) //, done)
	});
	
	it('should register a user', function (done) {
		request.post('/users')
			.send({
				email: "user24@email.org", 
				name: "User One", 
				password: "pass2"
			})
			.expect(200)
			.end(inspect('body', done))
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
			.expect(200)
			.end(inspect('body', done))
	})
	
	it('should allow account creation', function (done) {
		request.post('/accounts')
			.auth('21','pass2')
			.send({
				brand_id: 104,
				name: 'Personal Expense',
				authcode: 'ftix',
				sign: 1
			})
			.expect(200)
			.end(inspect('body', done))
	});
	
	it('should assign account holder', function (done) {
		request.post('/holders')
			.auth('21','pass2')
			.send({
				account_id: 94,
				user_id: 21,
				authcode: 'ftix'
			})
			.expect(200)
			.end(inspect('body', done))
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
			.expect(200)
			.end(inspect('body', done))
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
			.end(inspect('body', done))
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
			.end(inspect('body', done))
	});
	
	it('should give detailed info to a logged-in user', function (done) {
		request.get('/users/21')
			.auth('21','pass2')
			.expect(200)
			.end(inspect('body', done))
	});
})


function inspector() {	
	var fxns = {
		done: function () {},
		body: function (err, res) {
			if (err) console.log(err);
			if (res && res.body) console.log(res.body); 
			return fxns.done(err);
		}
	};
	
	function main(name, done) {
		if (arguments.length==2 && typeof done=='function') fxns.done = done;
		if (typeof name=='string' && fxns[name]) return fxns[name];
		return fxns['body']; //default
	}
		
	return main;	
}
