-- To-Do:
-- brands: add columns for url, wanted skills, icon, [image]
-- users: add columns for skills, profile privacy, public or private rankings, [profile image], gplus, fb, token,
-- holders: add contact info
-- products: add this table (no carts)


-- MySQL dump 10.13  Distrib 5.5.28, for Win64 (x86)
--
-- Host: localhost    Database: nplite
-- ------------------------------------------------------
-- Server version	5.5.28

--
-- Table structure for table `accounts`
--

DROP TABLE IF EXISTS `accounts`;

CREATE TABLE `accounts` (
  `account_id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `authcode` varchar(12) DEFAULT NULL,
  `unit` varchar(24) DEFAULT NULL,
  `balance` decimal(9,2) DEFAULT '0.00',
  `sign` tinyint(4) DEFAULT NULL,
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`updated` timestamp NULL DEFAULT NULL,
  `ended` timestamp NULL DEFAULT NULL,
	`throttle_id` int(11) DEFAULT '0',
  PRIMARY KEY (`account_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `brands`;

CREATE TABLE `brands` (
  `brand_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `mission` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `rating_min` float DEFAULT NULL,
  `rating_formula` varchar(255) DEFAULT NULL,
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`updated` timestamp NULL DEFAULT NULL,
  `ended` timestamp NULL DEFAULT NULL,
	`url` varchar(255) DEFAULT NULL,
	`advisor` varchar(255) DEFAULT NULL,
  `country_code` varchar(8) DEFAULT NULL,
  `area_code` varchar(8) DEFAULT NULL,
  `type_system` varchar(16) DEFAULT NULL,
  `type_id` int(11) DEFAULT NULL,
  `subtype_id` varchar(8) DEFAULT NULL,
	`logo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`brand_id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `consumers`;

CREATE TABLE `consumers` (
  `consumer_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL,
  `secret` varchar(80) DEFAULT NULL,
  `redirect_url` varchar(45) DEFAULT NULL,
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`updated` timestamp NULL DEFAULT NULL,
  `ended` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`consumer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Machine consumers of the API';



DROP TABLE IF EXISTS `holders`;

CREATE TABLE `holders` (
  `holder_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `authcode` varchar(12) DEFAULT NULL,
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`updated` timestamp NULL DEFAULT NULL,
  `ended` timestamp NULL DEFAULT NULL,
  `limkey` varchar(16) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`holder_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

--
-- Table structure for table `members`
--

DROP TABLE IF EXISTS `members`;

CREATE TABLE `members` (
  `member_id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `role` varchar(120) DEFAULT NULL,
  `hours` varchar(45) DEFAULT NULL,
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`updated` timestamp NULL DEFAULT NULL,
  `ended` timestamp NULL DEFAULT NULL,
	`joined` timestamp NULL DEFAULT NULL,
  `revoked` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;




DROP TABLE IF EXISTS `promos`;

CREATE TABLE `promos` (
  `promo_id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_id` int(11) DEFAULT '0',
  `name`  varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `imageURL` varchar(255) DEFAULT NULL,
  `infoURL` varchar(255) DEFAULT NULL,
  `amount` decimal(9,2) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `updated` timestamp NULL DEFAULT NULL,
  `ended` timestamp NULL DEFAULT NULL,
  `expires` timestamp NULL DEFAULT NULL,
  `relay_id` int(11) DEFAULT '0',
  `keyword` varchar(16) DEFAULT 'ad',
  PRIMARY KEY (`promo_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;




DROP TABLE IF EXISTS `ratings`;

CREATE TABLE `ratings` (
  `rating_id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `score` smallint(6) DEFAULT NULL,
  `reason` varchar(150) DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `updated` timestamp NULL DEFAULT NULL,
  `ended` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`rating_id`)
) ENGINE=MEMORY AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



--
-- Table structure for table `records`
--

DROP TABLE IF EXISTS `records`;

CREATE TABLE `records` (
  `record_id` int(11) NOT NULL AUTO_INCREMENT,
	`txntype` varchar(5) DEFAULT NULL,
  `from_acct` int(11) DEFAULT NULL,
  `from_user` int(11) DEFAULT NULL,
  `to_acct` int(11) DEFAULT NULL,
  `to_user` int(11) DEFAULT NULL,
  `amount` decimal(9,2) DEFAULT NULL,
  `note` varchar(120) DEFAULT NULL,
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`updated` timestamp NULL DEFAULT NULL,
  `ref_id` int(11) DEFAULT NULL,
  `status` tinyint(3) DEFAULT '0',
	`throttle_id` int(11) DEFAULT '0',
  `relay_id` int(11) DEFAULT '0',
  `promo_id` int(11) DEFAULT '0',
  `readkey` varchar(12) DEFAULT '0',
  PRIMARY KEY (`record_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;




DROP TABLE IF EXISTS `relays`;

CREATE TABLE `relays` (
  `relay_id` int(11) NOT NULL AUTO_INCREMENT,
  `holder_id` int(11) DEFAULT NULL,
  `secret` varchar(45) DEFAULT NULL,
  `amount_min` decimal(9,2) DEFAULT '0.00',
  `amount_max` decimal(9,2) DEFAULT '999999.99',
  `redirect` varchar(255) DEFAULT NULL,
  `tag` varchar(255) DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `updated` timestamp NULL DEFAULT NULL,
  `ended` timestamp NULL DEFAULT NULL,
  `txntype` varchar(5) DEFAULT 'pn',
  `by_all_limit` int(11) DEFAULT '25',
  `by_brand_limit` int(11) DEFAULT '5',
  `by_user_limit` int(11) DEFAULT '2',
  `by_user_wait` int(11) DEFAULT '48',
  PRIMARY KEY (`relay_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



--
-- Table structure for table `reversals`
--

DROP TABLE IF EXISTS `reversals`;

CREATE TABLE `reversals` (
  `orig_record_id` int(11) DEFAULT NULL,
  `rev_record_id` int(11) DEFAULT 0,
  `adjusted_amt` decimal(7,2) DEFAULT '0.00',
  `note` varchar(160) DEFAULT NULL,
	`txntype` varchar(2) DEFAULT NULL,
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`updated` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`orig_record_id`,`rev_record_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `tallies`;

CREATE TABLE `tallies` (
  `tally_id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_id` int(11) DEFAULT NULL,
  `p_start_bal` decimal(7,2) DEFAULT NULL,
  `n_start_bal` decimal(7,2) DEFAULT NULL,
  `issued` decimal(7,2) DEFAULT NULL,
  `intrause` decimal(7,2) DEFAULT NULL,
  `inflow` decimal(7,2) DEFAULT NULL,
  `outflow` decimal(7,2) DEFAULT NULL,
  `num_members` int(11) DEFAULT NULL,
  `member_hours` float DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `updated` timestamp NULL DEFAULT NULL,
  `week` tinyint(4) DEFAULT '0',
  `year` smallint(6) DEFAULT '2015',
  PRIMARY KEY (`tally_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;



--
-- Table structure for table `throttles`
--

DROP TABLE IF EXISTS `throttles`;

CREATE TABLE `throttles` (
  `throttle_id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_id` int(11) DEFAULT '0',
  `period` int(11) DEFAULT '0',
  `by_all` float DEFAULT '0',
  `by_brand` float DEFAULT '0',
  `by_user` float DEFAULT '0',
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`updated` timestamp NULL DEFAULT NULL,
  `ended` timestamp NULL DEFAULT NULL,
	`productURL` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`throttle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `tokens`
--

DROP TABLE IF EXISTS `tokens`;

CREATE TABLE `tokens` (
  `token_id` int(11) NOT NULL AUTO_INCREMENT,
  `consumer_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `otk` varchar(45) DEFAULT '0',
	`token_val` varchar(45) DEFAULT '0',
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`updated` timestamp NULL DEFAULT NULL,
	`login_provider` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`token_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Used by users to authorize API consumers ';



--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(120) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `password` varchar(80) DEFAULT NULL,
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NULL DEFAULT NULL,
  `ended` timestamp NULL DEFAULT NULL,
  `login_provider` varchar(16) DEFAULT NULL,
  `fb_id` varchar(24) DEFAULT '0',
  `gp_id` varchar(24) DEFAULT '0',
  `tw_id` varchar(24) DEFAULT '0',
  `phone` int(24) unsigned DEFAULT NULL,
  `profileImg` varchar(255) DEFAULT NULL,
  `bannerImg` varchar(255) DEFAULT NULL,
  `wallet` int(11) DEFAULT '2',
	`ver_code` varchar(12) DEFAULT '0',
	`ver_expires` int(11) DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email_UNIQUE` (`email`),
  UNIQUE KEY `profileImg_UNIQUE` (`profileImg`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;


--
-- Dumping routines for database 
--


DELIMITER ;;

DROP PROCEDURE IF EXISTS `accountInfo`;;

CREATE PROCEDURE `accountInfo`(IN acctID INT)
BEGIN

SELECT account_id, brand_id, name, authcode, unit, sign,
	balance+sign*(COALESCE(t.amount,0) - COALESCE(f.amount,0)) AS balance
FROM accounts
LEFT JOIN (
	SELECT from_acct, SUM(amount) AS amount 
	FROM records
	WHERE from_acct=acctID AND status BETWEEN 0 AND 6 AND amount>0
) f ON from_acct=account_id
LEFT JOIN (
	SELECT to_acct, SUM(amount) AS amount 
	FROM records
	WHERE to_acct=acctID AND status BETWEEN 0 AND 6 AND amount<0
) t ON to_acct=account_id
WHERE account_id=acctID;

END;;


DROP PROCEDURE IF EXISTS `acctAuthBals`;;

CREATE PROCEDURE `acctAuthBals`(
	IN fromAcct INT,
	IN toAcct INT
)
BEGIN

SELECT account_id, balance+sign*(COALESCE(t.amount,0) - COALESCE(f.amount,0)) AS balance, sign, brand_id, authcode
FROM accounts
LEFT JOIN (
	SELECT from_acct, SUM(amount) AS amount 
	FROM records
	WHERE from_acct IN (fromAcct, toAcct) AND status BETWEEN 0 AND 6 AND amount>0
	GROUP BY from_acct
) f ON from_acct=account_id
LEFT JOIN (
	SELECT to_acct, SUM(amount) AS amount 
	FROM records
	WHERE to_acct IN (fromAcct, toAcct) AND status BETWEEN 0 AND 6 AND amount<0
	GROUP BY to_acct
) t ON to_acct=account_id
WHERE account_id IN (fromAcct, toAcct);

END;;



DROP PROCEDURE IF EXISTS `holderAccts`;;

CREATE PROCEDURE `holderAccts`(IN userID INT)
BEGIN

SELECT a.account_id, brand_id, name, 
	a.authcode as acct_auth, h.authcode as holder_auth, 
	unit, sign, limkey,
	balance+sign*(COALESCE(t.amount,0) - COALESCE(f.amount,0)) AS balance
FROM accounts a
LEFT JOIN (
	SELECT from_acct, SUM(amount) AS amount 
	FROM records
	WHERE status BETWEEN 0 AND 6 AND amount>0
	GROUP BY from_acct
) f ON from_acct=account_id
LEFT JOIN (
	SELECT to_acct, SUM(amount) AS amount 
	FROM records 
	WHERE status BETWEEN 0 AND 6 AND amount<0
	GROUP BY to_acct
) t ON to_acct=account_id
JOIN holders h ON (h.account_id=a.account_id)
WHERE user_id=userID;

END;;



DROP PROCEDURE IF EXISTS `postBal`;;

CREATE PROCEDURE `postBal`(
	IN fromAcct INT,
	IN fromUser INT,
	IN toAcct INT,
	IN toUser INT,
	IN amt DECIMAL(7,2),
	IN note VARCHAR(255),
	IN cartID INT,
	OUT mssg VARCHAR(255)
)
BEGIN
	INSERT INTO records (from_acct,from_user,to_acct,to_user,amount,`note`,ref_id,created,status)
	VALUES (fromAcct, fromUser, toAcct, toUser, amt, note, cartID, NOW(), 0);

	SELECT last_insert_id() into @entryID;

	SELECT COUNT(*) INTO @num
	FROM accounts
	LEFT JOIN (
		SELECT from_acct, SUM(amount) AS amount 
		FROM records
		WHERE from_acct IN (fromAcct, toAcct) AND record_id <= @entryID AND status BETWEEN 0 AND 6 AND amount>0
		GROUP BY from_acct
	) f ON from_acct=account_id
	LEFT JOIN (
		SELECT to_acct, SUM(amount) AS amount
		FROM records
		WHERE to_acct IN (fromAcct, toAcct) AND record_id <= @entryID AND status BETWEEN 0 AND 6 AND amount<0
		GROUP BY to_acct
	) t ON to_acct=account_id
	WHERE account_id IN (fromAcct, toAcct)
		AND balance+sign*(COALESCE(t.amount,0) - COALESCE(f.amount,0)) > 0;

	IF @num IS NULL OR @num < 2 THEN 
		DELETE FROM records WHERE record_id=@entryID;
		SET mssg="The transaction amount would cause a negative budget.";
	ELSE 
		SET mssg=@entryID;
	END IF;

END;;



DROP PROCEDURE IF EXISTS `accountRecords`;;

CREATE PROCEDURE `accountRecords`(
	IN acctID INT,
	IN maxRecordID INT,
	IN itemsLimit INT,
	IN minUpdated INT,
	IN cutoffID INT
)
BEGIN

SELECT record_id, txntype, 'to' AS direction, r.throttle_id,
	to_acct AS other_acct,
	a.brand_id, b.name AS brand_name, amount, 
	r.created, `status`, note, UNIX_TIMESTAMP(r.updated) as updated
FROM records r 
JOIN accounts a ON a.account_id = r.to_acct
JOIN brands b ON a.brand_id = b.brand_id
WHERE from_acct=acctID AND record_id < maxRecordID AND record_id > cutoffID
	AND (r.updated IS NULL OR UNIX_TIMESTAMP(r.updated) > minUpdated)
UNION ALL 

SELECT record_id, txntype, 'from' AS direction, r.throttle_id,
	from_acct AS other_acct,
	a.brand_id, b.name AS brand_name, amount, 
	r.created, `status`, note, UNIX_TIMESTAMP(r.updated) as updated
FROM records r 
JOIN accounts a ON a.account_id = r.from_acct
JOIN brands b ON a.brand_id = b.brand_id
WHERE to_acct=acctID AND record_id < maxRecordID AND record_id > cutoffID
	AND (r.updated IS NULL OR UNIX_TIMESTAMP(r.updated) > minUpdated)
ORDER BY record_id DESC 
LIMIT itemsLimit;

END;;



DROP PROCEDURE IF EXISTS `holderCheck`;;

CREATE PROCEDURE `holderCheck`(
	IN holderID INT
)
BEGIN

SELECT holder_id, user_id, limkey, h.authcode AS holder_auth, 
	a.brand_id, a.account_id, a.authcode AS acct_auth, sign, a.throttle_id,
	balance+sign*(COALESCE(t.amount,0) - COALESCE(f.amount,0)) AS balance, unit	
FROM (
	SELECT * FROM holders WHERE holder_id = holderID AND ended IS NULL
) h 
JOIN accounts a ON a.account_id=h.account_id
LEFT JOIN (
	SELECT from_acct, SUM(amount) AS amount 
	FROM records
	WHERE status BETWEEN 0 AND 6 AND amount>0
	GROUP BY from_acct
) f ON from_acct = h.account_id
LEFT JOIN (
	SELECT to_acct, SUM(amount) AS amount 
	FROM records
	WHERE status BETWEEN 0 AND 6 AND amount<0
	GROUP BY to_acct
) t ON to_acct = h.account_id;

END;;



DROP PROCEDURE IF EXISTS `userAccounts`;;

CREATE PROCEDURE `userAccounts` (
	IN userID INT
)
BEGIN

SELECT 
	h.holder_id AS id, 
	h.alias,
	h.limkey, 
	h.authcode AS authcode,

	h.user_id AS user_id, 

	a.account_id AS account_id, 
	a.name AS account_name, 
	a.sign AS account_sign, 
	a.balance+sign*(COALESCE(t.amount,0) - COALESCE(f.amount,0)) AS account_balance, 
	a.unit AS account_unit,
	a.authcode AS account_authcode, 

	a.brand_id AS account_brand_id,
	b.name AS account_brand_name, 
	b.logo AS account_brand_logo,

	a.throttle_id AS account_throttle_id
FROM accounts a
JOIN brands b ON a.brand_id = b.brand_id
JOIN holders h ON a.account_id=h.account_id AND h.user_id=userID
LEFT JOIN (
	SELECT from_acct, SUM(amount) AS amount 
	FROM records
	WHERE status BETWEEN 0 AND 6 AND amount>0
	GROUP BY from_acct
) f ON from_acct=a.account_id
LEFT JOIN (
	SELECT to_acct, SUM(amount) AS amount 
	FROM records
	WHERE status BETWEEN 0 AND 6 AND amount<0
	GROUP BY to_acct
) t ON to_acct=a.account_id
GROUP BY a.account_id;

END;;




DROP PROCEDURE IF EXISTS `approveRecord`;;

CREATE PROCEDURE `approveRecord`(
	IN $record_id INT
)
BEGIN

SELECT from_acct, to_acct, amount INTO @f, @t, @amount
FROM records
WHERE record_id=$record_id AND status BETWEEN 0 AND 6;

IF @f!=0 AND @t!=0 THEN BEGIN
	START TRANSACTION;
	UPDATE records SET status=7, updated=NOW() WHERE record_id=$record_id;
	UPDATE accounts SET balance = balance-sign*@amount WHERE account_id=@f;
	UPDATE accounts SET balance = balance+sign*@amount WHERE account_id=@t;
	COMMIT;
	END; 
END IF;
END;;




DROP PROCEDURE IF EXISTS `brandAccountsAsc`;;

CREATE PROCEDURE `brandAccountsAsc` (
	IN $brandID INT,
	IN $maxAccountID INT,
	IN $itemsLimit INT
)
BEGIN

SELECT accounts.account_id, name, 
	sign*(balance+sign*(COALESCE(t.amount,0) - COALESCE(f.amount,0))) AS balance,
	unit, authcode, created, throttle_id			
FROM accounts
LEFT JOIN (
	SELECT from_acct, SUM(amount) AS amount 
	FROM records 
	JOIN accounts ON from_acct=accounts.account_id 
	WHERE brand_id=$brandID
		AND records.status BETWEEN 0 AND 6 
		AND amount > 0
	GROUP BY from_acct
) f ON from_acct=account_id
LEFT JOIN (
	SELECT to_acct, SUM(amount) AS amount 
	FROM records
	JOIN accounts ON to_acct=accounts.account_id
	WHERE brand_id=$brandID 
		AND records.status BETWEEN 0 AND 6 
		AND amount < 0
	GROUP BY to_acct
) t ON to_acct=account_id
WHERE brand_id=$brandID AND account_id > $maxAccountID
GROUP BY account_id
ORDER BY account_id ASC
LIMIT $itemsLimit;

END;;



DROP PROCEDURE IF EXISTS `tallyAdded`;;

CREATE PROCEDURE `tallyAdded` (
	IN $brandID INT,
	IN $startDate TIMESTAMP,
	IN $endDate TIMESTAMP,
	OUT $added DECIMAL(9,2)
)
BEGIN

SELECT COALESCE(SUM(amount),0) INTO $added
FROM records r
JOIN accounts f ON r.from_acct=f.account_id AND f.brand_id IN ($brandID) AND f.sign=-1
JOIN accounts t ON r.to_acct=t.account_id AND t.brand_id IN ($brandID) AND t.sign=1
WHERE f.brand_id = $brandID
	AND txntype='np'
	AND status>-1
	AND r.created BETWEEN $startDate AND $endDate;

END;;




DROP PROCEDURE IF EXISTS `tallyIntrause`;;

CREATE PROCEDURE `tallyIntrause` (
	IN $brandID INT,
	IN $startDate TIMESTAMP,
	IN $endDate TIMESTAMP,
	OUT $intrause DECIMAL(9,2)
)

BEGIN

SELECT COALESCE(SUM(amount),0) INTO $intrause
FROM records r
JOIN accounts f ON r.from_acct=f.account_id AND f.brand_id IN ($brandID)
JOIN accounts t ON r.to_acct=t.account_id AND t.brand_id IN ($brandID)
WHERE t.brand_id = $brandID
	AND txntype='pn'
	AND status>-1
	AND r.created BETWEEN $startDate AND $endDate;

END;;



DROP PROCEDURE IF EXISTS `tallyInflow`;;

CREATE PROCEDURE `tallyInflow` (
	IN $brandID INT,
	IN $startDate TIMESTAMP,
	IN $endDate TIMESTAMP,
	OUT $inflow DECIMAL(9,2)
)
BEGIN

SELECT COALESCE(SUM(amount),0) INTO $inflow
FROM records r
JOIN accounts f ON r.from_acct=f.account_id AND f.brand_id NOT IN ($brandID)
JOIN accounts t ON r.to_acct=t.account_id AND t.brand_id IN ($brandID)
WHERE t.brand_id = $brandID
	AND txntype='pn'
	AND status>-1
	AND r.created BETWEEN $startDate AND $endDate;

END;;




DROP PROCEDURE IF EXISTS `tallyOutflow`;;

CREATE PROCEDURE `tallyOutflow` (
	IN $brandID INT,
	IN $startDate TIMESTAMP,
	IN $endDate TIMESTAMP,
	OUT $outflow DECIMAL(9,2)
)

BEGIN

SELECT COALESCE(SUM(amount),0) INTO $outflow
FROM records r
JOIN accounts f ON r.from_acct=f.account_id AND f.brand_id IN ($brandID)
JOIN accounts t ON r.to_acct=t.account_id AND t.brand_id NOT IN ($brandID)
WHERE f.brand_id = $brandID
	AND txntype='pn'
	AND status>-1
	AND r.created BETWEEN $startDate AND $endDate;

END;;


DROP PROCEDURE IF EXISTS `budgetTotal`;;

CREATE PROCEDURE `budgetTotal` (
	IN $brandID INT,
	IN $sign INT,
	OUT $budget DECIMAL(9,2)
)
BEGIN

SELECT SUM(balance+sign*(COALESCE(t.amount,0) - COALESCE(f.amount,0))) INTO $budget
FROM accounts
LEFT JOIN (
	SELECT from_acct, SUM(amount) AS amount 
	FROM records r
	JOIN accounts a ON a.account_id=r.from_acct 
		AND a.sign=$sign
		AND a.brand_id=$brandID
		AND status BETWEEN 0 AND 6 AND amount>0
) f ON from_acct=account_id
LEFT JOIN (
	SELECT to_acct, SUM(amount) AS amount 
	FROM records r
	JOIN accounts a ON a.account_id=r.to_acct 
		AND a.sign=$sign
		AND brand_id=$brandID
		AND status BETWEEN 0 AND 6 AND amount<0
) t ON to_acct=account_id	
WHERE brand_id=$brandID AND sign=$sign;

END;;


DROP PROCEDURE IF EXISTS `budgetRevExp`;;

CREATE PROCEDURE `budgetRevExp`(
	IN $brandID INT
)
BEGIN


set @revBudget=0;
set @expBudget=0;
set @inflow=0;

set @startDate='2015-01-01 00:00:00';
set @endDate='2015-12-31 11:59:59';


call budgetTotal($brandID,-1,@revBudget);
call budgetTotal($brandID,1,@expBudget);
call tallyInflow($brandID, @startDate, @endDate, @inflow);

select 
	$brandID AS brand_id, 
	@revBudget AS revBal, 
	@expBudget AS expBal, 
	COALESCE(@inflow,0) AS inflow,
	(SELECT account_id FROM accounts WHERE brand_id=$brandID AND name='Main Revenue') AS revAcct,
	(SELECT account_id FROM accounts WHERE brand_id=$brandID AND name='Main Expense') AS expAcct,
	(SELECT WEEKOFYEAR(MAX(updated)) FROM records WHERE brand_id=$brandID AND txntype='np') AS lastWeekAdded
;

END;;


DROP PROCEDURE IF EXISTS `tally`;;

CREATE PROCEDURE `tally`(
	IN $brandID INT,
	IN $startDate TIMESTAMP,
	IN $endDate TIMESTAMP
)
BEGIN

call tallyAdded($brandID, $startDate, $endDate, @added);
call tallyIntrause($brandID, $startDate, $endDate, @intrause);
call tallyInflow($brandID, $startDate, $endDate, @inflow);
call tallyOutflow($brandID, $startDate, $endDate, @outflow);

call budgetTotal($brandID,-1,@rev);
call budgetTotal($brandID,1,@exp);


SELECT COUNT(*), COALESCE(SUM(hours),0) 
INTO @numMembers, @totalMemberHours
FROM members 
WHERE brand_id=$brandID;

select 
	@added AS added, 
	@intrause AS intrause, 
	@inflow AS inflow, 
	@outflow AS outflow,
	@rev AS revBudget,
	@exp AS expBudget,
	@numMembers AS numMembers,
	@totalMemberHours AS totalMemberHours;

END;;

delimiter ;;

DROP PROCEDURE IF EXISTS `detect_imbalance`;; 

CREATE PROCEDURE `detect_imbalance`(
)

BEGIN
select * from (
	select account_id, sign, balance, 
		(COALESCE(addedf,0) + COALESCE(addedt,0) - COALESCE(outflow,0) - COALESCE(inflow,0)) as actual,
		COALESCE(addedf,0), COALESCE(addedt,0),
		COALESCE(outflow,0), COALESCE(inflow,0)	
	from accounts a
	left join (
		select from_acct, SUM(amount) as outflow
		from records r
		where status=7 and txntype='pn' and to_acct IS NOT NULL and from_acct IS NOT NULL
		group by from_acct
	) f on f.from_acct = a.account_id
	left join (
		select to_acct, SUM(amount) as inflow
		from records r
		where status=7 and txntype='pn' and to_acct IS NOT NULL and from_acct IS NOT NULL
		group by to_acct
	) t on t.to_acct = a.account_id
	left join (
		select from_acct, SUM(amount) as addedf
		from records r
		where status=7 and txntype='np' and to_acct IS NOT NULL and from_acct IS NOT NULL
		group by from_acct
	) addf on addf.from_acct = a.account_id
	left join (
		select to_acct, SUM(amount) as addedt
		from records r
		where status=7 and txntype='np' and to_acct IS NOT NULL and from_acct IS NOT NULL
		group by to_acct
	) addt on addt.to_acct = a.account_id
) byrec
where balance<0 OR byrec.balance != actual
;

END;;



