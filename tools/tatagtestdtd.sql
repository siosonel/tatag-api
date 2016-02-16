CREATE DATABASE  IF NOT EXISTS `tatagtestdtd` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `tatagtestdtd`;
-- MySQL dump 10.13  Distrib 5.5.28, for Win64 (x86)
--
-- Host: localhost    Database: tatagtest
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
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NULL DEFAULT NULL,
  `ended` timestamp NULL DEFAULT NULL,
  `throttle_id` int(11) DEFAULT '0',
  PRIMARY KEY (`account_id`)
) ENGINE=MEMORY AUTO_INCREMENT=120 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounts`
--

LOCK TABLES `accounts` WRITE;
/*!40000 ALTER TABLE `accounts` DISABLE KEYS */;
INSERT INTO `accounts` VALUES (92,104,'Main Revenue','cftix','hour',1399.98,-1,'2015-01-05 19:53:31','2015-11-18 04:39:17',NULL,0),(93,104,'Main Expense','cftix','hour',1221.82,1,'2015-01-05 19:53:31','2015-11-18 05:12:08',NULL,0),(94,104,'Personal Expense','ti','hour',187.53,1,'2015-01-05 19:53:32',NULL,NULL,0),(95,105,'Main Revenue','cftix','hour',1000.00,-1,'2015-01-05 19:53:33',NULL,NULL,0),(96,105,'Main Expense','cftix','hour',990.63,1,'2015-01-05 19:53:33',NULL,NULL,0),(97,104,'Test Expense','ftix','hour',0.00,1,'2015-01-05 19:53:33',NULL,NULL,0),(98,106,'Main Revenue','cftix','hour',0.00,-1,'2015-08-09 01:38:54',NULL,NULL,0),(99,106,'Main Expense','cftix','hour',0.00,1,'2015-08-09 01:38:54',NULL,NULL,0),(100,107,'Main Revenue','cftix','hour',0.00,-1,'2015-08-09 01:39:44',NULL,NULL,0),(101,107,'Main Expense','cftix','hour',0.00,1,'2015-08-09 01:39:44',NULL,NULL,0),(102,108,'Main Revenue','cftix','hour',0.00,-1,'2015-08-09 01:40:26',NULL,NULL,0),(103,108,'Main Expense','cftix','hour',0.00,1,'2015-08-09 01:40:26',NULL,NULL,0),(104,109,'Main Revenue','cftix','hour',0.00,-1,'2015-08-09 01:42:51',NULL,NULL,0),(105,109,'Main Expense','cftix','hour',0.00,1,'2015-08-09 01:42:51',NULL,NULL,0),(106,110,'Main Revenue','cftix','hour',0.00,-1,'2015-08-09 01:45:35',NULL,NULL,0),(107,110,'Main Expense','cftix','hour',0.00,1,'2015-08-09 01:45:35',NULL,NULL,0),(108,111,'Main Revenue','cftix','hour',0.00,-1,'2015-08-09 01:46:25',NULL,NULL,0),(109,111,'Main Expense','cftix','hour',0.00,1,'2015-08-09 01:46:25',NULL,NULL,0),(110,112,'Main Revenue','cftix','hour',0.00,-1,'2015-08-09 01:47:43',NULL,NULL,0),(111,112,'Main Expense','cftix','hour',0.00,1,'2015-08-09 01:47:43',NULL,NULL,0),(112,113,'Main Revenue','cftix','hour',0.00,-1,'2015-08-09 01:49:43',NULL,NULL,0),(113,113,'Main Expense','cftix','hour',0.00,1,'2015-08-09 01:49:43',NULL,NULL,0),(114,114,'Main Revenue','cftix','hour',0.00,-1,'2015-08-09 01:53:23',NULL,NULL,0),(115,114,'Main Expense','cftix','hour',0.00,1,'2015-08-09 01:53:23',NULL,NULL,0),(116,115,'Main Revenue','cftix','hour',0.00,-1,'2015-11-18 18:50:05',NULL,NULL,0),(117,115,'Main Expense','cftix','hour',0.00,1,'2015-11-18 18:50:05',NULL,NULL,0),(118,116,'Main Revenue','cftix','hour',0.00,-1,'2015-11-19 04:21:32',NULL,NULL,0),(119,116,'Main Expense','cftix','hour',0.00,1,'2015-11-19 04:21:32',NULL,NULL,0);
/*!40000 ALTER TABLE `accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=MEMORY AUTO_INCREMENT=117 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brands`
--

LOCK TABLES `brands` WRITE;
/*!40000 ALTER TABLE `brands` DISABLE KEYS */;
INSERT INTO `brands` VALUES 
(104,'abc','to be the first brand','for testing',0,'0','2015-01-05 19:53:31',NULL,NULL,NULL,0,'USA','206','for-profit',9,NULL,NULL)
,(105,'def','to be the second brand','for testing',0,'0','2015-01-05 19:53:33',NULL,NULL,NULL,0,'USA','206','for-profit',9,NULL,NULL)
,(106,'~Amazon.com, Boren Avenue North, Seattle, WA, United States','simulate a well-known brand for whitelisting or blacklisting','This is a simulated brand to be used for testing the tatag system.',NULL,NULL,'2015-08-09 01:38:54',NULL,NULL,NULL,NULL,'USA','206','sim',10,NULL,NULL)
,(107,'~The Boeing Company, West Casino Road, Everett, WA, United States','simulate a well-known brand for whitelisting or blacklisting','This is a simulated brand to be used for testing the tatag system.',NULL,NULL,'2015-08-09 01:39:44',NULL,NULL,NULL,NULL,'USA','206','sim',10,NULL,NULL)
,(108,'~Starbucks, Aurora Avenue North, Shoreline, WA, United States','simulate a well-known brand for whitelisting or blacklisting','This is a simulated brand to be used for testing the tatag system.',NULL,NULL,'2015-08-09 01:40:26',NULL,NULL,NULL,NULL,'USA','206','sim',10,NULL,NULL)
,(109,'~First Peoples Worldwide, Leeland Road, Fredericksburg, VA, United States','simulate a well-known brand for whitelisting or blacklisting','This is a simulated brand to be used for testing the tatag system.',NULL,NULL,'2015-08-09 01:42:51',NULL,NULL,NULL,NULL,'USA','206','sim',10,NULL,NULL)
,(110,'~slumaid.org','simulate a well-known brand for whitelisting or blacklisting','This is a simulated brand to be used for testing the tatag system.',NULL,NULL,'2015-08-09 01:45:35',NULL,NULL,NULL,NULL,'USA','206','sim',10,NULL,NULL)
,(111,'~National Rifle Association, Waples Mill Road, Fairfax, VA, United States','simulate a well-known brand for whitelisting or blacklisting','This is a simulated brand to be used for testing the tatag system.',NULL,NULL,'2015-08-09 01:46:25',NULL,NULL,NULL,NULL,'USA','206','sim',10,NULL,NULL)
,(112,'~Koch Industries, East 37th Street North, Wichita, KS, United States','simulate a well-known brand for whitelisting or blacklisting','This is a simulated brand to be used for testing the tatag system.',NULL,NULL,'2015-08-09 01:47:43',NULL,NULL,NULL,NULL,'USA','206','sim',10,NULL,NULL)
,(113,'~McDonald\'s Corporation, West Wade Hampton Boulevard, Greer, SC, United States','simulate a well-known brand for whitelisting or blacklisting','This is a simulated brand to be used for testing the tatag system.',NULL,NULL,'2015-08-09 01:49:43',NULL,NULL,NULL,NULL,'USA','206','sim',10,NULL,NULL)
,(114,'~P-Patch, Lynn Street, Seattle, WA, United States','simulate a well-known brand for whitelisting or blacklisting','This is a simulated brand to be used for testing the tatag system.',NULL,NULL,'2015-08-09 01:53:23',NULL,NULL,NULL,NULL,'USA','206','sim',10,NULL,NULL)
,(115,'~Echo Lake Elementary School, Seattle, WA, United States','simulate a well-known brand for whitelisting or blacklisting','This is a simulated brand to be used for testing the tatag system.',NULL,NULL,'2015-11-18 18:50:05',NULL,NULL,NULL,NULL,'USA','206','sim',10,NULL,NULL)
,(116,'~Pizza Hut, Washington 99, Edmonds, WA, United States','simulate a well-known brand for whitelisting or blacklisting','This is a simulated brand to be used for testing the tatag system.',NULL,NULL,'2015-11-19 04:21:32',NULL,NULL,NULL,NULL,'USA','206','sim',10,NULL,NULL);
/*!40000 ALTER TABLE `brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `consumers`
--

