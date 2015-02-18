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
  `created` timestamp NULL DEFAULT NULL,
  `ended` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`account_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `brands`;

CREATE TABLE `brands` (
  `brand_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `mission` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `rating_min` float DEFAULT NULL,
  `rating_formula` varchar(255) DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `ended` timestamp NULL DEFAULT NULL,
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
  PRIMARY KEY (`consumer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Machine consumers of the API';



DROP TABLE IF EXISTS `holders`;

CREATE TABLE `holders` (
  `holder_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `authcode` varchar(12) DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `ended` timestamp NULL DEFAULT NULL,
  `limkey` varchar(12) DEFAULT NULL,
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
  `created` timestamp NULL DEFAULT NULL,
  `ended` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


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
  `created` timestamp NULL DEFAULT NULL,
  `ref_id` int(11) DEFAULT NULL,
  `status` tinyint(3) DEFAULT '0',
  PRIMARY KEY (`record_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


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
  `created` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`orig_record_id`,`rev_record_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



--
-- Table structure for table `tokens`
--

DROP TABLE IF EXISTS `tokens`;

CREATE TABLE `tokens` (
  `token_id` int(11) NOT NULL AUTO_INCREMENT,
  `consumer_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `otk` varchar(45) DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`token_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='Used by users to authorize API consumers '$$



--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(120) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `password` varchar(80) DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `ended` timestamp NULL DEFAULT NULL,
  `profileImg` varchar(255) DEFAULT NULL,
  `bannerImg` varchar(255) DEFAULT NULL,
	`fb_id` int(11) DEFAULT NULL,
  `fb_name` varchar(45) DEFAULT NULL,
  `fb_email` varchar(45) DEFAULT NULL,
  `gp_id` int(11) DEFAULT NULL,
  `gp_name` varchar(45) DEFAULT NULL,
  `gp_email` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


--
-- Dumping routines for database 
--
DROP PROCEDURE IF EXISTS `accountInfo`;


CREATE PROCEDURE `accountInfo`(IN acctID INT)
BEGIN

SELECT account_id, brand_id, name, authcode, unit, sign,
	balance+sign*(COALESCE(t.amount,0) - COALESCE(f.amount,0)) AS balance
FROM accounts
LEFT JOIN (
	SELECT from_acct, SUM(amount) AS amount 
	FROM records
	WHERE from_acct=acctID AND  (status=7 OR (status > -1 AND amount>0))
) f ON from_acct=account_id
LEFT JOIN (
	SELECT to_acct, SUM(amount) AS amount 
	FROM records
	WHERE to_acct=acctID AND (status=7 OR (status > -1 AND amount<0))
) t ON to_acct=account_id
WHERE account_id=acctID;

END;



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
	WHERE from_acct IN (fromAcct, toAcct) AND (status=7 OR (status > -1 AND amount>0))
	GROUP BY from_acct
) f ON from_acct=account_id
LEFT JOIN (
	SELECT to_acct, SUM(amount) AS amount 
	FROM records
	WHERE to_acct IN (fromAcct, toAcct) AND (status=7 OR (status > -1 AND amount<0))
	GROUP BY to_acct
) t ON to_acct=account_id
WHERE account_id IN (fromAcct, toAcct);

END;



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
	WHERE status=7 OR (status > -1 AND amount>0)
	GROUP BY from_acct
) f ON from_acct=account_id
LEFT JOIN (
	SELECT to_acct, SUM(amount) AS amount 
	FROM records 
	WHERE status=7 OR (status > -1 AND amount<0)
	GROUP BY to_acct
) t ON to_acct=account_id
JOIN holders h ON (h.account_id=a.account_id)
WHERE user_id=userID;

END;



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
		WHERE from_acct IN (fromAcct, toAcct) AND record_id <= @entryID AND (status=7 OR (status > -1 AND amount>0))
		GROUP BY from_acct
	) f ON from_acct=account_id
	LEFT JOIN (
		SELECT to_acct, SUM(amount) AS amount
		FROM records
		WHERE to_acct IN (fromAcct, toAcct) AND record_id <= @entryID AND (status=7 OR (status > -1 AND amount<0))
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

END;



CREATE PROCEDURE `tally`(IN brandID INT)
BEGIN
SELECT brands.brand_id, 
	C.addedBudget, I.intrause, inflow, outflow, revBudget, expBudget,
	numMembers, totalMemberHours
FROM brands
LEFT JOIN ( -- budget created
	SELECT SUM(amount) AS addedBudget, f.brand_id
	FROM records r
	JOIN accounts f ON r.from_acct=f.account_id AND f.brand_id IN (brandID) AND f.sign=-1
	JOIN accounts t ON r.to_acct=t.account_id AND t.brand_id IN (brandID) AND t.sign=1
	WHERE status > -1
) C ON brands.brand_id=C.brand_id
LEFT JOIN ( -- intrause
	SELECT SUM(amount) AS intrause, f.brand_id
	FROM records r
	JOIN accounts f ON r.from_acct=f.account_id AND f.brand_id IN (brandID) AND f.sign=1
	JOIN accounts t ON r.to_acct=t.account_id AND t.brand_id IN (brandID) AND t.sign=-1
	WHERE r.status > -1
) I ON brands.brand_id=C.brand_id
LEFT JOIN ( -- inflow
	SELECT SUM(amount) AS inflow, t.brand_id
	FROM records r
	JOIN accounts f ON r.from_acct=f.account_id AND f.brand_id NOT IN (brandID) -- AND f.sign=1
	JOIN accounts t ON r.to_acct=t.account_id AND t.brand_id IN (brandID) -- AND t.sign=-1
	WHERE r.status > -1
) flowin ON brands.brand_id=C.brand_id
LEFT JOIN ( -- outflow
	SELECT SUM(amount) AS outflow, f.brand_id
	FROM records r
	JOIN accounts f ON r.from_acct=f.account_id AND f.brand_id IN (brandID) -- AND f.sign=1
	JOIN accounts t ON r.to_acct=t.account_id AND t.brand_id NOT IN (brandID) -- AND t.sign=-1
	WHERE r.status > -1
) flowout ON brands.brand_id=C.brand_id
LEFT JOIN (
	SELECT COUNT(*) AS numMembers, SUM(hours) AS totalMemberHours, brand_id FROM members WHERE brand_id IN (brandID) 
) m ON brands.brand_id=m.brand_id

LEFT JOIN ( 
	SELECT brand_id, SUM(balance+sign*(COALESCE(t.amount,0) - COALESCE(f.amount,0))) AS revBudget
	FROM accounts
	LEFT JOIN (
		SELECT from_acct, SUM(amount) AS amount 
		FROM records r
		JOIN accounts a ON a.account_id=r.from_acct 
			AND a.sign=-1 
			AND a.brand_id IN (brandID) 
			AND (status=7 OR (status > -1 AND amount>0))
	) f ON from_acct=account_id
	LEFT JOIN (
		SELECT to_acct, SUM(amount) AS amount 
		FROM records r
		JOIN accounts a ON a.account_id=r.to_acct 
			AND a.sign=-1 
			AND brand_id IN (brandID) 
			AND (status=7 OR (status > -1 AND amount<0))
	) t ON to_acct=account_id	
	WHERE brand_id IN (brandID)
) N ON brands.brand_id=N.brand_id

LEFT JOIN (
	SELECT brand_id, SUM(balance+sign*(COALESCE(t.amount,0) - COALESCE(f.amount,0))) AS expBudget
	FROM accounts
	LEFT JOIN (
		SELECT from_acct, SUM(amount) AS amount 
		FROM records r
		JOIN accounts a ON a.account_id=r.from_acct 
			AND a.sign=1 
			AND a.brand_id IN (brandID) 
			AND (status=7 OR (status > -1 AND amount>0))
	) f ON from_acct=account_id
	LEFT JOIN (
		SELECT to_acct, SUM(amount) AS amount 
		FROM records r
		JOIN accounts a ON a.account_id=r.to_acct 
			AND a.sign=1 
			AND brand_id IN (brandID) 
			AND (status=7 OR (status > -1 AND amount<0))
	) t ON to_acct=account_id
	WHERE brand_id IN (brandID)
) P ON brands.brand_id=P.brand_id
WHERE brands.brand_id IN (brandID)
GROUP BY brand_id;


END;



CREATE PROCEDURE `tatagtest`.`accountRecords` (
	IN acctID INT,
	IN minRecordID INT,
	IN maxRecordID INT
)
BEGIN

SELECT record_id, txntype, 'to' AS direction, 
	a.brand_id, b.name AS brand_name, amount, r.created, `status`, note 
FROM records r 
JOIN accounts a ON a.account_id = r.to_acct
JOIN brands b ON a.brand_id = b.brand_id
WHERE from_acct=acctID AND record_id > minRecordID AND record_id < maxRecordID

UNION ALL 

SELECT record_id, txntype, 'from' AS direction,
	a.brand_id, b.name AS brand_name, amount, r.created, `status`, note
FROM records r 
JOIN accounts a ON a.account_id = r.from_acct
JOIN brands b ON a.brand_id = b.brand_id
WHERE to_acct=acctID AND record_id > minRecordID AND record_id < maxRecordID

ORDER BY record_id DESC LIMIT 50;

END;



CREATE DEFINER=`npxer`@`localhost` PROCEDURE `holderCheck`(
	IN holderID INT
)
BEGIN

SELECT holder_id, user_id, limkey, h.authcode AS holder_auth, 
	a.brand_id, a.account_id, a.authcode AS acct_auth, sign,	
	balance+sign*(COALESCE(t.amount,0) - COALESCE(f.amount,0)) AS balance, unit	
FROM (
	SELECT * FROM holders WHERE holder_id = holderID AND ended IS NULL
) h 
JOIN accounts a ON a.account_id=h.account_id
LEFT JOIN (
	SELECT from_acct, SUM(amount) AS amount 
	FROM records
	WHERE status=7 OR (status > -1 AND amount>0)
	GROUP BY from_acct
) f ON from_acct = h.account_id
LEFT JOIN (
	SELECT to_acct, SUM(amount) AS amount 
	FROM records
	WHERE status=7 OR (status > -1 AND amount<0)
	GROUP BY to_acct
) t ON to_acct = h.account_id;

END;



CREATE PROCEDURE `tatagtest`.`userAccounts` (
	IN userID INT
)
BEGIN

SELECT a.account_id AS account_id, 
	a.name AS account_name, alias,
	h.user_id, a.brand_id AS brand_id, b.name AS brand_name, 			
	sign, balance+sign*(COALESCE(t.amount,0) - COALESCE(f.amount,0)) AS balance, unit,
	holder_id, limkey, a.authcode as account_authcode, h.authcode as holder_authcode,
	m.role As role
FROM accounts a
JOIN brands b ON a.brand_id = b.brand_id
JOIN holders h ON a.account_id=h.account_id AND h.user_id=userID
JOIN members m ON m.brand_id = a.brand_id
LEFT JOIN (
	SELECT from_acct, SUM(amount) AS amount 
	FROM records
	WHERE status=7 OR (status > -1 AND amount>0)
	GROUP BY from_acct
) f ON from_acct=a.account_id
LEFT JOIN (
	SELECT to_acct, SUM(amount) AS amount 
	FROM records
	WHERE status=7 OR (status > -1 AND amount<0)
	GROUP BY to_acct
) t ON to_acct=a.account_id
GROUP BY a.account_id;

END

-- Dump completed on 2014-12-26 20:00:04
