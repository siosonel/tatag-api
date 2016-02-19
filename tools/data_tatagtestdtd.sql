LOCK TABLES `accounts` WRITE;
/*!40000 ALTER TABLE `accounts` DISABLE KEYS */;
INSERT INTO `accounts` VALUES 
(92,104,'Main Revenue','cftix','hour',1399.98,-1,'2015-01-05 19:53:31','2015-11-18 04:39:17',NULL,0)
,(93,104,'Main Expense','cftix','hour',1221.82,1,'2015-01-05 19:53:31','2015-11-18 05:12:08',NULL,0)
,(94,104,'Personal Expense','ti','hour',187.53,1,'2015-01-05 19:53:32',NULL,NULL,0)
,(95,105,'Main Revenue','cftix','hour',1000.00,-1,'2015-01-05 19:53:33',NULL,NULL,0)
,(96,105,'Main Expense','cftix','hour',990.63,1,'2015-01-05 19:53:33',NULL,NULL,0)
,(97,104,'Test Expense','ftix','hour',0.00,1,'2015-01-05 19:53:33',NULL,NULL,0)
,(98,106,'Main Revenue','cftix','hour',0.00,-1,'2015-08-09 01:38:54',NULL,NULL,0)
,(99,106,'Main Expense','cftix','hour',0.00,1,'2015-08-09 01:38:54',NULL,NULL,0)
,(100,107,'Main Revenue','cftix','hour',0.00,-1,'2015-08-09 01:39:44',NULL,NULL,0)
,(101,107,'Main Expense','cftix','hour',0.00,1,'2015-08-09 01:39:44',NULL,NULL,0)
,(102,108,'Main Revenue','cftix','hour',0.00,-1,'2015-08-09 01:40:26',NULL,NULL,0)
,(103,108,'Main Expense','cftix','hour',0.00,1,'2015-08-09 01:40:26',NULL,NULL,0)
,(104,109,'Main Revenue','cftix','hour',0.00,-1,'2015-08-09 01:42:51',NULL,NULL,0)
,(105,109,'Main Expense','cftix','hour',0.00,1,'2015-08-09 01:42:51',NULL,NULL,0)
,(106,110,'Main Revenue','cftix','hour',0.00,-1,'2015-08-09 01:45:35',NULL,NULL,0)
,(107,110,'Main Expense','cftix','hour',0.00,1,'2015-08-09 01:45:35',NULL,NULL,0)
,(108,111,'Main Revenue','cftix','hour',0.00,-1,'2015-08-09 01:46:25',NULL,NULL,0)
,(109,111,'Main Expense','cftix','hour',0.00,1,'2015-08-09 01:46:25',NULL,NULL,0)
,(110,112,'Main Revenue','cftix','hour',0.00,-1,'2015-08-09 01:47:43',NULL,NULL,0)
,(111,112,'Main Expense','cftix','hour',0.00,1,'2015-08-09 01:47:43',NULL,NULL,0)
,(112,113,'Main Revenue','cftix','hour',0.00,-1,'2015-08-09 01:49:43',NULL,NULL,0)
,(113,113,'Main Expense','cftix','hour',0.00,1,'2015-08-09 01:49:43',NULL,NULL,0)
,(114,114,'Main Revenue','cftix','hour',0.00,-1,'2015-08-09 01:53:23',NULL,NULL,0)
,(115,114,'Main Expense','cftix','hour',0.00,1,'2015-08-09 01:53:23',NULL,NULL,0)
,(116,115,'Main Revenue','cftix','hour',0.00,-1,'2015-11-18 18:50:05',NULL,NULL,0)
,(117,115,'Main Expense','cftix','hour',0.00,1,'2015-11-18 18:50:05',NULL,NULL,0)
,(118,116,'Main Revenue','cftix','hour',0.00,-1,'2015-11-19 04:21:32',NULL,NULL,0)
,(119,116,'Main Expense','cftix','hour',0.00,1,'2015-11-19 04:21:32',NULL,NULL,0);
/*!40000 ALTER TABLE `accounts` ENABLE KEYS */;
UNLOCK TABLES;



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



