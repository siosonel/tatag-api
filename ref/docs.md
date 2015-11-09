
# Introduction

Welcome to the tatag.cc API.


# Authentication

A bit of a dance using Oauth.


# Personal

```javascript

phlat.init({
	listeners: {
		personal: Personal
	}
});

```

These are resources that belong to the current user and are meant for private viewing. 

## me

```javascript

phlat.request('personal','me');

```

The current user.

personal-me-table

## memberOf

```javascript

phlat.request('personal','memberOf');

```

The current user's membership information in teams.

personal-memberOf-table

## holdings

```javascript

phlat.request('personal','holdings');

```

The current user's accountholdings.

personal-holdings-table

