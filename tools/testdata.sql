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
-- Dumping data for table `accounts`
--

LOCK TABLES `accounts` WRITE;
/*!40000 ALTER TABLE `accounts` DISABLE KEYS */;
INSERT INTO `accounts` VALUES 
(92,104,'Main Revenue','cftix','hour',1000.00,-1,'2015-01-05 19:53:31',NULL,NULL,0),
(93,104,'Main Expense','cftix','hour',964.13,1,'2015-01-05 19:53:31',NULL,NULL,0),
(94,104,'Personal Expense','ti','hour',35.87,1,'2015-01-05 19:53:32',NULL,NULL,0),
(95,105,'Main Revenue','cftix','hour',1000.00,-1,'2015-01-05 19:53:33',NULL,NULL,0),
(96,105,'Main Expense','cftix','hour',1000.00,1,'2015-01-05 19:53:33',NULL,NULL,0),
(97,104,'Test Expense','ftix','hour',0.00,1,'2015-01-05 19:53:33',NULL,NULL,0);
/*!40000 ALTER TABLE `accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `brands`
--

LOCK TABLES `brands` WRITE;
/*!40000 ALTER TABLE `brands` DISABLE KEYS */;
INSERT INTO `brands` VALUES 
(104,'abc','to be the first brand','for testing',0,'0','2015-01-05 19:53:31',NULL,NULL,"http://localhost/tatag/advisor.php/{?brand_id,revBudget,expBudget,inflow,outflow,numMembers,totalMemberHours}","USA",206,"for-profit",9,NULL),
(105,'def','to be the second brand','for testing',0,'0','2015-01-05 19:53:33',NULL,NULL,"http://localhost/tatag/advisor.php/{?brand_id,revBudget,expBudget,inflow,outflow,numMembers,totalMemberHours}","USA",206,"for-profit",9,NULL);
/*!40000 ALTER TABLE `brands` ENABLE KEYS */;
UNLOCK TABLES;



LOCK TABLES `consumers` WRITE;
/*!40000 ALTER TABLE `consumers` DISABLE KEYS */;
INSERT INTO `consumers` VALUES 
(1,'login','login','$2y$10$fsqU/JRVmc3fXxVA3pclsO9/jyXur2RDIIlIq/5KXy1PVbsc39cc.',NULL,NOW(),NULL,NULL),
(2,'tatag-ui','ui','$2y$10$fsqU/JRVmc3fXxVA3pclsO9/jyXur2RDIIlIq/5KXy1PVbsc39cc.',NULL,NOW(),NULL,NULL);
/*!40000 ALTER TABLE `consumers` ENABLE KEYS */;
UNLOCK TABLES;



--
-- Dumping data for table `holders`
--

LOCK TABLES `holders` WRITE;
/*!40000 ALTER TABLE `holders` DISABLE KEYS */;
INSERT INTO `holders` VALUES 
(41,21,92,'cftix','2015-01-05 19:53:31',NULL,NULL,'abc',NULL),
(42,21,93,'cftix','2015-01-05 19:53:31',NULL,NULL,'abc',NULL),
(43,21,94,'ftix','2015-01-05 19:53:32',NULL,NULL,'abc',NULL),
(44,22,95,'cftix','2015-01-05 19:53:33',NULL,NULL,'abc',NULL),
(45,22,96,'cftix','2015-01-05 19:53:33',NULL,NULL,'abc',NULL);
/*!40000 ALTER TABLE `holders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `members`
--

LOCK TABLES `members` WRITE;
/*!40000 ALTER TABLE `members` DISABLE KEYS */;
INSERT INTO `members` VALUES 
(53,104,21,'admin','0','2015-01-05 19:53:31',NULL,NULL,NULL,NULL),
(54,105,22,'admin','0','2015-01-05 19:53:33',NULL,NULL,NULL,NULL),
(55,104,23,'staff','0','2015-01-05 19:53:31',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `members` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Dumping data for table `records`
--

LOCK TABLES `records` WRITE;
/*!40000 ALTER TABLE `records` DISABLE KEYS */;
INSERT INTO `records` VALUES 
(30,'np',92,21,93,21,1000.00,'first budget','2015-01-05 19:53:32',NULL,0,7,0)
,(31,'pp',93,21,94,21,35.87,'wages','2015-01-05 19:53:32',NULL,0,7,0)
,(32,'pn',94,21,92,21,2.05,'disounted employee purchase','2015-01-05 19:53:32',NULL,0,0,0)
,(33,'np',95,22,96,22,1000.00,'first budget','2015-01-05 19:53:33',NULL,0,7,0)
,(34,'pn',96,22,92,21,9.37,'first external budget use','2015-01-05 19:53:33',NULL,0,0,0)
,(35,'pn',94,21,92,21,2.05,'disounted employee purchase','2015-01-06 03:13:00',NULL,0,0,0)
,(36,'pn',94,21,92,21,2.05,'disounted employee purchase','2015-01-06 03:29:57',NULL,0,0,0);
/*!40000 ALTER TABLE `records` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Dumping data for table `throttles`
--

LOCK TABLES `throttles` WRITE;
/*!40000 ALTER TABLE `throttles` DISABLE KEYS */;
INSERT INTO `throttles` VALUES 
(1,104,172800,100,20,2,'2015-02-24 10:48:00',NULL,NULL,NULL),
(2,104,3600,10,2,1,'2015-02-24 10:48:00',NULL,NULL,NULL),
(3,104,9999999,100,20,2,'2015-02-24 10:48:00',NULL,NULL,NULL);
/*!40000 ALTER TABLE `throttles` ENABLE KEYS */;
UNLOCK TABLES;




--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES 
(21,'user21@email.org','User One','$2y$10$6AtN5uH28i6jwugnPLf3DOv1H9HzSruikFEfssr2ls0kESJLtZ1zS','2015-01-05 19:53:31',NULL,NULL,'gp',NULL,'105726759246117896959',NULL,NULL,NULL),
(22,'user22@email.org','User Two','$2y$10$fsqU/JRVmc3fXxVA3pclsO9/jyXur2RDIIlIq/5KXy1PVbsc39cc.','2015-01-05 19:53:33',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(23,'user23@email.org','User Three','$2y$10$fsqU/JRVmc3fXxVA3pclsO9/jyXur2RDIIlIq/5KXy1PVbsc39cc.','2015-01-05 19:53:33',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(24,'user24@email.org','User Four','$2y$10$fsqU/JRVmc3fXxVA3pclsO9/jyXur2RDIIlIq/5KXy1PVbsc39cc.','2015-01-05 19:53:33',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-01-06 10:03:11
