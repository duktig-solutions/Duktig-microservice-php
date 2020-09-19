-- MySQL dump 10.13  Distrib 8.0.16, for macos10.14 (x86_64)
--
-- Host: localhost    Database: Duktig
-- ------------------------------------------------------
-- Server version	5.7.18

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
 SET NAMES utf8 ;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `users` (
  `userId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `firstName` varchar(15) DEFAULT NULL,
  `lastName` varchar(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `phone` varchar(25) DEFAULT NULL,
  `comment` text,
  `dateRegistered` datetime DEFAULT NULL,
  `dateLastUpdate` datetime DEFAULT NULL,
  `dateLastLogin` datetime DEFAULT NULL,
  `roleId` int(2) unsigned DEFAULT '1',
  `status` int(2) unsigned DEFAULT '0',
  PRIMARY KEY (`userId`),
  UNIQUE KEY `email` (`email`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
