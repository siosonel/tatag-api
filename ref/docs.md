
# Introduction

Welcome to tatag.cc's API.


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

> Status 200

```
{{examples/personal-me.json}}
```


The current user.

_table:examples/personal-me.json



## memberOf


```javascript

phlat.request('personal','memberOf');

```

> Status 200

```
{{examples/personal-memberOf.json}}
```


The current user's paged collection of membership information.

_table:examples/personal-memberOf.json




## memberInfo


```javascript

phlat.request('personal','memberInfo');

```

> Status 200

```
{{examples/personal-memberInfo.json}}
```


The current user's membership information, presented as an array
for each memberOf collection page.

_table:examples/personal-memberInfo.json



## holdings

```javascript

phlat.request('personal','holdings');

```

> Status 200

```
{{examples/personal-holdings.json}}
```


The current user's paged collection of accountholdings.

_table:examples/personal-holdings.json




## holdingInfo

```javascript

phlat.request('personal','holdingInfo');

```

> Status 200

```
{{examples/personal-holdingInfo.json}}
```


The current user's accountholdings, presented as an array.

_table:examples/personal-holdingInfo.json

