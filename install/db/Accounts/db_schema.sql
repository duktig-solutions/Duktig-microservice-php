
DROP TABLE IF EXISTS `Users`;

SET character_set_client = utf8mb4 ;

CREATE TABLE `Users` (
  `userId` varchar(30) NOT NULL,
  `roleId` varchar(10) NOT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '0',
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(100) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `displayName` varchar(100) DEFAULT NULL,
  `gender` tinyint(1) NOT NULL DEFAULT '0',
  `dob` date DEFAULT NULL,
  `photo` varchar(50) DEFAULT NULL,
  `aboutMe` text,
  `country` varchar(50) DEFAULT NULL,
  `state` varchar(150) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `zip_code` varchar(10) DEFAULT NULL,
  `address_line1` varchar(150) DEFAULT NULL,
  `address_line2` varchar(150) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `provider` varchar(20) DEFAULT NULL,
  `profileCompleteLevel` tinyint DEFAULT '0',
  `adminComments` text,
  `website` varchar(250) DEFAULT NULL,
  `notEditableFields` json DEFAULT NULL,
  `lastLoginIP` varchar(15) DEFAULT NULL,
  `dateRegistered` datetime NOT NULL,
  `dateUpdated` datetime DEFAULT NULL,
  `dateLastLogin` datetime DEFAULT NULL,
  `dateLastAction` datetime DEFAULT NULL,
  PRIMARY KEY (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Super','Admin','super.admin@duktig.dev','$2y$10$Q1DWKHd.wxw8u1NC3KSHsu.RHhRuxWUOFIR1/wgKHFHzd6qTGubuO','+37495565003','Super Administrator of System','2019-07-21 00:09:07',NULL,NULL,1,1),(2,'Regular','Admin','admin@duktig.dev','$2y$10$VbCbrKjEOSIISHtLEcWW2OecmIbGE6EDsIwmmkvetvfJyIY2tkRji','+37495565003','Regular Administrator of System','2019-07-20 23:52:03',NULL,NULL,2,1),(3,'Service','Provider','service.provider@duktig.dev','$2y$10$10/BvGcnuUsNK1TuIDu/zuppeGgxueecgzRVKjjsI1gRD/jGhi.4i','+37495565003','Service provider test account','2019-07-21 00:11:10',NULL,NULL,3,1),(4,'Client','User','client@duktig.dev','$2y$10$/VAIP5Cv1ejkxWTcIgEl2ucdkKZPJHxAaez8RG/DuobpgTEBxWPam','+37495565003','Client test account','2019-07-21 00:11:26','2019-07-21 10:33:53','2019-07-21 10:33:53',4,1),(5,'Developer','Testing','developer@duktig.dev','$2y$10$9mv/pWfPWemZXXijjr0z9Oxqg20EyBa/qODUcqni39UoCvkz0nU/6','+37495565003',NULL,'2019-07-21 11:38:44',NULL,NULL,5,0);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

CREATE TABLE `userActions` (
  `actionId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `dateAction` datetime NOT NULL,
  `actionMessage` text,
  `actionCode` varchar(255) NOT NULL,
  PRIMARY KEY (`actionId`),
  KEY `userId` (`userId`),
  KEY `dateAction` (`dateAction`),
  KEY `userByDate` (`userId`,`dateAction`),
  KEY `actionCode` (`actionCode`),
  KEY `actionCodeByDate` (`actionCode`,`dateAction`),
  KEY `actionCodeByUserId` (`actionCode`,`userId`),
  KEY `actionCodeByUserIdDate` (`userId`,`dateAction`,`actionCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `statistics` (
  `statId` VARCHAR(100) NOT NULL,
  `dateLastUpdate` DATETIME NOT NULL,
  `statisticsJson` JSON NOT NULL,
  PRIMARY KEY (`statId`),
  INDEX `dateLastUpdate` (`dateLastUpdate` ASC)
);


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-07-21 11:40:40