LOCK TABLES `consumers` WRITE;
/*!40000 ALTER TABLE `consumers` DISABLE KEYS */;
INSERT INTO `consumers` VALUES 
(1,'login','login','$2y$10$fsqU/JRVmc3fXxVA3pclsO9/jyXur2RDIIlIq/5KXy1PVbsc39cc.',NULL,'2015-11-16 22:59:28',NULL,NULL,21)
,(2,'tatag-ui','ui','$2y$10$fsqU/JRVmc3fXxVA3pclsO9/jyXur2RDIIlIq/5KXy1PVbsc39cc.',NULL,'2015-11-16 22:59:28',NULL,NULL,21)
,(3,'flora','sim','$2y$10$fsqU/JRVmc3fXxVA3pclsO9/jyXur2RDIIlIq/5KXy1PVbsc39cc.',NULL,'2015-11-16 22:59:28',NULL,NULL,21)
,(4,'advisorX','advisor','$2y$10$fsqU/JRVmc3fXxVA3pclsO9/jyXur2RDIIlIq/5KXy1PVbsc39cc.',NULL,NOW(),NULL,NULL,21);
/*!40000 ALTER TABLE `consumers` ENABLE KEYS */;
UNLOCK TABLES;



LOCK TABLES `holders` WRITE;
/*!40000 ALTER TABLE `holders` DISABLE KEYS */;
INSERT INTO `holders` VALUES 
(41,21,92,'cftix','2015-01-05 19:53:31',NULL,NULL,'abc',NULL)
,(42,21,93,'cftix','2015-01-05 19:53:31','2015-12-08 02:48:17',NULL,'abc','Main Expense')
,(43,21,94,'ftix','2015-01-05 19:53:32','2015-12-11 05:29:32',NULL,'abc','personal expense')
,(44,22,95,'cftix','2015-01-05 19:53:33',NULL,NULL,'abc',NULL)
,(45,22,96,'cftix','2015-01-05 19:53:33',NULL,NULL,'abc',NULL);
/*!40000 ALTER TABLE `holders` ENABLE KEYS */;
UNLOCK TABLES;



LOCK TABLES `members` WRITE;
/*!40000 ALTER TABLE `members` DISABLE KEYS */;
INSERT INTO `members` VALUES 
(53,104,21,'admin','0','2015-01-05 19:53:31','2015-11-18 00:15:14',NULL,'2015-11-17 12:15:14',NULL)
,(54,105,22,'admin','0','2015-01-05 19:53:33',NULL,NULL,NULL,NULL)
,(55,104,23,'staff','0','2015-01-05 19:53:31',NULL,NULL,NULL,NULL)
,(56,115,21,'admin','0','2015-11-18 18:50:05',NULL,'2015-11-18 18:50:05',NULL,NULL)
,(57,116,21,'admin','0','2015-11-19 04:21:32',NULL,'2015-11-19 04:21:32',NULL,NULL);
/*!40000 ALTER TABLE `members` ENABLE KEYS */;
UNLOCK TABLES;



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



LOCK TABLES `ratings` WRITE;
/*!40000 ALTER TABLE `ratings` DISABLE KEYS */;
INSERT INTO `ratings` VALUES 
(1,106,21,95,'++customer service; --community involvement;','2015-08-09 01:38:54',NULL,NULL)
,(2,107,21,95,'++technical excellence; ','2015-08-09 01:39:44',NULL,NULL)
,(3,108,21,95,'++community involvement; -fair trade;','2015-08-09 01:40:26',NULL,NULL)
,(4,109,21,95,'++social cause','2015-08-09 01:42:51',NULL,NULL),(5,110,21,100,'++social cause;','2015-08-09 01:45:35',NULL,NULL)
,(6,111,21,0,'---social responsibility;','2015-08-09 01:46:25',NULL,NULL)
,(7,112,21,10,'--social responsibility; --climate responsibility;','2015-08-09 01:47:43',NULL,NULL)
,(8,113,21,40,'--dietary health; --environmental impact;','2015-08-09 01:49:43',NULL,NULL)
,(9,114,21,100,'++environmental impact; ++community health','2015-08-09 01:53:23',NULL,NULL)
,(10,115,21,100,'++good school;','2015-11-18 18:50:05',NULL,NULL),(11,116,21,64,'--social responsibility','2015-11-19 04:21:32',NULL,NULL);
/*!40000 ALTER TABLE `ratings` ENABLE KEYS */;
UNLOCK TABLES;



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