DROP TABLE IF EXISTS `consumers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `consumers` (
  `consumer_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL,
  `secret` varchar(80) DEFAULT NULL,
  `redirect_url` varchar(45) DEFAULT NULL,
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NULL DEFAULT NULL,
  `ended` timestamp NULL DEFAULT NULL,
  `user_id` int(11) DEFAULT '0',
  PRIMARY KEY (`consumer_id`)
) ENGINE=MEMORY AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='Machine consumers of the API';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `consumers`
--

LOCK TABLES `consumers` WRITE;
/*!40000 ALTER TABLE `consumers` DISABLE KEYS */;
INSERT INTO `consumers` VALUES 
(1,'login','login','$2y$10$fsqU/JRVmc3fXxVA3pclsO9/jyXur2RDIIlIq/5KXy1PVbsc39cc.',NULL,'2015-11-16 22:59:28',NULL,NULL,21)
,(2,'tatag-ui','ui','$2y$10$fsqU/JRVmc3fXxVA3pclsO9/jyXur2RDIIlIq/5KXy1PVbsc39cc.',NULL,'2015-11-16 22:59:28',NULL,NULL,21)
,(3,'flora','sim','$2y$10$fsqU/JRVmc3fXxVA3pclsO9/jyXur2RDIIlIq/5KXy1PVbsc39cc.',NULL,'2015-11-16 22:59:28',NULL,NULL,21)
,(4,'advisorX','advisor','$2y$10$fsqU/JRVmc3fXxVA3pclsO9/jyXur2RDIIlIq/5KXy1PVbsc39cc.',NULL,NOW(),NULL,NULL,21);
/*!40000 ALTER TABLE `consumers` ENABLE KEYS */;
UNLOCK TABLES;

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
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NULL DEFAULT NULL,
  `ended` timestamp NULL DEFAULT NULL,
  `limkey` varchar(16) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`holder_id`)
) ENGINE=MEMORY AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `holders`
--

