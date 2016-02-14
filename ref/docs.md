
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

- add
- edit
- transfer
- approve
- reject
- etc.


## Tolerance

The client must be programmed with tolerance in mind, to accomodate potential variations in labels and methods as seen above. Many of these concerns are automatically handled by generic API clients, such as for the Phlat profile. We recommend using generic media-type or profile clients to minimize the effort in getting started and to increase the long-term stability of the consumer application.

The API consumer should gracefully handle the following: 

- Changes to a resource's URL (not an issue if you use concept-based navigation)
- Embedded links, i.e., a link value is the resource object instead of just that resource's string URL
- Unknown properties, which the client should ignore
- "Missing" methods
- Changes to the API's sitemap or relation layout (not an issue if you use concept-based navigation)




# Versioning

(... TBD ...)




# Authentication

If your application is not acting on behalf of a user, then simply use HTTP Basic Auth.

If your application is acting on behalf of a user, such as displaying personal, team, and admin resources, then you would need to do the Oauth dance of passing around token value and secret in the URL. I'll document this dance based on demand.




# Public Resources

These resources do not require a user to be logged-in.


===public===



# Personal Resources

These are resources that belong to the current user and are meant for private viewing. These resources are inteded to be used for wallet type applications, and in order for such applications to act on behalf of a user, oauth-type authentication is required.


===my===


# Team Resources

All members of a team share access to these resources. The accessible resources are based on the membership of the currently logged-in user.


===team===




# Admin Resources

The admins of a brand share access to these resources. The accessible resources are based on the admin status of the currently logged-in user.


===admin===




# Developer Resources

These are application-related resources that belongs to a developer. The consumer id and secret are required fro authentication.


===dev===






# System Resources

These are system administration related resources.


===sys===


