var frisby = require("C:/Users/esioson/AppData/Roaming/npm/node_modules/frisby");

frisby.globalSetup({
	request: { 
		headers: { 'Content-Type': 'application/json' }
	}
});

frisby.create('test of database installation')
	.post('http://localhost/tatag/tools/db_init.php?step=upload')
	.expectStatus(200)
	.inspectBody()
	.after(registerUsers)
.toss()

function registerUsers() {
	frisby.create('test of user1 registration')
		.post(
			'http://localhost/tatag/users', 
			{email: "user2@email.org", name: "User One", password: "pass2"}, 
			{json: true}
		)
		//.expectHeaderContains('Content-Type', 'json')
		.inspectBody()
		.expectStatus(200)
		//.after(registerBrand1)
	.toss();
	
	/*frisby.create('test of user2 registration')
		.post(
			'http://localhost/tatag/users', 
			{email: 'user2@email.org', name: 'User Two', password: 'pass2'} //, {json: true}
		)
		.expectStatus(200)
		.after(registerBrand2)
	.toss();*/
}

function registerBrand1() {
	frisby.create('test of brand1 registration')
		.post(
			'http://localhost/tatag/brands', 
			{}, 
			{json: true}
		)
		.expectStatus(200)
	.toss();
}

function registerBrand2() {	
	frisby.create('test of brand2 registration')
		.post(
			'http://localhost/tatag/brands', 
			{} //,{json: true}
		)
		.expectStatus(200)
	.toss();
}
	
function getBrandInfo() {	
	frisby.create('test of brand info')
		.get('http://localhost/tatag/brands/100')
		.expectStatus(200)
	.toss();
}