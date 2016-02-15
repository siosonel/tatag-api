# Contributing


## API Documentation

https://github.com/siosonel/tatag-api/blob/master/CONTRIBUTING.md


## Advisor Apps

There is a crucial need to offer users a varied selection of Advisors, applications that offers real-time advise on whether to accept or reject a payment offer. 

A related challenge here is: What information and level of input data freshness are required for an effective Advisor program?

- Example Advisor Code: [advisor](https://github.com/siosonel/tatag-api/blob/master/advisors/Advisor2.php)
- Expected Input to the advisor.advice() method: see the ['tally' property](http://tatag.cc/api/app/advise?from_brand=1&to_brand=2&example=1)
- Expected Output of the advisor.advice() method: see the [advise property](http://tatag.cc/api/app/advise?from_brand=1&to_brand=2&example=1)
- An Advisor may use information from other sources, other than tatag.cc, in the formulation of its advise.


## Aggregator Apps

The tatag.cc platform is primarily OLTP (transaction) oriented. Aggregator applications should help with the OLAP (warehousing) of transaction data. Aggregators must periodically pull reports from tatag.cc; see the "data" property in [this code example](https://tatag.cc/api/ref/docs.html#dev-budgetlog).

A related challenge here is: How to audit and reconcile aggregated reports from different service providers? How would a block-chain type report chaining help ensure the integrity of aggregated reports?


## User Interface

You can design and create user interfaces similar to the [wallet](https://tatag.cc/wallet) and [team](https://tatag.cc/teams) pages of the default UI. Those UIs render personal, teams, and admin resources, which all require Oauth-type authentication.

There is an example of a [React-driven UI](https://github.com/siosonel/tatag-rx).


## Risks
 
- no one accepts your currency: if that is the case, you would have wasted just a few minutes
- someone who buys a durable good from you (used phone) might sell it for cash later. If so, would you wish you have sold that durable goods for cash instead of through the tatag.cc platform?: you could require a good-faith condition from a buyer to not engage in such practice
- payment spam: deterred via promo code usage limits