LOCK TABLES `relays` WRITE;
/*!40000 ALTER TABLE `relays` DISABLE KEYS */;
INSERT INTO `relays` VALUES 
(1,41,NULL,0.01,100.00,NULL,'test','2015-01-01 00:00:00',NULL,NULL,'pn',25,5,25,24)
,(2,41,NULL,5.00,10.00,NULL,'test','2015-01-01 00:00:00',NULL,NULL,'pn',25,5,25,0)
,(3,44,'xyz',0.01,50.00,NULL,'test-brand-105','2015-01-01 00:00:00',NULL,NULL,'pn',25,5,25,0)
,(4,44,'qrs',0.01,100.00,NULL,'test','2015-01-01 00:00:00',NULL,NULL,'pn',25,5,0,24)
,(5,41,'7d02',57.57,57.57,NULL,NULL,'2015-11-18 00:15:57',NULL,NULL,'pn',25,5,2,24)
,(6,41,'eb7b',57.69,57.69,NULL,NULL,'2015-11-18 00:16:09',NULL,NULL,'pn',25,5,2,24)
,(7,41,'d85b',57.71,57.71,NULL,NULL,'2015-11-18 00:16:12',NULL,NULL,'pn',25,5,2,24)
,(8,41,'77aa',57.71,57.71,NULL,NULL,'2015-11-18 00:16:12',NULL,NULL,'pn',25,5,2,24)
,(9,41,'3675',7.77,7.77,NULL,NULL,'2015-11-18 18:05:36',NULL,NULL,'pn',25,5,2,24)
,(10,41,'16085',44.44,44.44,NULL,NULL,'2015-11-18 18:11:48',NULL,NULL,'pn',25,5,2,24)
,(11,41,'b537',3.33,3.33,NULL,NULL,'2015-11-18 18:14:48',NULL,NULL,'pn',25,5,2,24)
,(12,41,'10023',2.22,2.22,NULL,NULL,'2015-11-18 18:15:31',NULL,NULL,'pn',25,5,2,24)
,(13,41,'7936',1.11,1.11,NULL,NULL,'2015-11-18 18:36:58',NULL,NULL,'pn',25,5,2,24);
/*!40000 ALTER TABLE `relays` ENABLE KEYS */;
UNLOCK TABLES;



LOCK TABLES `reversals` WRITE;
/*!40000 ALTER TABLE `reversals` DISABLE KEYS */;
INSERT INTO `reversals` VALUES 
(56,62,0.00,NULL,'pn','2015-11-21 17:48:37',NULL)
,(32,63,0.00,NULL,'pn','2015-11-21 17:57:47',NULL)
,(55,64,0.00,NULL,'pn','2015-11-21 22:10:38',NULL);
/*!40000 ALTER TABLE `reversals` ENABLE KEYS */;
UNLOCK TABLES;



LOCK TABLES `tallies` WRITE;
/*!40000 ALTER TABLE `tallies` DISABLE KEYS */;
/*!40000 ALTER TABLE `tallies` ENABLE KEYS */;
UNLOCK TABLES;



LOCK TABLES `throttles` WRITE;
/*!40000 ALTER TABLE `throttles` DISABLE KEYS */;
INSERT INTO `throttles` VALUES 
(1,104,172800,100,20,2,'2015-02-24 10:48:00',NULL,NULL,NULL)
,(2,104,3600,10,2,1,'2015-02-24 10:48:00',NULL,NULL,NULL)
,(3,104,9999999,100,20,2,'2015-02-24 10:48:00',NULL,NULL,NULL)
,(4,104,360000,11,5,1,'2015-11-18 05:16:54',NULL,NULL,NULL);
/*!40000 ALTER TABLE `throttles` ENABLE KEYS */;
UNLOCK TABLES;


LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES 
(21,'user21@email.org','User One','$2y$10$6AtN5uH28i6jwugnPLf3DOv1H9HzSruikFEfssr2ls0kESJLtZ1zS','2015-01-05 19:53:31',NULL,NULL,'gp',NULL,'105726759246117896959',NULL,2,NULL,NULL,NULL,NULL,NULL)
,(22,'user22@email.org','User Two','$2y$10$fsqU/JRVmc3fXxVA3pclsO9/jyXur2RDIIlIq/5KXy1PVbsc39cc.','2015-01-05 19:53:33',NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,NULL,NULL,NULL,NULL)
,(23,'user23@email.org','User Three','$2y$10$fsqU/JRVmc3fXxVA3pclsO9/jyXur2RDIIlIq/5KXy1PVbsc39cc.','2015-01-05 19:53:33',NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,NULL,NULL,NULL,NULL)
,(24,'user24@email.org','User Four','$2y$10$fsqU/JRVmc3fXxVA3pclsO9/jyXur2RDIIlIq/5KXy1PVbsc39cc.','2015-01-05 19:53:33',NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
