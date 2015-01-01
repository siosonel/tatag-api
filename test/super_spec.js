var assert = require('assert');
var request = require('supertest');

request = request('http://localhost/tatag');

describe('API test', function () {		
	it('set up database', function (done) {
		request.post('/tools/db_init.php?step=upload')
			.expect(200)
			.end(function(err, res){console.log(res.body); return done(err);})
	});
	
	it('register a user', function (done) {
		request.post('/users')
			.send({email: "user22@email.org", name: "User One", password: "pass2"})
			.expect(200)
			.end(function(err, res){console.log(res.body); return done(err);})
	});
})
	