LOCK TABLES `holders` WRITE;
/*!40000 ALTER TABLE `holders` DISABLE KEYS */;
INSERT INTO `holders` VALUES (41,21,92,'cftix','2015-01-05 19:53:31',NULL,NULL,'abc',NULL),(42,21,93,'cftix','2015-01-05 19:53:31','2015-12-08 02:48:17',NULL,'abc','Main Expense'),(43,21,94,'ftix','2015-01-05 19:53:32','2015-12-11 05:29:32',NULL,'abc','personal expense'),(44,22,95,'cftix','2015-01-05 19:53:33',NULL,NULL,'abc',NULL),(45,22,96,'cftix','2015-01-05 19:53:33',NULL,NULL,'abc',NULL);
/*!40000 ALTER TABLE `holders` ENABLE KEYS */;
UNLOCK TABLES;

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
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NULL DEFAULT NULL,
  `ended` timestamp NULL DEFAULT NULL,
  `joined` timestamp NULL DEFAULT NULL,
  `revoked` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`member_id`)
) ENGINE=MEMORY AUTO_INCREMENT=58 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `members`
--

LOCK TABLES `members` WRITE;
/*!40000 ALTER TABLE `members` DISABLE KEYS */;
INSERT INTO `members` VALUES (53,104,21,'admin','0','2015-01-05 19:53:31','2015-11-18 00:15:14',NULL,'2015-11-17 12:15:14',NULL),(54,105,22,'admin','0','2015-01-05 19:53:33',NULL,NULL,NULL,NULL),(55,104,23,'staff','0','2015-01-05 19:53:31',NULL,NULL,NULL,NULL),(56,115,21,'admin','0','2015-11-18 18:50:05',NULL,'2015-11-18 18:50:05',NULL,NULL),(57,116,21,'admin','0','2015-11-19 04:21:32',NULL,'2015-11-19 04:21:32',NULL,NULL);
/*!40000 ALTER TABLE `members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promos`
--

DROP TABLE IF EXISTS `promos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `promos` (
  `promo_id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_id` int(11) DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
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
) ENGINE=MEMORY AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promos`
--

LOCK TABLES `promos` WRITE;
/*!40000 ALTER TABLE `promos` DISABLE KEYS */;
INSERT INTO `promos` VALUES 
(1,104,'Test promo','A brand #104 promo',NULL,NULL,25.00,NULL,'2015-01-01 00:00:00',NULL,NULL,NULL,1,'software')
,(2,105,'Test promo','A brand #105 promo',NULL,NULL,25.00,NULL,'2015-01-01 00:00:00',NULL,NULL,NULL,4,'food')
,(3,104,'product promo 1f8','a brand 104 promo 1f8',NULL,NULL,57.57,NULL,'2015-11-18 00:15:57',NULL,NULL,'2016-12-31 08:00:00',5,'grocery')
,(4,104,'product promo 67','a brand 104 promo 67',NULL,NULL,57.69,NULL,'2015-11-18 00:16:09',NULL,NULL,'2016-12-31 08:00:00',6,'ride')
,(5,104,'product promo 3cc','a brand 104 promo 3cc',NULL,NULL,57.71,NULL,'2015-11-18 00:16:12',NULL,NULL,'2016-12-31 08:00:00',7,'drink')
,(6,104,'product promo 3cc','a brand 104 promo 3cc',NULL,NULL,57.71,NULL,'2015-11-18 00:16:12',NULL,NULL,'2016-12-31 08:00:00',8,'book')
,(7,104,'test','test','',NULL,7.77,NULL,'2015-11-18 18:05:36',NULL,NULL,'2016-12-31 08:00:00',9,'grocery')
,(8,104,'help me','test','',NULL,44.44,NULL,'2015-11-18 18:11:48',NULL,NULL,'2016-12-31 08:00:00',10,'ad')
,(9,104,'raisins','test','',NULL,3.33,NULL,'2015-11-18 18:14:48',NULL,NULL,'2016-12-31 08:00:00',11,'ad')
,(10,104,'mmm','nnn','',NULL,2.22,NULL,'2015-11-18 18:15:31',NULL,NULL,'2016-12-31 08:00:00',12,'ad')
,(11,104,'finallyu','yes','',NULL,1.11,NULL,'2015-11-18 18:36:58',NULL,NULL,'2016-12-31 08:00:00',13,'ad');
/*!40000 ALTER TABLE `promos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ratings`
--

DROP TABLE IF EXISTS `ratings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=MEMORY AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ratings`
--

LOCK TABLES `ratings` WRITE;
/*!40000 ALTER TABLE `ratings` DISABLE KEYS */;
INSERT INTO `ratings` VALUES (1,106,21,95,'++customer service; --community involvement;','2015-08-09 01:38:54',NULL,NULL),(2,107,21,95,'++technical excellence; ','2015-08-09 01:39:44',NULL,NULL),(3,108,21,95,'++community involvement; -fair trade;','2015-08-09 01:40:26',NULL,NULL),(4,109,21,95,'++social cause','2015-08-09 01:42:51',NULL,NULL),(5,110,21,100,'++social cause;','2015-08-09 01:45:35',NULL,NULL),(6,111,21,0,'---social responsibility;','2015-08-09 01:46:25',NULL,NULL),(7,112,21,10,'--social responsibility; --climate responsibility;','2015-08-09 01:47:43',NULL,NULL),(8,113,21,40,'--dietary health; --environmental impact;','2015-08-09 01:49:43',NULL,NULL),(9,114,21,100,'++environmental impact; ++community health','2015-08-09 01:53:23',NULL,NULL),(10,115,21,100,'++good school;','2015-11-18 18:50:05',NULL,NULL),(11,116,21,64,'--social responsibility','2015-11-19 04:21:32',NULL,NULL);
/*!40000 ALTER TABLE `ratings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `records`
--

DROP TABLE IF EXISTS `records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `order_step` tinyint(3) DEFAULT '0',
  PRIMARY KEY (`record_id`)
) ENGINE=MEMORY AUTO_INCREMENT=65 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `records`
--

LOCK TABLES `records` WRITE;
/*!40000 ALTER TABLE `records` DISABLE KEYS */;
INSERT INTO `records` 
VALUES 
(30,'np',92,21,93,21,1000.00,'first budget','2015-01-05 19:53:32',NULL,0,7,0,0,0,'0',0)
,(31,'pp',93,21,94,21,35.87,'wages','2015-01-05 19:53:32',NULL,0,7,0,0,0,'0',0)
,(32,'pn',94,21,92,21,2.05,'disounted employee purchase','2015-01-05 19:53:32','2015-11-19 07:15:05',0,7,0,0,0,'0',0)
,(33,'np',95,22,96,22,1000.00,'first budget','2015-01-05 19:53:33',NULL,0,7,0,0,0,'0',0)
,(34,'pn',96,22,92,21,9.37,'first external budget use','2015-01-05 19:53:33','2015-11-19 07:15:41',0,7,0,0,0,'0',0)
,(35,'pn',94,21,92,21,2.05,'disounted employee purchase','2015-01-06 03:13:00','2015-11-19 07:14:04',0,7,0,0,0,'0',0)
,(36,'pn',94,21,92,21,2.05,'disounted employee purchase','2015-01-06 03:29:57','2015-11-18 06:07:35',0,7,0,0,0,'0',0)
,(37,'pp',93,21,94,21,55.55,'test','2015-11-18 06:21:49','2015-11-18 06:21:49',NULL,7,0,0,0,'0',0)
,(38,'np',92,21,93,21,100.11,'test','2015-11-18 06:24:12','2015-11-18 06:24:12',NULL,7,0,0,0,'0',0)
,(39,'pn',93,21,92,21,9.99,'test','2015-11-18 06:28:26','2015-11-18 06:28:39',NULL,7,0,0,0,'0',0)
,(40,'pn',93,21,92,21,25.00,'for ad-1','2015-11-18 15:21:15','2015-11-19 07:10:25',NULL,7,0,1,1,'0',0)
,(41,'np',92,21,93,21,400.00,'test','2015-11-18 19:00:51','2015-11-18 19:00:51',NULL,7,0,0,0,'0',0)
,(42,'pp',93,21,94,21,100.00,'test','2015-11-19 04:21:56','2015-11-19 04:21:56',NULL,7,0,0,0,'0',0)
,(43,'pp',93,21,94,21,10.11,'test','2015-11-19 04:28:08','2015-11-19 04:28:08',NULL,7,0,0,0,'0',0)
,(44,'pp',93,21,94,21,3.33,'test','2015-11-19 04:29:32','2015-11-19 04:29:32',NULL,7,0,0,0,'0',0)
,(45,'pp',93,21,94,21,1.11,'test','2015-11-19 04:36:54','2015-11-19 04:36:54',NULL,7,0,0,0,'0',0)
,(46,'pp',93,21,94,21,2.22,'test','2015-11-19 04:38:24','2015-11-19 04:38:24',NULL,7,0,0,0,'0',0)
,(47,'pp',93,21,94,21,4.32,'test','2015-11-19 04:43:12','2015-11-19 04:43:12',NULL,7,0,0,0,'0',0)
,(48,'pn',94,21,92,21,12.00,'test','2015-11-19 04:43:58','2015-11-19 04:44:13',NULL,7,0,0,0,'0',0)
,(49,'pn',93,21,92,21,2.92,'test','2015-11-19 06:55:26','2015-11-19 06:56:06',NULL,7,0,0,0,'0',0)
,(50,'pn',94,21,92,21,2.22,'','2015-11-19 07:58:48','2015-11-19 07:59:12',NULL,7,0,0,0,'0',0)
,(51,'pn',93,21,92,21,2.34,'test','2015-11-19 08:00:56','2015-11-19 08:01:38',NULL,7,0,0,0,'0',0)
,(52,'pn',94,21,92,21,8.88,'test','2015-11-19 08:07:35','2015-11-19 08:07:52',NULL,7,0,0,0,'0',0)
,(53,'pp',93,21,94,21,5.55,'test','2015-11-21 06:16:55','2015-11-21 06:16:55',NULL,7,0,0,0,'0',0)
,(54,'pn',94,21,92,21,1.11,'test','2015-11-21 06:17:30','2015-11-21 06:36:01',NULL,7,0,0,0,'0',0)
,(55,'pn',94,21,92,21,1.11,'test','2015-11-21 06:20:57','2015-11-21 06:23:24',NULL,7,0,0,0,'0',0)
,(56,'pn',94,21,92,21,3.33,'test','2015-11-21 06:37:23','2015-11-21 06:37:35',NULL,7,0,0,0,'0',0)
,(57,'pn',94,21,92,21,2.22,'','2015-11-21 06:44:29','2015-11-21 15:08:59',NULL,7,0,0,0,'0',0)
,(58,'pn',93,21,92,21,7.77,'','2015-11-21 14:58:30','2015-11-21 15:23:13',NULL,7,0,0,0,'0',0)
,(59,'pn',93,21,92,21,6.66,'','2015-11-21 15:26:06','2015-11-21 15:26:21',NULL,7,0,0,0,'0',0)
,(60,'pn',93,21,92,21,4.56,'','2015-11-21 15:27:59','2015-11-21 15:30:40',NULL,7,0,0,0,'0',0)
,(61,'pn',93,21,92,21,0.99,'','2015-11-21 17:26:29','2015-11-21 17:27:23',NULL,7,0,0,0,'0',0)
,(62,'pn',94,21,92,21,-3.33,'','2015-11-21 17:48:37','2015-11-21 17:49:01',NULL,7,0,0,0,'0',0)
,(63,'pn',94,21,92,21,-2.05,'','2015-11-21 17:57:47','2015-11-21 17:58:16',NULL,7,0,0,0,'0',0)
,(64,'pn',94,21,92,21,-1.11,'','2015-11-21 22:10:38','2015-11-21 22:21:47',NULL,7,0,0,0,'0',0);
/*!40000 ALTER TABLE `records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `relays`
--

DROP TABLE IF EXISTS `relays`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=MEMORY AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `relays`
--

LOCK TABLES `relays` WRITE;
/*!40000 ALTER TABLE `relays` DISABLE KEYS */;
INSERT INTO `relays` VALUES (1,41,NULL,0.01,100.00,NULL,'test','2015-01-01 00:00:00',NULL,NULL,'pn',25,5,25,24),(2,41,NULL,5.00,10.00,NULL,'test','2015-01-01 00:00:00',NULL,NULL,'pn',25,5,25,0),(3,44,'xyz',0.01,50.00,NULL,'test-brand-105','2015-01-01 00:00:00',NULL,NULL,'pn',25,5,25,0),(4,44,'qrs',0.01,100.00,NULL,'test','2015-01-01 00:00:00',NULL,NULL,'pn',25,5,0,24),(5,41,'7d02',57.57,57.57,NULL,NULL,'2015-11-18 00:15:57',NULL,NULL,'pn',25,5,2,24),(6,41,'eb7b',57.69,57.69,NULL,NULL,'2015-11-18 00:16:09',NULL,NULL,'pn',25,5,2,24),(7,41,'d85b',57.71,57.71,NULL,NULL,'2015-11-18 00:16:12',NULL,NULL,'pn',25,5,2,24),(8,41,'77aa',57.71,57.71,NULL,NULL,'2015-11-18 00:16:12',NULL,NULL,'pn',25,5,2,24),(9,41,'3675',7.77,7.77,NULL,NULL,'2015-11-18 18:05:36',NULL,NULL,'pn',25,5,2,24),(10,41,'16085',44.44,44.44,NULL,NULL,'2015-11-18 18:11:48',NULL,NULL,'pn',25,5,2,24),(11,41,'b537',3.33,3.33,NULL,NULL,'2015-11-18 18:14:48',NULL,NULL,'pn',25,5,2,24),(12,41,'10023',2.22,2.22,NULL,NULL,'2015-11-18 18:15:31',NULL,NULL,'pn',25,5,2,24),(13,41,'7936',1.11,1.11,NULL,NULL,'2015-11-18 18:36:58',NULL,NULL,'pn',25,5,2,24);
/*!40000 ALTER TABLE `relays` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reversals`
--





DROP TABLE IF EXISTS `reports`;

CREATE TABLE `reports` (
  `report_id` int(11) NOT NULL AUTO_INCREMENT,
  `txntype` varchar(2) DEFAULT NULL,
  `from_brand` int(11) DEFAULT NULL,
  `to_brand` int(11) DEFAULT NULL,
  `amount` decimal(9,2) DEFAULT '0.00',
  `max_id` int(11) DEFAULT NULL,
  `max_updated` timestamp NULL DEFAULT NULL,
  `keyword` varchar(16) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`report_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;





DROP TABLE IF EXISTS `reversals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reversals` (
  `orig_record_id` int(11) NOT NULL DEFAULT '0',
  `rev_record_id` int(11) NOT NULL DEFAULT '0',
  `adjusted_amt` decimal(7,2) DEFAULT '0.00',
  `note` varchar(160) DEFAULT NULL,
  `txntype` varchar(2) DEFAULT NULL,
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`orig_record_id`,`rev_record_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reversals`
--

LOCK TABLES `reversals` WRITE;
/*!40000 ALTER TABLE `reversals` DISABLE KEYS */;
INSERT INTO `reversals` VALUES (56,62,0.00,NULL,'pn','2015-11-21 17:48:37',NULL),(32,63,0.00,NULL,'pn','2015-11-21 17:57:47',NULL),(55,64,0.00,NULL,'pn','2015-11-21 22:10:38',NULL);
/*!40000 ALTER TABLE `reversals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tallies`
--

DROP TABLE IF EXISTS `tallies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=MEMORY DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tallies`
--

LOCK TABLES `tallies` WRITE;
/*!40000 ALTER TABLE `tallies` DISABLE KEYS */;
/*!40000 ALTER TABLE `tallies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `throttles`
--

DROP TABLE IF EXISTS `throttles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=MEMORY AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `throttles`
--

LOCK TABLES `throttles` WRITE;
/*!40000 ALTER TABLE `throttles` DISABLE KEYS */;
INSERT INTO `throttles` VALUES (1,104,172800,100,20,2,'2015-02-24 10:48:00',NULL,NULL,NULL),(2,104,3600,10,2,1,'2015-02-24 10:48:00',NULL,NULL,NULL),(3,104,9999999,100,20,2,'2015-02-24 10:48:00',NULL,NULL,NULL),(4,104,360000,11,5,1,'2015-11-18 05:16:54',NULL,NULL,NULL);
/*!40000 ALTER TABLE `throttles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tokens`
--

DROP TABLE IF EXISTS `tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=MEMORY AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COMMENT='Used by users to authorize API consumers ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tokens`
--

LOCK TABLES `tokens` WRITE;
/*!40000 ALTER TABLE `tokens` DISABLE KEYS */;
INSERT INTO `tokens` VALUES (1,2,21,'16465338','96806169','2015-11-16 23:00:06','2015-11-16 23:00:11','gp'),(2,2,21,'41336212','95994219','2015-11-17 20:36:59','2015-11-17 20:37:44','gp'),(3,2,21,'42049249','39492669','2015-11-17 23:30:25','2015-11-17 23:30:29','gp'),(4,2,21,'31625625','53647150','2015-11-18 02:47:00','2015-11-18 02:47:06','gp'),(5,2,21,'41255725','56459898','2015-11-18 15:21:05','2015-11-18 15:21:10','gp'),(6,2,21,'11479086','80446726','2015-11-18 21:17:07','2015-11-18 21:17:10','gp'),(7,2,21,'82266958','204533','2015-11-19 03:55:53','2015-11-19 03:56:24','gp'),(8,2,21,'48592112','80248949','2015-11-19 06:56:42','2015-11-19 06:56:46','gp'),(9,2,21,'60268258','75828525','2015-11-20 19:52:57','2015-11-20 19:53:06','gp'),(10,2,21,'87877460','96355219','2015-11-21 05:12:45','2015-11-21 05:12:50','gp'),(11,2,21,'91630435','79480393','2015-11-21 15:22:30','2015-11-21 15:22:34','gp'),(12,2,21,'24080522','94022405','2015-11-21 16:54:42','2015-11-21 16:55:05','gp'),(13,2,21,'73720865','12759858','2015-11-22 02:58:20','2015-11-22 02:58:25','gp'),(14,2,21,'63905089','17475745','2015-12-03 18:30:51','2015-12-03 18:30:55','gp'),(15,2,NULL,'485913','0','2015-12-04 16:17:31',NULL,'0'),(16,2,21,'87870393','4698430','2015-12-04 16:22:25','2015-12-04 16:22:30','gp'),(17,2,NULL,'52443','0','2015-12-05 21:51:13',NULL,'0'),(18,2,NULL,'894826','0','2015-12-05 21:51:16',NULL,'0'),(19,2,NULL,'916654','0','2015-12-06 01:39:31',NULL,'0'),(20,2,NULL,'232638','0','2015-12-06 01:39:32',NULL,'0'),(21,2,21,'25683350','55600959','2015-12-08 17:08:24','2015-12-08 17:08:30','gp'),(22,2,21,'38149652','63581831','2015-12-11 05:37:24','2015-12-11 05:37:29','gp'),(23,2,21,'72470946','16383620','2015-12-11 23:37:23','2015-12-11 23:37:28','gp'),(24,2,21,'85251895','98814823','2015-12-18 04:12:24','2015-12-18 04:12:33','gp'),(25,2,NULL,'356668','0','2015-12-18 17:45:43',NULL,'0'),(26,2,21,'63442687','85479400','2015-12-18 17:46:50','2015-12-18 17:46:54','gp'),(27,2,NULL,'883235','0','2015-12-19 00:51:00',NULL,'0');
/*!40000 ALTER TABLE `tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `useraccount_account`
--

DROP TABLE IF EXISTS `useraccount_account`;
/*!50001 DROP VIEW IF EXISTS `useraccount_account`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `useraccount_account` (
  `brand_id` tinyint NOT NULL,
  `name` tinyint NOT NULL,
  `role` tinyint NOT NULL,
  `user_id` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

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
) ENGINE=MEMORY AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (21,'user21@email.org','User One','$2y$10$6AtN5uH28i6jwugnPLf3DOv1H9HzSruikFEfssr2ls0kESJLtZ1zS','2015-01-05 19:53:31',NULL,NULL,'gp',NULL,'105726759246117896959',NULL,2,NULL,NULL,NULL,NULL,NULL),(22,'user22@email.org','User Two','$2y$10$fsqU/JRVmc3fXxVA3pclsO9/jyXur2RDIIlIq/5KXy1PVbsc39cc.','2015-01-05 19:53:33',NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,NULL,NULL,NULL,NULL),(23,'user23@email.org','User Three','$2y$10$fsqU/JRVmc3fXxVA3pclsO9/jyXur2RDIIlIq/5KXy1PVbsc39cc.','2015-01-05 19:53:33',NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,NULL,NULL,NULL,NULL),(24,'user24@email.org','User Four','$2y$10$fsqU/JRVmc3fXxVA3pclsO9/jyXur2RDIIlIq/5KXy1PVbsc39cc.','2015-01-05 19:53:33',NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'tatagtest'
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

SELECT record_id, txntype, 'to' AS role, r.throttle_id,
	to_acct AS other_acct,
	a.brand_id, b.name AS brand_name, amount, 
	r.created, `status`, note, UNIX_TIMESTAMP(r.updated) as updated
FROM records r 
JOIN accounts a ON a.account_id = r.to_acct
JOIN brands b ON a.brand_id = b.brand_id
WHERE from_acct=acctID AND record_id < maxRecordID AND record_id > cutoffID
	AND (r.updated IS NULL OR UNIX_TIMESTAMP(r.updated) > minUpdated)
UNION ALL 

SELECT record_id, txntype, 'from' AS role, r.throttle_id,
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

SELECT accounts.account_id AS id, name, 
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
    (COALESCE(addedf,0) + COALESCE(addedt,0) - COALESCE(outflow,0) - COALESCE(inflow,0) - COALESCE(transferredf,0) + COALESCE(transferredt,0)) as actual,
    COALESCE(addedf,0), COALESCE(addedt,0),
    COALESCE(outflow,0), COALESCE(inflow,0),
    COALESCE(transferredf,0), COALESCE(transferredt,0)    
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
  left join (
    select from_acct, SUM(amount) as transferredf
    from records r
    where status=7 and (txntype='pp' OR txntype='nn') and to_acct IS NOT NULL and from_acct IS NOT NULL
    group by from_acct
  ) transf on transf.from_acct = a.account_id
  left join (
    select to_acct, SUM(amount) as transferredt
    from records r
    where status=7 and (txntype='pp' OR txntype='nn') and to_acct IS NOT NULL and from_acct IS NOT NULL
    group by to_acct
  ) transt on transt.to_acct = a.account_id
) byrec
where balance<0 OR byrec.balance != actual
;

END;;


