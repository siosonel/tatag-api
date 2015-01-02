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
	
	it.only('should login a user', function (done) {
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
