
# Introduction

Welcome to tatag.cc's API.




# Resource Orientation


## Navigation

> Examples of the supported navigational approaches

```
api.loadConcept('my-memberships') 
.then(okHandler, errHandler) 
```

To get a target resource, the client looks in the root resource to fill-in applicable URL-templates or to follow directions along a given sequence of link relations. This approach is tolerant of changes in the URL structure and media type, and may be likened to always asking for directions to a certain warehous item - you will be able to find something even if there are changes to the aisle-number or the corridor layout. The API consumer will also be more likely to be able to navigate different warehouses.



## Methods

Resources may be linked to forms for creating, editing, or otherwise affecting its state. API clients should not assume that a particular resource will always support the same set of methods. Instead, a client should check for the existence of any of the following form links before initiating the corresponding display or action options.

- createForm
- editForm
- budgetAdd
- budgetTransfer
- budgetUse
- approve
- reject


## Tolerance

The client must be programmed with tolerance in mind, to accomodate potential variations in labels and methods as seen above. Many of these concerns are automatically handled by generic API clients, such as for the Phlat profile. We recommend using generic media-type or profile clients to minimize the effort in getting started and to increase the long-term stability of the consumer application.

The API consumer should gracefully handle the following: 

- Changes to a resource's URL (not an issue if you use concept-based navigation)
- Embedded links, i.e., a link value is the resource object instead of just that resource's string URL
- Unknown properties, which the client should ignore
- "Missing" methods
- Changes to the API's sitemap or relation layout (not an issue if you use concept-based navigation)




# Versioning

> Deprecated example

```


GET /api-20150101/something

// before merging the deprecated attributes or links
{
	"@id": "/api-20150101/something", 
	"name": "example resource",
	"deprecated": [{
		"date": 20151019, 
		"reason": "old attributes",
		"merge-patch": {
			"attr1": "value 1",
			"attr2": "value 2"
		}
	}]
}

// after the client merges in the deprecated attributes
// and then deleting the "deprecated" array of objects 
{
	"@id": "/api-20150101/something", 
	"name": "example resource",
	"attr1": "value 1",
	"attr2": "value 2"
}

```
 

A type of change that is not expected to be handled gracefully is a deprecated resource attribute or relation name. In order to avoid breaking, clients must be able to process the link relation "deprecated". This link relation points to an array of objects, each representing a deprecation timepoint, an explanation, and a merge-patch object that can be used to extend the current resource with the deprecated attributes and links.




# Authentication

A bit of a dance using Oauth.




# Public Resources

These resources do not require a user to be logged-in.


## public-promos

```

api.loadConcept('public-promos').then('...')

```

A list of promotions that any user could pay for, such as for purchasing products, 
funding nonprofit campaigns, or supporting team projects.


## public-ratings

## public-users

```
api.loadConcept('public-promos').then('...')

```

A paged list of registered users, containing non-sensitive user information.



## public-brands


```
api.loadConcept('public-brands')

```

A paged list of registered brands.



# Personal Resources

These are resources that belong to the current user and are meant for private viewing. 

## my-me

```
api.loadConcept('me').then(...)
```

> Status 200

```
{{examples/wip/my-me.json}}
```


The currently logged-in user.

_table:examples/wip/my-me.json



## my-about

```javascript

// Concept-based
api.loadConcept('my-about');

```

> Status 200

```
{{examples/wip/my-about.json}}
```


Public information about the currently logged-in user.

_table:examples/wip/my-about.json




## my-memberships


```javascript

phlat.loadConcept('my-memberships');

```

> Status 200

```
{{examples/wip/my-memberships.json}}
```


The current user's paged collection of membership information.

_table:examples/wip/my-memberships.json




## my-membership


```javascript

phlat.request('my-membership');

```

> Status 200

```
{{examples/wip/my-membership.json}}
```


The current user's membership information, presented as an array
(a user could belong to multiple one team).

_table:examples/wip/my-membership.json



## my-holdings

```javascript

phlat.loadConcept('my-holdings');

```

> Status 200

```
{{examples/wip/my-holdings.json}}
```


The current user's paged collection of accountholdings.

_table:examples/wip/my-holdings.json




## my-holding

```javascript

phlat.loadConcept('my-holding');

```

> Status 200

```
{{examples/wip/my-holding.json}}
```


The current user's accountholdings, presented as an array.

_table:examples/wip/my-holding.json



## my-account-records

```javascript

api.loadConcept('my-account-records');

```

> Status 200

```
{{examples/wip/my-account-records.json}}
```



The account's transaction records, most recent first.

_table:examples/wip/my-account-records.json



## my-account-record

```javascript

api.loadConcept('my-account-record');

```

> Status 200

```
{{examples/wip/my-account-record.json}}
```


# Team Resources

All members of a team share access to these resources. The accessible resources are based on the membership of the currently logged-in user.


## team

## team-members

## team-member

## team-accounts

## team-account



# Admin Resources

The admins of a brand share access to these resources. The accessible resources are based on the admin status of the currently logged-in user.


## brand

## brand-about

Public information about a team.

## brand-members

## brand-member

## brand-member-holdings

## brand-member-holding

## brand-accounts

## brand-account

## brand-account-holders

## brand-account-holder



# Glossary

## account

## brand

## holder

## holding

## items

## membership

## promo

## rating

## tally

## team

## user

