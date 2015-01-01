var assert = require('assert');
var request = require('supertest');

request = request('http://localhost/tatag');


describe('API test', function () {		
	var inspect = inspector();
	
	it('set up database', function (done) {
		request.post('/tools/db_init.php?step=upload')
			.expect(200, done)
	});
	
	it('register a user', function (done) {
		request.post('/users')
			.send({email: "user2@email.org", name: "User One", password: "pass2"})
			.expect(200)
			.end(inspect('body', done))
	});
})


function inspector() {	
	var fxns = {
		done: function () {},
		body: function (err, res) {
			if (res.body) console.log(res.body); 
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
