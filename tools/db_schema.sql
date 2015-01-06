-- MySQL dump 10.13  Distrib 5.5.28, for Win64 (x86)
--
-- Host: localhost    Database: nplite
-- ------------------------------------------------------
-- Server version	5.5.28

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `accounts`
--

DROP TABLE IF EXISTS `accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `advisors`
--

DROP TABLE IF EXISTS `advisors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `advisors` (
  `advisor_id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_id` int(11) DEFAULT NULL,
  `rater_id` int(11) DEFAULT NULL,
  `weight` float DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `ended` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`advisor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `brands` (
  `brand_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `mission` varchar(255) DEFAULT NULL,
  `description` text,
  `rating_min` float DEFAULT NULL,
  `rating_formula` varchar(255) DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `ended` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`brand_id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=104 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `carts`
--

DROP TABLE IF EXISTS `carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carts` (
  `cart_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `price` decimal(9,2) DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `ended` timestamp NULL DEFAULT NULL,
  `viewkey` varchar(5) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`cart_id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `changes`
--

DROP TABLE IF EXISTS `changes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `changes` (
  `change_id` int(11) NOT NULL AUTO_INCREMENT,
  `affected` varchar(24) DEFAULT NULL,
  `params` varchar(120) DEFAULT NULL,
  `vals` varchar(255) DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `ended` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`change_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fills`
--

DROP TABLE IF EXISTS `fills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fills` (
  `fill_id` int(11) NOT NULL AUTO_INCREMENT,
  `cart_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`fill_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `holders`
--

DROP TABLE IF EXISTS `holders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `holders` (
  `holder_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `authcode` varchar(12) DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `ended` timestamp NULL DEFAULT NULL,
  `limkey` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`holder_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_id` int(11) DEFAULT NULL,
  `name` varchar(120) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `unit` varchar(24) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `price` decimal(9,2) DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `ended` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `members`
--

DROP TABLE IF EXISTS `members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `members` (
  `member_id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `role` varchar(120) DEFAULT NULL,
  `hours` varchar(45) DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `ended` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `cart_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ratings`
--

DROP TABLE IF EXISTS `ratings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ratings` (
  `rater_id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `rating` float DEFAULT NULL,
  `rated_id` int(11) DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `ended` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`rater_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `records`
--

DROP TABLE IF EXISTS `records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `records` (
  `entry_id` int(11) NOT NULL AUTO_INCREMENT,
  `from_acct` int(11) DEFAULT NULL,
  `from_user` int(11) DEFAULT NULL,
  `to_acct` int(11) DEFAULT NULL,
  `to_user` int(11) DEFAULT NULL,
  `amount` decimal(9,2) DEFAULT NULL,
  `comment` varchar(120) DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `cart_id` int(11) DEFAULT NULL,
  `status` tinyint(3) unsigned DEFAULT '0',
  PRIMARY KEY (`entry_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(120) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `password` varchar(80) DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `ended` timestamp NULL DEFAULT NULL,
  `profileImg` varchar(255) DEFAULT NULL,
  `bannerImg` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database 'nplite'
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
	WHERE from_acct=acctID AND entry_id > 0
) f ON from_acct=account_id
LEFT JOIN (
	SELECT to_acct, SUM(amount) AS amount 
	FROM records
	WHERE to_acct=acctID AND entry_id > 0
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
	WHERE from_acct IN (fromAcct, toAcct) 
	GROUP BY from_acct
) f ON from_acct=account_id
LEFT JOIN (
	SELECT to_acct, SUM(amount) AS amount 
	FROM records
	WHERE to_acct IN (fromAcct, toAcct) 
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
	GROUP BY from_acct
) f ON from_acct=account_id
LEFT JOIN (
	SELECT to_acct, SUM(amount) AS amount 
	FROM records 
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
	INSERT INTO records (from_acct,from_user,to_acct,to_user,amount,`comment`,cart_id,created,status)
	VALUES (fromAcct, fromUser, toAcct, toUser, amt, note, cartID, NOW(), 0);

	SELECT last_insert_id() into @entryID;

	SELECT COUNT(*) INTO @num
	FROM accounts
	LEFT JOIN (
		SELECT from_acct, SUM(amount) AS amount 
		FROM records
		WHERE from_acct IN (fromAcct, toAcct) AND entry_id <= @entryID
		GROUP BY from_acct
	) f ON from_acct=account_id
	LEFT JOIN (
		SELECT to_acct, SUM(amount) AS amount
		FROM records
		WHERE to_acct IN (fromAcct, toAcct) AND entry_id <= @entryID
		GROUP BY to_acct
	) t ON to_acct=account_id
	WHERE account_id IN (fromAcct, toAcct)
		AND balance+sign*(COALESCE(t.amount,0) - COALESCE(f.amount,0)) > 0;

	IF @num IS NULL OR @num < 2 THEN 
		DELETE FROM records WHERE entry_id=@entryID;
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
) C ON brands.brand_id=C.brand_id
LEFT JOIN ( -- intrause
	SELECT SUM(amount) AS intrause, f.brand_id
	FROM records r
	JOIN accounts f ON r.from_acct=f.account_id AND f.brand_id IN (brandID) AND f.sign=1
	JOIN accounts t ON r.to_acct=t.account_id AND t.brand_id IN (brandID) AND t.sign=-1
	WHERE r.status<10
) I ON brands.brand_id=C.brand_id
LEFT JOIN ( -- inflow
	SELECT SUM(amount) AS inflow, t.brand_id
	FROM records r
	JOIN accounts f ON r.from_acct=f.account_id AND f.brand_id NOT IN (brandID) -- AND f.sign=1
	JOIN accounts t ON r.to_acct=t.account_id AND t.brand_id IN (brandID) -- AND t.sign=-1
	WHERE r.status<10
) flowin ON brands.brand_id=C.brand_id
LEFT JOIN ( -- outflow
	SELECT SUM(amount) AS outflow, f.brand_id
	FROM records r
	JOIN accounts f ON r.from_acct=f.account_id AND f.brand_id IN (brandID) -- AND f.sign=1
	JOIN accounts t ON r.to_acct=t.account_id AND t.brand_id NOT IN (brandID) -- AND t.sign=-1
	WHERE r.status<10
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
		JOIN accounts a ON a.account_id=r.from_acct AND a.sign=-1 AND a.brand_id IN (brandID) AND r.status<10
	) f ON from_acct=account_id
	LEFT JOIN (
		SELECT to_acct, SUM(amount) AS amount 
		FROM records r
		JOIN accounts a ON a.account_id=r.to_acct AND a.sign=-1 AND brand_id IN (brandID) AND r.status<10
	) t ON to_acct=account_id	
	WHERE brand_id IN (brandID)
) N ON brands.brand_id=N.brand_id
LEFT JOIN (
	SELECT brand_id, SUM(balance+sign*(COALESCE(t.amount,0) - COALESCE(f.amount,0))) AS expBudget
	FROM accounts
	LEFT JOIN (
		SELECT from_acct, SUM(amount) AS amount 
		FROM records r
		JOIN accounts a ON a.account_id=r.from_acct AND a.sign=1 AND a.brand_id IN (brandID) AND r.status<10
	) f ON from_acct=account_id
	LEFT JOIN (
		SELECT to_acct, SUM(amount) AS amount 
		FROM records r
		JOIN accounts a ON a.account_id=r.to_acct AND a.sign=1 AND brand_id IN (brandID) AND r.status<10
	) t ON to_acct=account_id
	WHERE brand_id IN (brandID)
) P ON brands.brand_id=P.brand_id
WHERE brands.brand_id IN (brandID)
GROUP BY brand_id;


END;

-- Dump completed on 2014-12-26 20:00:04
