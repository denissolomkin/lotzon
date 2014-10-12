-- MySQL dump 10.13  Distrib 5.5.37, for Linux (x86_64)
--
-- Host: localhost    Database: lotzone
-- ------------------------------------------------------
-- Server version	5.5.37

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
-- Table structure for table `Admins`
--

DROP TABLE IF EXISTS `Admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Admins` (
  `Login` varchar(50) NOT NULL DEFAULT '',
  `Password` varchar(32) NOT NULL DEFAULT '',
  `Salt` varchar(13) NOT NULL DEFAULT '',
  `LastLogin` int(11) NOT NULL DEFAULT '0',
  `LastLoginIP` varchar(255) NOT NULL DEFAULT '',
  `Role` enum('ADMIN','MANAGER') NOT NULL DEFAULT 'MANAGER',
  PRIMARY KEY (`Login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Admins`
--

LOCK TABLES `Admins` WRITE;
/*!40000 ALTER TABLE `Admins` DISABLE KEYS */;
INSERT INTO `Admins` VALUES ('rav','7e43742ba2a5f674b7df938d75e91242','5404eb6757dd0',1413150777,'127.0.0.1','ADMIN');
/*!40000 ALTER TABLE `Admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `EmailInvites`
--

DROP TABLE IF EXISTS `EmailInvites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `EmailInvites` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Email` varchar(255) NOT NULL DEFAULT '',
  `InviterId` int(11) NOT NULL DEFAULT '0',
  `Date` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `idx_Email` (`Email`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `EmailInvites`
--

LOCK TABLES `EmailInvites` WRITE;
/*!40000 ALTER TABLE `EmailInvites` DISABLE KEYS */;
INSERT INTO `EmailInvites` VALUES (1,'sdfsdf@kuku.ru',21,1413142600),(3,'test2@test.com',21,1413142892),(4,'test3@gmail.com',21,1413142925),(5,'test4@test.com',21,1413142936),(7,'button3@test.com',21,1413142949),(8,'button4@test.com',21,1413142951),(9,'button5@test.com',21,1413142954),(10,'button6@test.com',21,1413142958),(11,'6ke6rn6@gmail.com',25,1413143971),(12,'test@test.com',25,1413144089),(13,'button@test.com',25,1413144138);
/*!40000 ALTER TABLE `EmailInvites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `GamesSettings`
--

DROP TABLE IF EXISTS `GamesSettings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `GamesSettings` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `StartTime` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `GamesSettings`
--

LOCK TABLES `GamesSettings` WRITE;
/*!40000 ALTER TABLE `GamesSettings` DISABLE KEYS */;
INSERT INTO `GamesSettings` VALUES (1,54960);
/*!40000 ALTER TABLE `GamesSettings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Lotteries`
--

DROP TABLE IF EXISTS `Lotteries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Lotteries` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Date` int(11) NOT NULL DEFAULT '0',
  `Combination` text,
  `WinnersCount` int(11) NOT NULL DEFAULT '0',
  `MoneyTotal` int(11) NOT NULL DEFAULT '0',
  `PointsTotal` int(11) NOT NULL DEFAULT '0',
  `Ready` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  KEY `idx_Date` (`Date`)
) ENGINE=InnoDB AUTO_INCREMENT=138 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Lotteries`
--

LOCK TABLES `Lotteries` WRITE;
/*!40000 ALTER TABLE `Lotteries` DISABLE KEYS */;
INSERT INTO `Lotteries` VALUES (23,1412207035,'a:6:{i:0;i:8;i:1;i:20;i:2;i:40;i:3;i:2;i:4;i:13;i:5;i:42;}',1,0,36,1),(24,1412207036,'a:6:{i:0;i:44;i:1;i:20;i:2;i:17;i:3;i:8;i:4;i:26;i:5;i:28;}',1,0,59,1),(25,1412207036,'a:6:{i:0;i:10;i:1;i:37;i:2;i:27;i:3;i:47;i:4;i:2;i:5;i:41;}',1,0,12,1),(26,1412207037,'a:6:{i:0;i:7;i:1;i:46;i:2;i:43;i:3;i:23;i:4;i:36;i:5;i:35;}',1,0,47,1),(27,1412207037,'a:6:{i:0;i:12;i:1;i:25;i:2;i:16;i:3;i:32;i:4;i:29;i:5;i:10;}',1,0,59,1),(28,1412207038,'a:6:{i:0;i:36;i:1;i:1;i:2;i:39;i:3;i:17;i:4;i:13;i:5;i:14;}',1,0,59,1),(29,1412207038,'a:6:{i:0;i:7;i:1;i:47;i:2;i:31;i:3;i:18;i:4;i:2;i:5;i:17;}',1,0,36,1),(30,1412207039,'a:6:{i:0;i:36;i:1;i:40;i:2;i:34;i:3;i:35;i:4;i:27;i:5;i:44;}',0,0,0,1),(31,1412207039,'a:6:{i:0;i:42;i:1;i:29;i:2;i:44;i:3;i:45;i:4;i:12;i:5;i:24;}',1,0,24,1),(32,1412207039,'a:6:{i:0;i:20;i:1;i:49;i:2;i:21;i:3;i:19;i:4;i:30;i:5;i:4;}',1,0,23,1),(33,1412207040,'a:6:{i:0;i:15;i:1;i:32;i:2;i:6;i:3;i:18;i:4;i:25;i:5;i:23;}',1,0,60,1),(34,1412207040,'a:6:{i:0;i:2;i:1;i:15;i:2;i:41;i:3;i:24;i:4;i:13;i:5;i:11;}',1,0,47,1),(35,1412207041,'a:6:{i:0;i:42;i:1;i:44;i:2;i:45;i:3;i:6;i:4;i:20;i:5;i:21;}',1,0,23,1),(36,1412207041,'a:6:{i:0;i:43;i:1;i:13;i:2;i:25;i:3;i:2;i:4;i:33;i:5;i:4;}',1,0,70,1),(37,1412207042,'a:6:{i:0;i:13;i:1;i:33;i:2;i:38;i:3;i:12;i:4;i:36;i:5;i:47;}',1,0,81,1),(38,1412207042,'a:6:{i:0;i:40;i:1;i:9;i:2;i:35;i:3;i:28;i:4;i:26;i:5;i:17;}',1,0,58,1),(39,1412207042,'a:6:{i:0;i:17;i:1;i:49;i:2;i:40;i:3;i:13;i:4;i:3;i:5;i:2;}',1,0,36,1),(40,1412207043,'a:6:{i:0;i:28;i:1;i:36;i:2;i:49;i:3;i:44;i:4;i:25;i:5;i:24;}',1,0,23,1),(41,1412207043,'a:6:{i:0;i:44;i:1;i:2;i:2;i:33;i:3;i:6;i:4;i:29;i:5;i:36;}',1,0,36,1),(42,1412207043,'a:6:{i:0;i:48;i:1;i:21;i:2;i:45;i:3;i:38;i:4;i:39;i:5;i:10;}',1,0,35,1),(43,1412207043,'a:6:{i:0;i:1;i:1;i:19;i:2;i:45;i:3;i:7;i:4;i:37;i:5;i:46;}',1,0,36,1),(44,1412207044,'a:6:{i:0;i:28;i:1;i:13;i:2;i:14;i:3;i:2;i:4;i:18;i:5;i:17;}',1,0,82,1),(45,1412207044,'a:6:{i:0;i:29;i:1;i:36;i:2;i:15;i:3;i:27;i:4;i:18;i:5;i:14;}',1,0,36,1),(46,1412207044,'a:6:{i:0;i:20;i:1;i:5;i:2;i:7;i:3;i:36;i:4;i:40;i:5;i:45;}',1,0,12,1),(47,1412207044,'a:6:{i:0;i:6;i:1;i:7;i:2;i:24;i:3;i:4;i:4;i:33;i:5;i:8;}',1,0,36,1),(48,1412207045,'a:6:{i:0;i:40;i:1;i:45;i:2;i:36;i:3;i:42;i:4;i:31;i:5;i:22;}',1,0,12,1),(49,1412207045,'a:6:{i:0;i:8;i:1;i:35;i:2;i:14;i:3;i:1;i:4;i:11;i:5;i:16;}',1,0,47,1),(50,1412207045,'a:6:{i:0;i:28;i:1;i:7;i:2;i:27;i:3;i:49;i:4;i:21;i:5;i:9;}',1,0,24,1),(51,1412207045,'a:6:{i:0;i:44;i:1;i:33;i:2;i:21;i:3;i:15;i:4;i:40;i:5;i:14;}',1,0,47,1),(52,1412207046,'a:6:{i:0;i:6;i:1;i:18;i:2;i:40;i:3;i:35;i:4;i:4;i:5;i:45;}',1,0,24,1),(53,1412207046,'a:6:{i:0;i:8;i:1;i:28;i:2;i:42;i:3;i:49;i:4;i:31;i:5;i:20;}',1,0,36,1),(54,1412207046,'a:6:{i:0;i:4;i:1;i:1;i:2;i:2;i:3;i:31;i:4;i:11;i:5;i:20;}',1,0,35,1),(55,1412207046,'a:6:{i:0;i:10;i:1;i:7;i:2;i:31;i:3;i:5;i:4;i:33;i:5;i:49;}',1,0,36,1),(56,1412207046,'a:6:{i:0;i:47;i:1;i:1;i:2;i:17;i:3;i:4;i:4;i:41;i:5;i:9;}',1,0,35,1),(57,1412207049,'a:6:{i:0;i:37;i:1;i:8;i:2;i:13;i:3;i:36;i:4;i:46;i:5;i:3;}',1,0,47,1),(58,1412207298,'a:6:{i:0;i:18;i:1;i:2;i:2;i:23;i:3;i:12;i:4;i:9;i:5;i:11;}',1,0,70,1),(59,1412207415,'a:6:{i:0;i:36;i:1;i:6;i:2;i:2;i:3;i:29;i:4;i:46;i:5;i:44;}',0,0,0,1),(60,1412207415,'a:6:{i:0;i:36;i:1;i:28;i:2;i:4;i:3;i:14;i:4;i:6;i:5;i:21;}',0,0,0,1),(61,1412207416,'a:6:{i:0;i:36;i:1;i:14;i:2;i:10;i:3;i:20;i:4;i:19;i:5;i:23;}',0,0,0,1),(62,1412207417,'a:6:{i:0;i:6;i:1;i:44;i:2;i:22;i:3;i:26;i:4;i:9;i:5;i:19;}',0,0,0,1),(63,1412208277,'a:6:{i:0;i:28;i:1;i:17;i:2;i:38;i:3;i:30;i:4;i:6;i:5;i:35;}',1,0,48,1),(64,1412208302,'a:6:{i:0;i:38;i:1;i:17;i:2;i:22;i:3;i:6;i:4;i:43;i:5;i:31;}',0,0,0,1),(65,1412208361,'a:6:{i:0;i:35;i:1;i:32;i:2;i:17;i:3;i:42;i:4;i:15;i:5;i:2;}',0,0,0,1),(66,1412208372,'a:6:{i:0;i:25;i:1;i:47;i:2;i:1;i:3;i:26;i:4;i:15;i:5;i:41;}',0,0,0,1),(67,1412208421,'a:6:{i:0;i:45;i:1;i:8;i:2;i:28;i:3;i:2;i:4;i:34;i:5;i:15;}',1,0,48,1),(68,1412510402,'a:6:{i:0;i:45;i:1;i:24;i:2;i:20;i:3;i:4;i:4;i:12;i:5;i:32;}',1,0,35,1),(69,1412522102,'a:6:{i:0;i:37;i:1;i:30;i:2;i:7;i:3;i:8;i:4;i:46;i:5;i:32;}',1,0,12,1),(70,1412522402,'a:6:{i:0;i:10;i:1;i:45;i:2;i:17;i:3;i:31;i:4;i:16;i:5;i:49;}',0,0,0,1),(71,1412522463,'a:6:{i:0;i:17;i:1;i:26;i:2;i:2;i:3;i:31;i:4;i:43;i:5;i:49;}',0,0,0,1),(72,1412522762,'a:6:{i:0;i:12;i:1;i:26;i:2;i:5;i:3;i:15;i:4;i:6;i:5;i:4;}',0,0,0,1),(73,1412522821,'a:6:{i:0;i:26;i:1;i:38;i:2;i:33;i:3;i:7;i:4;i:27;i:5;i:6;}',0,0,0,1),(74,1412523182,'a:6:{i:0;i:21;i:1;i:36;i:2;i:16;i:3;i:23;i:4;i:13;i:5;i:38;}',0,0,0,1),(75,1412523241,'a:6:{i:0;i:46;i:1;i:35;i:2;i:24;i:3;i:34;i:4;i:27;i:5;i:49;}',0,0,0,1),(76,1412523362,'a:6:{i:0;i:24;i:1;i:45;i:2;i:22;i:3;i:26;i:4;i:34;i:5;i:38;}',0,0,0,1),(77,1412523782,'a:6:{i:0;i:18;i:1;i:13;i:2;i:31;i:3;i:36;i:4;i:25;i:5;i:42;}',1,0,35,1),(78,1412523842,'a:6:{i:0;i:44;i:1;i:32;i:2;i:10;i:3;i:6;i:4;i:31;i:5;i:26;}',1,0,59,1),(79,1412524381,'a:6:{i:0;i:38;i:1;i:46;i:2;i:14;i:3;i:9;i:4;i:44;i:5;i:12;}',0,0,0,1),(80,1412524441,'a:6:{i:0;i:41;i:1;i:37;i:2;i:36;i:3;i:30;i:4;i:18;i:5;i:11;}',0,0,0,1),(81,1412524681,'a:6:{i:0;i:12;i:1;i:28;i:2;i:16;i:3;i:34;i:4;i:6;i:5;i:7;}',0,0,0,1),(82,1412524802,'a:6:{i:0;i:39;i:1;i:17;i:2;i:4;i:3;i:21;i:4;i:42;i:5;i:3;}',0,0,0,1),(83,1412525042,'a:6:{i:0;i:5;i:1;i:11;i:2;i:22;i:3;i:17;i:4;i:12;i:5;i:37;}',1,0,23,1),(84,1412525101,'a:6:{i:0;i:35;i:1;i:39;i:2;i:31;i:3;i:25;i:4;i:38;i:5;i:41;}',1,0,12,1),(85,1412525161,'a:6:{i:0;i:3;i:1;i:21;i:2;i:1;i:3;i:14;i:4;i:32;i:5;i:12;}',1,0,12,1),(86,1412525222,'a:6:{i:0;i:3;i:1;i:46;i:2;i:40;i:3;i:8;i:4;i:49;i:5;i:43;}',0,0,0,1),(87,1412525942,'a:6:{i:0;i:41;i:1;i:19;i:2;i:36;i:3;i:23;i:4;i:43;i:5;i:34;}',1,0,47,1),(88,1412527441,'a:6:{i:0;i:48;i:1;i:30;i:2;i:40;i:3;i:49;i:4;i:7;i:5;i:28;}',1,0,12,1),(89,1412530741,'a:6:{i:0;i:5;i:1;i:3;i:2;i:17;i:3;i:25;i:4;i:21;i:5;i:39;}',1,0,69,1),(90,1412530861,'a:6:{i:0;i:21;i:1;i:20;i:2;i:25;i:3;i:9;i:4;i:2;i:5;i:4;}',1,0,59,1),(91,1412531161,'a:6:{i:0;i:1;i:1;i:17;i:2;i:30;i:3;i:42;i:4;i:19;i:5;i:24;}',0,0,0,1),(92,1412531821,'a:6:{i:0;i:22;i:1;i:11;i:2;i:7;i:3;i:39;i:4;i:49;i:5;i:37;}',1,0,35,1),(93,1412532541,'a:6:{i:0;i:13;i:1;i:11;i:2;i:22;i:3;i:14;i:4;i:37;i:5;i:18;}',1,0,48,1),(94,1412532781,'a:6:{i:0;i:47;i:1;i:18;i:2;i:34;i:3;i:2;i:4;i:5;i:5;i:19;}',0,0,0,1),(95,1412532901,'a:6:{i:0;i:2;i:1;i:42;i:2;i:29;i:3;i:22;i:4;i:21;i:5;i:24;}',1,0,59,1),(96,1412538661,'a:6:{i:0;i:8;i:1;i:14;i:2;i:25;i:3;i:49;i:4;i:28;i:5;i:45;}',1,0,47,1),(97,1412540281,'a:6:{i:0;i:36;i:1;i:22;i:2;i:41;i:3;i:15;i:4;i:32;i:5;i:24;}',1,0,82,1),(98,1412540762,'a:6:{i:0;i:20;i:1;i:2;i:2;i:36;i:3;i:28;i:4;i:47;i:5;i:46;}',1,0,24,1),(99,1412542502,'a:6:{i:0;i:12;i:1;i:13;i:2;i:19;i:3;i:31;i:4;i:46;i:5;i:24;}',1,0,46,1),(100,1412543402,'a:6:{i:0;i:17;i:1;i:36;i:2;i:3;i:3;i:30;i:4;i:48;i:5;i:44;}',1,0,35,1),(101,1412550181,'a:6:{i:0;i:9;i:1;i:30;i:2;i:37;i:3;i:35;i:4;i:19;i:5;i:7;}',1,0,36,1),(102,1412550301,'a:6:{i:0;i:47;i:1;i:37;i:2;i:41;i:3;i:29;i:4;i:3;i:5;i:44;}',1,0,59,1),(103,1412550379,'a:6:{i:0;i:3;i:1;i:18;i:2;i:12;i:3;i:13;i:4;i:8;i:5;i:22;}',0,0,0,1),(104,1412550388,'a:6:{i:0;i:19;i:1;i:32;i:2;i:4;i:3;i:28;i:4;i:24;i:5;i:8;}',0,0,0,1),(105,1412550388,'a:6:{i:0;i:19;i:1;i:41;i:2;i:40;i:3;i:45;i:4;i:28;i:5;i:10;}',0,0,0,1),(106,1412550393,'a:6:{i:0;i:18;i:1;i:14;i:2;i:35;i:3;i:5;i:4;i:47;i:5;i:27;}',0,0,0,1),(107,1412550452,'a:6:{i:0;i:42;i:1;i:40;i:2;i:19;i:3;i:26;i:4;i:33;i:5;i:8;}',1,0,47,1),(108,1412550493,'a:6:{i:0;i:22;i:1;i:37;i:2;i:44;i:3;i:33;i:4;i:28;i:5;i:23;}',1,0,36,1),(109,1412550553,'a:6:{i:0;i:27;i:1;i:21;i:2;i:28;i:3;i:11;i:4;i:20;i:5;i:40;}',1,0,36,1),(110,1412554083,'a:6:{i:0;i:31;i:1;i:23;i:2;i:21;i:3;i:42;i:4;i:40;i:5;i:22;}',1,0,35,1),(111,1412554085,'a:6:{i:0;i:35;i:1;i:14;i:2;i:9;i:3;i:34;i:4;i:7;i:5;i:47;}',0,0,0,1),(112,1412554141,'a:6:{i:0;i:24;i:1;i:39;i:2;i:12;i:3;i:46;i:4;i:47;i:5;i:49;}',0,0,0,1),(113,1412554202,'a:6:{i:0;i:49;i:1;i:34;i:2;i:13;i:3;i:12;i:4;i:28;i:5;i:43;}',0,0,0,1),(114,1412554262,'a:6:{i:0;i:38;i:1;i:30;i:2;i:28;i:3;i:37;i:4;i:14;i:5;i:7;}',0,0,0,1),(115,1412554321,'a:6:{i:0;i:37;i:1;i:47;i:2;i:25;i:3;i:49;i:4;i:29;i:5;i:5;}',0,0,0,1),(116,1412554382,'a:6:{i:0;i:6;i:1;i:12;i:2;i:21;i:3;i:41;i:4;i:35;i:5;i:38;}',0,0,0,1),(117,1412554442,'a:6:{i:0;i:45;i:1;i:30;i:2;i:39;i:3;i:10;i:4;i:16;i:5;i:34;}',0,0,0,1),(118,1412554501,'a:6:{i:0;i:25;i:1;i:15;i:2;i:14;i:3;i:38;i:4;i:21;i:5;i:9;}',0,0,0,1),(119,1412708583,'a:6:{i:0;i:26;i:1;i:8;i:2;i:1;i:3;i:10;i:4;i:46;i:5;i:38;}',0,0,0,1),(120,1412884382,'a:6:{i:0;i:22;i:1;i:47;i:2;i:10;i:3;i:24;i:4;i:12;i:5;i:13;}',1,0,70,1),(121,1412884441,'a:6:{i:0;i:38;i:1;i:39;i:2;i:16;i:3;i:35;i:4;i:7;i:5;i:33;}',1,0,10,1),(122,1413109131,'a:6:{i:0;i:12;i:1;i:21;i:2;i:18;i:3;i:4;i:4;i:44;i:5;i:30;}',0,0,20,1),(123,1413109261,'a:6:{i:0;i:17;i:1;i:45;i:2;i:38;i:3;i:43;i:4;i:13;i:5;i:23;}',0,0,40,1),(124,1413109502,'a:6:{i:0;i:6;i:1;i:24;i:2;i:35;i:3;i:21;i:4;i:42;i:5;i:18;}',0,0,0,1),(125,1413109562,'a:6:{i:0;i:37;i:1;i:27;i:2;i:10;i:3;i:49;i:4;i:47;i:5;i:14;}',0,0,20,1),(126,1413109742,'a:6:{i:0;i:4;i:1;i:25;i:2;i:30;i:3;i:22;i:4;i:6;i:5;i:16;}',0,0,30,1),(127,1413110041,'a:6:{i:0;i:42;i:1;i:36;i:2;i:5;i:3;i:17;i:4;i:4;i:5;i:48;}',0,0,10,1),(128,1413112081,'a:6:{i:0;i:29;i:1;i:46;i:2;i:38;i:3;i:37;i:4;i:49;i:5;i:48;}',0,0,10,1),(129,1413112202,'a:6:{i:0;i:22;i:1;i:48;i:2;i:41;i:3;i:28;i:4;i:4;i:5;i:5;}',0,0,60,1),(130,1413112441,'a:6:{i:0;i:15;i:1;i:43;i:2;i:5;i:3;i:19;i:4;i:36;i:5;i:6;}',0,0,30,1),(131,1413112622,'a:6:{i:0;i:22;i:1;i:19;i:2;i:45;i:3;i:9;i:4;i:24;i:5;i:32;}',0,0,40,1),(132,1413113101,'a:6:{i:0;i:8;i:1;i:38;i:2;i:17;i:3;i:3;i:4;i:7;i:5;i:15;}',0,0,40,1),(133,1413113221,'a:6:{i:0;i:47;i:1;i:17;i:2;i:32;i:3;i:22;i:4;i:24;i:5;i:25;}',0,0,60,1),(134,1413113641,'a:6:{i:0;i:43;i:1;i:33;i:2;i:18;i:3;i:17;i:4;i:14;i:5;i:36;}',0,0,50,1),(135,1413117602,'a:6:{i:0;i:7;i:1;i:24;i:2;i:9;i:3;i:41;i:4;i:1;i:5;i:16;}',1,60,0,1),(136,1413123242,'a:6:{i:0;i:1;i:1;i:40;i:2;i:10;i:3;i:5;i:4;i:45;i:5;i:46;}',2,120,0,1),(137,1413126961,'a:6:{i:0;i:42;i:1;i:13;i:2;i:15;i:3;i:16;i:4;i:31;i:5;i:18;}',1,20,0,1);
/*!40000 ALTER TABLE `Lotteries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `LotterySettings`
--

DROP TABLE IF EXISTS `LotterySettings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `LotterySettings` (
  `BallsCount` tinyint(1) NOT NULL DEFAULT '1',
  `CountryCode` varchar(2) NOT NULL DEFAULT 'UA',
  `Prize` int(11) NOT NULL DEFAULT '0',
  `Currency` enum('POINT','MONEY') NOT NULL DEFAULT 'POINT',
  `SumTotal` int(11) NOT NULL DEFAULT '0',
  `JackPot` tinyint(1) NOT NULL DEFAULT '0',
  `Coefficient` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`BallsCount`,`CountryCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `LotterySettings`
--

LOCK TABLES `LotterySettings` WRITE;
/*!40000 ALTER TABLE `LotterySettings` DISABLE KEYS */;
INSERT INTO `LotterySettings` VALUES (1,'BY',0,'POINT',10000,1,0.6),(1,'RU',10,'POINT',10000,1,0.3),(1,'UA',10,'MONEY',10000,1,1),(2,'BY',0,'POINT',10000,1,0.6),(2,'RU',230,'POINT',10000,1,0.3),(2,'UA',20,'MONEY',10000,1,1),(3,'BY',0,'POINT',10000,1,0.6),(3,'RU',300,'POINT',10000,1,0.3),(3,'UA',30,'MONEY',10000,1,1),(4,'BY',0,'MONEY',10000,1,0.6),(4,'RU',500,'MONEY',10000,1,0.3),(4,'UA',40,'MONEY',10000,1,1),(5,'BY',0,'MONEY',10000,1,0.6),(5,'RU',660,'MONEY',10000,1,0.3),(5,'UA',50000,'MONEY',10000,1,1),(6,'BY',0,'MONEY',10000,1,0.6),(6,'RU',666,'MONEY',10000,1,0.3),(6,'UA',60,'MONEY',10000,1,1);
/*!40000 ALTER TABLE `LotterySettings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `LotteryTickets`
--

DROP TABLE IF EXISTS `LotteryTickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `LotteryTickets` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `LotteryId` int(11) NOT NULL DEFAULT '0',
  `PlayerId` int(11) NOT NULL DEFAULT '0',
  `Combination` text NOT NULL,
  `DateCreated` int(11) NOT NULL DEFAULT '0',
  `TicketNum` tinyint(1) NOT NULL DEFAULT '1',
  `TicketWin` int(11) NOT NULL DEFAULT '0',
  `TicketWinCurrency` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`),
  KEY `idx_LotteryId` (`LotteryId`),
  KEY `idx_PlayerId` (`PlayerId`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `LotteryTickets`
--

LOCK TABLES `LotteryTickets` WRITE;
/*!40000 ALTER TABLE `LotteryTickets` DISABLE KEYS */;
INSERT INTO `LotteryTickets` VALUES (1,122,21,'a:6:{i:0;s:1:\"6\";i:1;s:2:\"15\";i:2;s:2:\"34\";i:3;s:2:\"36\";i:4;s:2:\"45\";i:5;s:2:\"47\";}',1413105320,1,0,''),(2,122,21,'a:6:{i:0;s:1:\"4\";i:1;s:1:\"7\";i:2;s:2:\"15\";i:3;s:2:\"21\";i:4;s:2:\"35\";i:5;s:2:\"42\";}',1413105329,3,20,'POINT'),(3,122,21,'a:6:{i:0;s:2:\"24\";i:1;s:2:\"25\";i:2;s:2:\"32\";i:3;s:2:\"37\";i:4;s:2:\"41\";i:5;s:2:\"48\";}',1413105333,5,0,''),(4,122,21,'a:6:{i:0;s:1:\"2\";i:1;s:1:\"5\";i:2;s:1:\"7\";i:3;s:1:\"8\";i:4;s:2:\"27\";i:5;s:2:\"43\";}',1413109029,2,0,''),(5,122,21,'a:6:{i:0;s:1:\"3\";i:1;s:1:\"7\";i:2;s:1:\"8\";i:3;s:2:\"13\";i:4;s:2:\"24\";i:5;s:2:\"35\";}',1413109032,4,0,''),(6,123,21,'a:6:{i:0;s:2:\"12\";i:1;s:2:\"25\";i:2;s:2:\"29\";i:3;s:2:\"34\";i:4;s:2:\"48\";i:5;s:2:\"49\";}',1413109204,1,0,''),(7,123,21,'a:6:{i:0;s:1:\"9\";i:1;s:2:\"13\";i:2;s:2:\"19\";i:3;s:2:\"26\";i:4;s:2:\"39\";i:5;s:2:\"45\";}',1413109206,3,20,'POINT'),(8,123,21,'a:6:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"8\";i:3;s:2:\"23\";i:4;s:2:\"27\";i:5;s:2:\"43\";}',1413109209,5,20,'POINT'),(9,125,21,'a:6:{i:0;s:1:\"6\";i:1;s:2:\"19\";i:2;s:2:\"20\";i:3;s:2:\"22\";i:4;s:2:\"42\";i:5;s:2:\"45\";}',1413109515,1,0,''),(10,125,21,'a:6:{i:0;s:1:\"5\";i:1;s:2:\"20\";i:2;s:2:\"29\";i:3;s:2:\"34\";i:4;s:2:\"45\";i:5;s:2:\"48\";}',1413109518,2,0,''),(11,125,21,'a:6:{i:0;s:2:\"12\";i:1;s:2:\"15\";i:2;s:2:\"17\";i:3;s:2:\"18\";i:4;s:2:\"36\";i:5;s:2:\"37\";}',1413109521,3,10,'POINT'),(12,125,21,'a:6:{i:0;s:1:\"5\";i:1;s:2:\"24\";i:2;s:2:\"35\";i:3;s:2:\"42\";i:4;s:2:\"45\";i:5;s:2:\"49\";}',1413109524,4,10,'POINT'),(13,125,21,'a:6:{i:0;s:1:\"2\";i:1;s:2:\"12\";i:2;s:2:\"17\";i:3;s:2:\"22\";i:4;s:2:\"32\";i:5;s:2:\"34\";}',1413109527,5,0,''),(14,126,21,'a:6:{i:0;s:2:\"20\";i:1;s:2:\"24\";i:2;s:2:\"26\";i:3;s:2:\"34\";i:4;s:2:\"42\";i:5;s:2:\"46\";}',1413109591,1,0,''),(15,126,21,'a:6:{i:0;s:1:\"3\";i:1;s:2:\"11\";i:2;s:2:\"16\";i:3;s:2:\"19\";i:4;s:2:\"25\";i:5;s:2:\"32\";}',1413109594,3,20,'POINT'),(16,126,21,'a:6:{i:0;s:1:\"4\";i:1;s:2:\"12\";i:2;s:2:\"18\";i:3;s:2:\"20\";i:4;s:2:\"41\";i:5;s:2:\"45\";}',1413109596,5,10,'POINT'),(17,127,21,'a:6:{i:0;s:2:\"18\";i:1;s:2:\"26\";i:2;s:2:\"33\";i:3;s:2:\"35\";i:4;s:2:\"37\";i:5;s:2:\"44\";}',1413109970,1,0,''),(18,127,21,'a:6:{i:0;s:2:\"24\";i:1;s:2:\"26\";i:2;s:2:\"34\";i:3;s:2:\"38\";i:4;s:2:\"39\";i:5;s:2:\"43\";}',1413109973,3,0,''),(19,127,21,'a:6:{i:0;s:1:\"4\";i:1;s:2:\"16\";i:2;s:2:\"29\";i:3;s:2:\"31\";i:4;s:2:\"40\";i:5;s:2:\"47\";}',1413109976,5,10,'POINT'),(20,128,21,'a:6:{i:0;s:2:\"11\";i:1;s:2:\"18\";i:2;s:2:\"21\";i:3;s:2:\"22\";i:4;s:2:\"28\";i:5;s:2:\"39\";}',1413112034,1,0,''),(21,128,21,'a:6:{i:0;s:1:\"9\";i:1;s:2:\"19\";i:2;s:2:\"24\";i:3;s:2:\"28\";i:4;s:2:\"31\";i:5;s:2:\"35\";}',1413112038,3,0,''),(22,128,21,'a:6:{i:0;s:1:\"2\";i:1;s:1:\"3\";i:2;s:2:\"18\";i:3;s:2:\"25\";i:4;s:2:\"42\";i:5;s:2:\"49\";}',1413112041,5,10,'POINT'),(23,129,21,'a:6:{i:0;s:1:\"2\";i:1;s:1:\"5\";i:2;s:2:\"21\";i:3;s:2:\"22\";i:4;s:2:\"33\";i:5;s:2:\"39\";}',1413112148,1,20,'POINT'),(24,129,21,'a:6:{i:0;s:1:\"1\";i:1;s:2:\"29\";i:2;s:2:\"30\";i:3;s:2:\"37\";i:4;s:2:\"38\";i:5;s:2:\"46\";}',1413112151,2,0,''),(25,129,21,'a:6:{i:0;s:1:\"5\";i:1;s:1:\"7\";i:2;s:1:\"8\";i:3;s:2:\"20\";i:4;s:2:\"22\";i:5;s:2:\"43\";}',1413112154,3,20,'POINT'),(26,129,21,'a:6:{i:0;s:1:\"4\";i:1;s:1:\"7\";i:2;s:2:\"21\";i:3;s:2:\"26\";i:4;s:2:\"32\";i:5;s:2:\"48\";}',1413112157,4,20,'POINT'),(27,129,21,'a:6:{i:0;s:1:\"6\";i:1;s:1:\"8\";i:2;s:2:\"15\";i:3;s:2:\"29\";i:4;s:2:\"39\";i:5;s:2:\"44\";}',1413112161,5,0,''),(28,130,21,'a:6:{i:0;s:1:\"3\";i:1;s:1:\"9\";i:2;s:2:\"13\";i:3;s:2:\"32\";i:4;s:2:\"33\";i:5;s:2:\"42\";}',1413112405,1,0,''),(29,130,21,'a:6:{i:0;s:1:\"3\";i:1;s:1:\"5\";i:2;s:2:\"19\";i:3;s:2:\"26\";i:4;s:2:\"33\";i:5;s:2:\"38\";}',1413112408,2,20,'POINT'),(30,130,21,'a:6:{i:0;s:1:\"2\";i:1;s:1:\"3\";i:2;s:2:\"15\";i:3;s:2:\"17\";i:4;s:2:\"26\";i:5;s:2:\"42\";}',1413112411,3,10,'POINT'),(31,130,21,'a:6:{i:0;s:2:\"14\";i:1;s:2:\"28\";i:2;s:2:\"29\";i:3;s:2:\"37\";i:4;s:2:\"45\";i:5;s:2:\"47\";}',1413112413,4,0,''),(32,130,21,'a:6:{i:0;s:2:\"13\";i:1;s:2:\"24\";i:2;s:2:\"27\";i:3;s:2:\"38\";i:4;s:2:\"40\";i:5;s:2:\"42\";}',1413112416,5,0,''),(33,131,21,'a:6:{i:0;s:2:\"13\";i:1;s:2:\"19\";i:2;s:2:\"22\";i:3;s:2:\"27\";i:4;s:2:\"46\";i:5;s:2:\"47\";}',1413112576,1,20,'POINT'),(34,131,21,'a:6:{i:0;s:1:\"2\";i:1;s:1:\"8\";i:2;s:2:\"10\";i:3;s:2:\"12\";i:4;s:2:\"27\";i:5;s:2:\"30\";}',1413112578,2,0,''),(35,131,21,'a:6:{i:0;s:2:\"12\";i:1;s:2:\"19\";i:2;s:2:\"21\";i:3;s:2:\"22\";i:4;s:2:\"23\";i:5;s:2:\"36\";}',1413112581,3,20,'POINT'),(36,131,21,'a:6:{i:0;s:1:\"8\";i:1;s:2:\"10\";i:2;s:2:\"20\";i:3;s:2:\"30\";i:4;s:2:\"38\";i:5;s:2:\"49\";}',1413112583,4,0,''),(37,131,21,'a:6:{i:0;s:2:\"18\";i:1;s:2:\"27\";i:2;s:2:\"30\";i:3;s:2:\"33\";i:4;s:2:\"39\";i:5;s:2:\"42\";}',1413112585,5,0,''),(38,132,21,'a:6:{i:0;s:1:\"7\";i:1;s:2:\"12\";i:2;s:2:\"17\";i:3;s:2:\"18\";i:4;s:2:\"22\";i:5;s:2:\"49\";}',1413113052,1,20,'POINT'),(39,132,21,'a:6:{i:0;s:2:\"11\";i:1;s:2:\"16\";i:2;s:2:\"17\";i:3;s:2:\"41\";i:4;s:2:\"44\";i:5;s:2:\"47\";}',1413113055,2,10,'POINT'),(40,132,21,'a:6:{i:0;s:2:\"14\";i:1;s:2:\"19\";i:2;s:2:\"24\";i:3;s:2:\"25\";i:4;s:2:\"27\";i:5;s:2:\"48\";}',1413113057,3,0,''),(41,132,21,'a:6:{i:0;s:1:\"4\";i:1;s:2:\"10\";i:2;s:2:\"31\";i:3;s:2:\"32\";i:4;s:2:\"40\";i:5;s:2:\"43\";}',1413113060,4,0,''),(42,132,21,'a:6:{i:0;s:1:\"3\";i:1;s:2:\"18\";i:2;s:2:\"20\";i:3;s:2:\"23\";i:4;s:2:\"34\";i:5;s:2:\"42\";}',1413113063,5,10,'POINT'),(43,133,21,'a:6:{i:0;s:1:\"3\";i:1;s:1:\"9\";i:2;s:2:\"18\";i:3;s:2:\"29\";i:4;s:2:\"36\";i:5;s:2:\"47\";}',1413113155,1,10,'POINT'),(44,133,21,'a:6:{i:0;s:1:\"5\";i:1;s:1:\"9\";i:2;s:2:\"25\";i:3;s:2:\"26\";i:4;s:2:\"28\";i:5;s:2:\"32\";}',1413113157,2,20,'POINT'),(45,133,21,'a:6:{i:0;s:1:\"5\";i:1;s:2:\"13\";i:2;s:2:\"28\";i:3;s:2:\"34\";i:4;s:2:\"38\";i:5;s:2:\"44\";}',1413113160,3,0,''),(46,133,21,'a:6:{i:0;s:1:\"9\";i:1;s:2:\"14\";i:2;s:2:\"25\";i:3;s:2:\"38\";i:4;s:2:\"47\";i:5;s:2:\"49\";}',1413113162,4,20,'POINT'),(47,133,21,'a:6:{i:0;s:1:\"2\";i:1;s:2:\"10\";i:2;s:2:\"32\";i:3;s:2:\"38\";i:4;s:2:\"41\";i:5;s:2:\"48\";}',1413113164,5,10,'POINT'),(48,134,21,'a:6:{i:0;s:2:\"11\";i:1;s:2:\"17\";i:2;s:2:\"19\";i:3;s:2:\"22\";i:4;s:2:\"23\";i:5;s:2:\"29\";}',1413113547,1,10,'POINT'),(49,134,21,'a:6:{i:0;s:2:\"24\";i:1;s:2:\"35\";i:2;s:2:\"38\";i:3;s:2:\"40\";i:4;s:2:\"43\";i:5;s:2:\"46\";}',1413113574,2,10,'POINT'),(50,134,21,'a:6:{i:0;s:1:\"8\";i:1;s:2:\"16\";i:2;s:2:\"24\";i:3;s:2:\"25\";i:4;s:2:\"41\";i:5;s:2:\"46\";}',1413113577,3,0,''),(51,134,21,'a:6:{i:0;s:2:\"24\";i:1;s:2:\"35\";i:2;s:2:\"38\";i:3;s:2:\"40\";i:4;s:2:\"43\";i:5;s:2:\"46\";}',1413113580,4,10,'POINT'),(52,134,21,'a:6:{i:0;s:1:\"3\";i:1;s:2:\"10\";i:2;s:2:\"15\";i:3;s:2:\"18\";i:4;s:2:\"43\";i:5;s:2:\"49\";}',1413113585,5,20,'POINT'),(53,135,21,'a:6:{i:0;s:1:\"9\";i:1;s:2:\"12\";i:2;s:2:\"19\";i:3;s:2:\"23\";i:4;s:2:\"33\";i:5;s:2:\"34\";}',1413117580,1,10,'MONEY'),(54,135,21,'a:6:{i:0;s:1:\"3\";i:1;s:1:\"9\";i:2;s:2:\"16\";i:3;s:2:\"21\";i:4;s:2:\"26\";i:5;s:2:\"28\";}',1413117582,2,20,'MONEY'),(55,135,21,'a:6:{i:0;s:1:\"4\";i:1;s:1:\"7\";i:2;s:2:\"19\";i:3;s:2:\"28\";i:4;s:2:\"46\";i:5;s:2:\"47\";}',1413117584,3,10,'MONEY'),(56,135,21,'a:6:{i:0;s:1:\"5\";i:1;s:2:\"12\";i:2;s:2:\"16\";i:3;s:2:\"32\";i:4;s:2:\"33\";i:5;s:2:\"39\";}',1413117586,4,10,'MONEY'),(57,135,21,'a:6:{i:0;s:1:\"9\";i:1;s:2:\"22\";i:2;s:2:\"27\";i:3;s:2:\"38\";i:4;s:2:\"43\";i:5;s:2:\"47\";}',1413117588,5,10,'MONEY'),(58,136,22,'a:6:{i:0;s:1:\"4\";i:1;s:1:\"6\";i:2;s:2:\"15\";i:3;s:2:\"16\";i:4;s:2:\"36\";i:5;s:2:\"45\";}',1413123093,1,10,'MONEY'),(59,136,22,'a:6:{i:0;s:1:\"5\";i:1;s:2:\"10\";i:2;s:2:\"14\";i:3;s:2:\"20\";i:4;s:2:\"25\";i:5;s:2:\"43\";}',1413123095,2,20,'MONEY'),(60,136,22,'a:6:{i:0;s:1:\"6\";i:1;s:2:\"18\";i:2;s:2:\"25\";i:3;s:2:\"29\";i:4;s:2:\"40\";i:5;s:2:\"48\";}',1413123097,3,10,'MONEY'),(61,136,22,'a:6:{i:0;s:2:\"10\";i:1;s:2:\"16\";i:2;s:2:\"33\";i:3;s:2:\"34\";i:4;s:2:\"41\";i:5;s:2:\"42\";}',1413123099,4,10,'MONEY'),(62,136,22,'a:6:{i:0;s:2:\"14\";i:1;s:2:\"31\";i:2;s:2:\"35\";i:3;s:2:\"39\";i:4;s:2:\"42\";i:5;s:2:\"46\";}',1413123101,5,10,'MONEY'),(63,136,21,'a:6:{i:0;s:1:\"9\";i:1;s:2:\"30\";i:2;s:2:\"33\";i:3;s:2:\"39\";i:4;s:2:\"44\";i:5;s:2:\"45\";}',1413123175,1,10,'MONEY'),(64,136,21,'a:6:{i:0;s:1:\"1\";i:1;s:2:\"14\";i:2;s:2:\"22\";i:3;s:2:\"24\";i:4;s:2:\"27\";i:5;s:2:\"28\";}',1413123177,2,10,'MONEY'),(65,136,21,'a:6:{i:0;s:1:\"5\";i:1;s:1:\"8\";i:2;s:2:\"26\";i:3;s:2:\"29\";i:4;s:2:\"34\";i:5;s:2:\"36\";}',1413123180,3,10,'MONEY'),(66,136,21,'a:6:{i:0;s:1:\"2\";i:1;s:1:\"6\";i:2;s:2:\"29\";i:3;s:2:\"31\";i:4;s:2:\"44\";i:5;s:2:\"45\";}',1413123182,4,10,'MONEY'),(67,136,21,'a:6:{i:0;s:1:\"3\";i:1;s:1:\"7\";i:2;s:2:\"32\";i:3;s:2:\"40\";i:4;s:2:\"43\";i:5;s:2:\"45\";}',1413123184,5,20,'MONEY'),(68,137,21,'a:6:{i:0;s:2:\"14\";i:1;s:2:\"18\";i:2;s:2:\"21\";i:3;s:2:\"27\";i:4;s:2:\"30\";i:5;s:2:\"47\";}',1413126904,1,10,'MONEY'),(69,137,21,'a:6:{i:0;s:1:\"2\";i:1;s:2:\"15\";i:2;s:2:\"25\";i:3;s:2:\"37\";i:4;s:2:\"47\";i:5;s:2:\"49\";}',1413126908,3,10,'MONEY'),(70,137,21,'a:6:{i:0;s:2:\"10\";i:1;s:2:\"33\";i:2;s:2:\"35\";i:3;s:2:\"38\";i:4;s:2:\"39\";i:5;s:2:\"41\";}',1413126910,5,0,''),(71,0,21,'a:6:{i:0;s:1:\"6\";i:1;s:2:\"15\";i:2;s:2:\"18\";i:3;s:2:\"20\";i:4;s:2:\"27\";i:5;s:2:\"35\";}',1413136688,1,0,''),(72,0,21,'a:6:{i:0;s:1:\"1\";i:1;s:1:\"3\";i:2;s:1:\"9\";i:3;s:2:\"12\";i:4;s:2:\"16\";i:5;s:2:\"35\";}',1413136782,2,0,''),(73,0,21,'a:6:{i:0;s:1:\"2\";i:1;s:2:\"14\";i:2;s:2:\"22\";i:3;s:2:\"33\";i:4;s:2:\"48\";i:5;s:2:\"49\";}',1413136816,3,0,''),(74,0,21,'a:6:{i:0;s:2:\"17\";i:1;s:2:\"19\";i:2;s:2:\"20\";i:3;s:2:\"22\";i:4;s:2:\"29\";i:5;s:2:\"32\";}',1413136890,4,0,''),(75,0,21,'a:6:{i:0;s:2:\"14\";i:1;s:2:\"16\";i:2;s:2:\"25\";i:3;s:2:\"27\";i:4;s:2:\"28\";i:5;s:2:\"46\";}',1413136908,5,0,'');
/*!40000 ALTER TABLE `LotteryTickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `News`
--

DROP TABLE IF EXISTS `News`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `News` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(255) NOT NULL DEFAULT '',
  `Lang` varchar(2) NOT NULL DEFAULT 'ru',
  `Date` int(11) NOT NULL DEFAULT '0',
  `Text` text,
  PRIMARY KEY (`Id`),
  KEY `Date_idx` (`Date`),
  KEY `Lang_idx` (`Lang`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `News`
--

LOCK TABLES `News` WRITE;
/*!40000 ALTER TABLE `News` DISABLE KEYS */;
INSERT INTO `News` VALUES (1,'test2','UA',1410434099,'\n                            test                        '),(6,'EN test','UA',1410437140,'\n                            <p>test EN news</p><p><br>\n		</p>\n\n\n\n	<p class=\"text_materials_title\">\n		материалы по теме:\n	</p>\n\n<p class=\"text_materials_list_item\">\n	<a href=\"http://comments.ua/politics/486443-za-selo-fermerov-parlamente.html\" class=\"link_live\" title=\"\">\nЗа село и фермеров в парламенте будет бороться «ЗАСТУП»\n	</a>\n</p>\n\n<p class=\"text_materials_list_item\">\n	<a href=\"http://comments.ua/politics/485154-obse-otpravit-ukrainu-680.html\" class=\"link_live\" title=\"\">\nОБСЕ отправит в Украину 680 наблюдателей - МИД\n	</a>\n</p>\n\n<p class=\"text_materials_list_item\">\n	<a href=\"http://comments.ua/politics/485085-turchinov-rasskazal-dolzhni-prohodit.html\" class=\"link_live\" title=\"\">\nТурчинов рассказал, как должны проходить выборы\n	</a>\n</p>\n\n<p class=\"text_materials_list_item\">\n	<a href=\"http://comments.ua/politics/484963-zastup-prezentoval-proekt-programmi-za.html\" class=\"link_live\" title=\"\">\n«ЗАСТУП» презентовал проект программы «За родную землю!»\n	</a>\n</p>\n	\n\n				\n\n		\n        \n		\n\n		\n		\n		\n			\n				\n			\n		\n        \n\n	\n    \n		\n		\n	\n	\n\n\n\n\n<span style=\"vertical-align: bottom; width: 106px; height: 20px;\"></span>\n\n	\n	\n        \n        \n	\n	\n\n\n\n\n\n\n	\n\n        \n        \n        \n		\nВ Верховную Раду не должны попасть депутаты с сомнительным прошлым - Пасхавер\n		\n		\n		\n			\n				\n					\n						<p class=\"text_news_header_date\" itemprop=\"datePublished\" datetime=\"2014-09-11T14:55:16+03:00\">\n11/09/2014 14:55\n						</p>\n						\n    						<p class=\"text_comments\"><a href=\"http://comments.ua/politics/486622-v-verhovnuyu-radu-dolzhni-popast.html#disqus_thread\">0</a></p>\n						\n						\n						\n					\n				\n				\n					\n    					\n					\n				<br>\n			\n		\n		\n		\n        \n\n        \n\n\n<p class=\"news_anons\" itemprop=\"description\">Директор Института \nэкономических исследований и&nbsp;политических консультаций Александр \nПасхавер отмечает в&nbsp;своем материале на&nbsp;«Голос.UA», что попадание \nсомнительных кандидатов от&nbsp;демократических сил в&nbsp;Верховную Раду может \nподорвать авторитет нынешней власти и&nbsp;привести к&nbsp;новой политической \nнестабильности</p>\n<p>Об этом передает <a href=\"http://www.unn.com.ua/ru/news/1384374-do-verkhovnoyi-radi-ne-povinni-potrapiti-deputati-z-sumnivnim-minulim-o-paskhaver\" target=\"_blank\">«УНН».</a></p>\n<p>«Выдвижение сомнительных кандидатов от коалиции после Евромайдана, \nобщество уже не простит и не попустит. Такие шаги бросят тень на всю \nнынешнюю систему власти. Таких примеров сегодня, к сожалению, множество,\n в том числе и в Полтавской области», - говорит эксперт.</p>\n<p>Как пишет «Голос.UA», в этом регионе по 145 округу баллотируется в \nВерховную Раду Юрий Бублик (сейчас народный депутат от партии \n«Свобода»), который, по информации издания, подозревается в организации \nубийства мэра и судьи Кременчуга. Бублик действовал через своего \nпомощника Александра Мельника, который недавно был арестован судом по \nподозрению в заказе убийства мэра.</p>\n<p>Кроме того, у господина Бублика уже есть криминальное прошлое. В \nчастности в 2011 году против него за превышение служебных полномочий, \nкоторые нанесли ущерб государству в размере 31 тыс гривен, было \nвозбуждено уголовное дело. Полтавский районный суд в 2012 году \nприговорил Юрия Бублика к трем годам условно, а также наложил запрет \nсроком на три года занимать руководящие должности.</p>\n \n\n\n			<p class=\"text_news_source\">\n				Читайте новости <a href=\"http://comments.ua/\">Comments.UA</a> в социальных сетях <a rel=\"nofollow\" href=\"http://www.facebook.com/comments.ua\" style=\"text-decoration: underline;\" target=\"_blank\">facebook</a> и <a rel=\"nofollow\" href=\"http://twitter.com/commentsUA\" style=\"text-decoration: underline;\" target=\"_blank\">twitter</a>.\n			</p>                        '),(7,'test3test3','UA',1410437247,'test3'),(8,'test4','UA',1410437255,'test4'),(9,'test5','UA',1410437261,'test5'),(10,'test6','UA',1410437272,'test6'),(11,'test7','UA',1410437280,'test7'),(12,'test8','UA',1410437290,'test8'),(13,'test9','UA',1410437299,'test9'),(14,'test10','UA',1410437306,'test10'),(15,'test11','UA',1410437318,'<p>&nbsp; test11<br></p>'),(17,'sdfsd','UA',1410537127,'fsdfsdfs'),(18,'dfgdfgdfgd','UA',1410537136,'dfgdfgdfgd'),(19,'game-popup-win','UA',1412540047,'game-popup-win');
/*!40000 ALTER TABLE `News` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `PlayerLotteryWins`
--

DROP TABLE IF EXISTS `PlayerLotteryWins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PlayerLotteryWins` (
  `PlayerId` int(11) NOT NULL DEFAULT '0',
  `LotteryId` int(11) NOT NULL DEFAULT '0',
  `Date` int(11) NOT NULL DEFAULT '0',
  `MoneyWin` int(11) NOT NULL DEFAULT '0',
  `PointsWin` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`PlayerId`,`LotteryId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `PlayerLotteryWins`
--

LOCK TABLES `PlayerLotteryWins` WRITE;
/*!40000 ALTER TABLE `PlayerLotteryWins` DISABLE KEYS */;
INSERT INTO `PlayerLotteryWins` VALUES (21,108,1412550493,0,0),(21,109,1412550553,0,36),(21,110,1412554083,0,35),(21,120,1412884382,0,70),(21,121,1412884441,0,10),(21,122,1413109131,0,20),(21,123,1413109261,0,40),(21,125,1413109562,0,20),(21,126,1413109742,0,30),(21,127,1413110041,0,10),(21,128,1413112081,0,10),(21,129,1413112202,0,60),(21,130,1413112441,0,30),(21,131,1413112622,0,40),(21,132,1413113101,0,40),(21,133,1413113221,0,60),(21,134,1413113641,0,50),(21,135,1413117602,60,0),(21,136,1413123242,60,0),(21,137,1413126961,20,0),(22,136,1413123242,60,0);
/*!40000 ALTER TABLE `PlayerLotteryWins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Players`
--

DROP TABLE IF EXISTS `Players`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Players` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Email` varchar(255) NOT NULL DEFAULT '',
  `Password` varchar(255) NOT NULL DEFAULT '',
  `Salt` varchar(13) NOT NULL DEFAULT '',
  `Nicname` varchar(20) NOT NULL DEFAULT '',
  `Name` varchar(50) NOT NULL DEFAULT '',
  `Surname` varchar(50) NOT NULL DEFAULT '',
  `SecondName` varchar(50) NOT NULL DEFAULT '',
  `Phone` varchar(50) NOT NULL DEFAULT '',
  `Birthday` int(11) NOT NULL DEFAULT '0',
  `DateRegistered` int(11) NOT NULL DEFAULT '0',
  `DateLogined` int(11) NOT NULL DEFAULT '0',
  `Country` varchar(2) NOT NULL DEFAULT '0',
  `Avatar` varchar(50) NOT NULL DEFAULT '',
  `Favorite` varchar(255) NOT NULL DEFAULT '0',
  `Visible` tinyint(1) NOT NULL DEFAULT '0',
  `Points` int(11) NOT NULL DEFAULT '0',
  `Money` int(11) NOT NULL DEFAULT '0',
  `GamesPlayed` int(11) NOT NULL DEFAULT '0',
  `InvitesCount` tinyint(1) NOT NULL DEFAULT '10',
  `Online` tinyint(1) NOT NULL DEFAULT '0',
  `OnlineTime` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Email_idx` (`Email`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Players`
--

LOCK TABLES `Players` WRITE;
/*!40000 ALTER TABLE `Players` DISABLE KEYS */;
INSERT INTO `Players` VALUES (21,'6ke6rn6@gmail.com','ec0729a5e4ae4c90a21907a5f62900f7','543abade4b220','rav','Александр','Петушков','','063722642',474768000,1411689818,1413148097,'UA','543ada4b49beb.jpg','a:6:{i:0;s:2:\"24\";i:1;s:2:\"35\";i:2;s:2:\"38\";i:3;s:2:\"40\";i:4;s:2:\"43\";i:5;s:2:\"46\";}',1,3045,140,80,10,1,1413154738),(22,'ravanger@kntele.com','c3f9e8be8999990df8f9aa888266a3fc','543a8c0f33271','id22','','','','',0,1413123087,1413123087,'UA','','a:0:{}',1,0,60,1,10,0,0),(23,'test3@gmail.com','2ca8b51f59a6d76da795fb2f81a1dd8d','543adc2a361af','','','','','',0,1413143594,1413143594,'UA','','a:0:{}',0,0,0,0,10,0,0),(24,'test@test.com','435ab3fd008106c19b9ff54b6e6e186a','543add4c50a96','','','','','',0,1413143884,1413143884,'UA','','0',0,0,0,0,10,0,0),(25,'button@test.com','13d9beeffa4c79388636e3d5e946f64d','543add92f337d','','','','','',0,1413143954,1413143954,'UA','','0',0,0,0,0,10,0,0),(26,'ssdfsdf@sfg.ru','ebdb43c3dd968cd7d3b1228cbc5d49f8','543aecf9b3070','','','','','',0,1413147897,1413147897,'UA','','0',0,0,0,0,10,0,0);
/*!40000 ALTER TABLE `Players` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `SEO`
--

DROP TABLE IF EXISTS `SEO`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SEO` (
  `Title` varchar(255) NOT NULL DEFAULT '',
  `Description` text,
  `Keywords` text,
  `Identifier` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`Identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `SEO`
--

LOCK TABLES `SEO` WRITE;
/*!40000 ALTER TABLE `SEO` DISABLE KEYS */;
INSERT INTO `SEO` VALUES ('игра бла бла бла','игра бла бла бла описание','игра, бла, бла','default');
/*!40000 ALTER TABLE `SEO` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ShopCategories`
--

DROP TABLE IF EXISTS `ShopCategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ShopCategories` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ShopCategories`
--

LOCK TABLES `ShopCategories` WRITE;
/*!40000 ALTER TABLE `ShopCategories` DISABLE KEYS */;
INSERT INTO `ShopCategories` VALUES (2,'Популярные'),(5,'Тестовая');
/*!40000 ALTER TABLE `ShopCategories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ShopItems`
--

DROP TABLE IF EXISTS `ShopItems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ShopItems` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(255) NOT NULL DEFAULT '',
  `Price` int(11) NOT NULL DEFAULT '0',
  `Quantity` int(11) NOT NULL DEFAULT '0',
  `Visible` tinyint(1) NOT NULL DEFAULT '0',
  `Image` varchar(50) NOT NULL DEFAULT '',
  `CategoryId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  KEY `CategoryId_idx` (`CategoryId`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ShopItems`
--

LOCK TABLES `ShopItems` WRITE;
/*!40000 ALTER TABLE `ShopItems` DISABLE KEYS */;
INSERT INTO `ShopItems` VALUES (5,'тот еще стол',2000,20,1,'542494b286a6e.jpg',2),(6,'WinDws',4000,0,1,'542494c515c70.jpg',2),(7,'Ойпот',1000,0,1,'542494e9b9f6c.jpg',2),(8,'Часы',2300,50,1,'542494fe41cf1.jpg',2),(9,'Ранетки',500,20,1,'5424951192287.jpg',5),(10,'Thermo',5000,0,1,'54249524451fd.jpg',5),(23,'Thermo2',200,0,1,'543463149797d.jpg',5);
/*!40000 ALTER TABLE `ShopItems` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ShopOrders`
--

DROP TABLE IF EXISTS `ShopOrders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ShopOrders` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `PlayerId` int(11) NOT NULL DEFAULT '0',
  `ItemId` int(11) NOT NULL DEFAULT '0',
  `DateOrdered` int(11) NOT NULL DEFAULT '0',
  `DateProcessed` int(11) NOT NULL DEFAULT '0',
  `AdminProcessed` varchar(255) NOT NULL DEFAULT '',
  `Status` tinyint(1) NOT NULL DEFAULT '0',
  `Name` varchar(255) NOT NULL DEFAULT '',
  `Surname` varchar(255) NOT NULL DEFAULT '',
  `SecondName` varchar(255) NOT NULL DEFAULT '',
  `Phone` varchar(255) NOT NULL DEFAULT '',
  `Region` varchar(255) NOT NULL DEFAULT '',
  `City` varchar(255) NOT NULL DEFAULT '',
  `Address` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`),
  KEY `idx_PlayerId` (`PlayerId`),
  KEY `idx_ItemId` (`ItemId`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ShopOrders`
--

LOCK TABLES `ShopOrders` WRITE;
/*!40000 ALTER TABLE `ShopOrders` DISABLE KEYS */;
INSERT INTO `ShopOrders` VALUES (1,21,6,1412728169,0,'',0,'Александр','Петушков','','+38(063)722 67 42','','Киев','Красноармейская 85'),(2,21,6,1412728412,0,'',0,'Александр','Петушков','','+38(063)722 67 42','','sdf','ssdfs'),(3,21,6,1412728476,0,'',0,'Александр','Петушков','','+38(063)722 67 42','','fsdf','sdfsdf'),(4,21,6,1412728495,0,'',0,'Александр','Петушков','','+38(063)722 67 42','','fsdf','sdfsdf'),(5,21,6,1412728559,0,'',0,'Александр','Петушков','','+38(063)722 67 42','','sdf','sdf'),(6,21,7,1412728754,0,'',0,'Александр','Петушков','','+38(063)722 67 42','','sdfsd','fsdfsdf'),(7,21,5,1412728882,0,'',0,'Александр','Петушков','','+38(063)722 67 42','','sdfsdf','sdfsdf'),(8,21,5,1413136514,0,'',0,'Александр','Петушков','','063722642','','Киев','Красноармейская 87/87');
/*!40000 ALTER TABLE `ShopOrders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `SiteStaticTexts`
--

DROP TABLE IF EXISTS `SiteStaticTexts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SiteStaticTexts` (
  `Id` varchar(50) NOT NULL DEFAULT '',
  `Lang` varchar(2) NOT NULL DEFAULT 'UA',
  `Text` text,
  PRIMARY KEY (`Id`,`Lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `SiteStaticTexts`
--

LOCK TABLES `SiteStaticTexts` WRITE;
/*!40000 ALTER TABLE `SiteStaticTexts` DISABLE KEYS */;
INSERT INTO `SiteStaticTexts` VALUES ('game-popup-fail','RU','game-popup-fail'),('game-popup-fail','UA','game-popup-fail'),('game-popup-win','RU','game-popup-win'),('game-popup-win','UA','game-popup-win'),('main-faq','RU','<ul style=\"display: block;\" class=\"faq\"><li class=\"faq_li\">\n                                    Почему мы не наебем\n                                    <p>Для того, чтобы сделать покупку, \nне нужно выходить из дому — заходите в наш магазин, выбирайте \nприглянувшийся товар и делайте заказ! Через короткое время курьерская \nслужба доставит его вам. Для того, чтобы сделать покупку, не нужно \nвыходить из дому — заходите в наш магазин, выбирайте!</p>\n                                </li><li class=\"faq_li\">\n                                    Как лучше выводить бабло\n                                    <p>Для того, чтобы сделать покупку, \nне нужно выходить из дому — заходите в наш магазин, выбирайте \nприглянувшийся товар и делайте заказ! Через короткое время курьерская \nслужба доставит его вам. Для того, чтобы сделать покупку, не нужно \nвыходить из дому — заходите в наш магазин, выбирайте! Не нужно выходить \nиз дому — заходите в наш магазин, выбирайте приглянувшийся товар и \nделайте заказ! Через короткое время курьерская служба доставит его вам. \nДля того, чтобы сделать покупку, не нужно выходить из дому — заходите в \nнаш магазин, выбирайте!<br><br>• Приват 24,<br>• QIWI,<br>• Visa/MasterCard,<br>• WebMoney.</p>\n                                </li><li class=\"faq_li\">\n                                    Кто на самом деле разрушил башни Всемирного торгового центра\n                                    <p>Для того, чтобы сделать покупку, \nне нужно выходить из дому — заходите в наш магазин, выбирайте \nприглянувшийся товар и делайте заказ! Через короткое время курьерская \nслужба доставит его вам. Для того, чтобы сделать покупку, не нужно \nвыходить из дому — заходите в наш магазин, выбирайте! Не нужно выходить \nиз дому — заходите в наш магазин, выбирайте приглянувшийся товар и \nделайте заказ! Через короткое время курьерская служба доставит его вам. \nДля того, чтобы сделать покупку, не нужно выходить из дому — заходите в \nнаш магазин, выбирайте!</p>\n                                </li><li class=\"faq_li\">\n                                    Почему пираты Сомали не \nвыступили продюссерами фильма «Пираты карибскогоморя», что в свою \nочередь повлияло на название картины и ее выход в прокат\n                                    <p>Для того, чтобы сделать покупку, \nне нужно выходить из дому — заходите в наш магазин, выбирайте \nприглянувшийся товар и делайте заказ! Через короткое время курьерская \nслужба доставит его вам. Для того, чтобы сделать покупку, не нужно \nвыходить из дому — заходите в наш магазин, выбирайте! Не нужно выходить \nиз дому — заходите в наш магазин, выбирайте приглянувшийся товар и \nделайте заказ! Через короткое время курьерская служба доставит его вам. \nДля того, чтобы сделать покупку, не нужно выходить из дому — заходите в \nнаш магазин, выбирайте!</p>\n                                </li><li class=\"faq_li\">\n                                    Когда Михаэль Шумахер выйдет из комы\n                                    <p>Для того, чтобы сделать покупку, \nне нужно выходить из дому — заходите в наш магазин, выбирайте \nприглянувшийся товар и делайте заказ! Через короткое время курьерская \nслужба доставит его вам. Для того, чтобы сделать покупку, не нужно \nвыходить из дому — заходите в наш магазин, выбирайте! Не нужно выходить \nиз дому — заходите в наш магазин, выбирайте приглянувшийся товар и \nделайте заказ! Через короткое время курьерская служба доставит его вам. \nДля того, чтобы сделать покупку, не нужно выходить из дому — заходите в \nнаш магазин, выбирайте!</p>\n                                </li><li class=\"faq_li\">\n                                    Как и зачем Алиса пробралась в Зазеркалье\n                                    <p>Для того, чтобы сделать покупку, \nне нужно выходить из дому — заходите в наш магазин, выбирайте \nприглянувшийся товар и делайте заказ! Через короткое время курьерская \nслужба доставит его вам. <br><br> Для того, чтобы сделать покупку, не \nнужно выходить из дому — заходите в наш магазин, выбирайте! Не нужно \nвыходить из дому — заходите в наш магазин, выбирайте приглянувшийся \nтовар и делайте заказ! Через короткое время курьерская служба доставит \nего вам. Для того, чтобы сделать покупку, не нужно выходить из дому — \nзаходите в наш магазин, выбирайте! Для того, чтобы сделать покупку, не \nнужно выходить из дому — заходите в наш магазин, выбирайте \nприглянувшийся товар и делайте заказ! Через короткое время курьерская \nслужба доставит его вам. Для того, чтобы сделать покупку, не нужно \nвыходить из дому — заходите в наш магазин, выбирайте! Не нужно выходить \nиз дому — заходите в наш магазин, выбирайте приглянувшийся товар и \nделайте заказ! Через короткое время курьерская служба доставит его вам. \nДля того, чтобы сделать покупку, не нужно выходить из дому — заходите в \nнаш магазин, выбирайте!</p>\n                                </li></ul>'),('main-faq','UA','<ul style=\"display: block;\" class=\"faq\"><li class=\"faq_li\">\n                                    Почему мы не наебем\n                                    <p>Для того, чтобы сделать покупку, \nне нужно выходить из дому — заходите в наш магазин, выбирайте \nприглянувшийся товар и делайте заказ! Через короткое время курьерская \nслужба доставит его вам. Для того, чтобы сделать покупку, не нужно \nвыходить из дому — заходите в наш магазин, выбирайте!</p>\n                                </li><li class=\"faq_li\">\n                                    Как лучше выводить бабло\n                                    <p>Для того, чтобы сделать покупку, \nне нужно выходить из дому — заходите в наш магазин, выбирайте \nприглянувшийся товар и делайте заказ! Через короткое время курьерская \nслужба доставит его вам. Для того, чтобы сделать покупку, не нужно \nвыходить из дому — заходите в наш магазин, выбирайте! Не нужно выходить \nиз дому — заходите в наш магазин, выбирайте приглянувшийся товар и \nделайте заказ! Через короткое время курьерская служба доставит его вам. \nДля того, чтобы сделать покупку, не нужно выходить из дому — заходите в \nнаш магазин, выбирайте!<br><br>• Приват 24,<br>• QIWI,<br>• Visa/MasterCard,<br>• WebMoney.</p>\n                                </li><li class=\"faq_li\">\n                                    Кто на самом деле разрушил башни Всемирного торгового центра\n                                    <p>Для того, чтобы сделать покупку, \nне нужно выходить из дому — заходите в наш магазин, выбирайте \nприглянувшийся товар и делайте заказ! Через короткое время курьерская \nслужба доставит его вам. Для того, чтобы сделать покупку, не нужно \nвыходить из дому — заходите в наш магазин, выбирайте! Не нужно выходить \nиз дому — заходите в наш магазин, выбирайте приглянувшийся товар и \nделайте заказ! Через короткое время курьерская служба доставит его вам. \nДля того, чтобы сделать покупку, не нужно выходить из дому — заходите в \nнаш магазин, выбирайте!</p>\n                                </li><li class=\"faq_li\">\n                                    Почему пираты Сомали не \nвыступили продюссерами фильма «Пираты карибскогоморя», что в свою \nочередь повлияло на название картины и ее выход в прокат\n                                    <p>Для того, чтобы сделать покупку, \nне нужно выходить из дому — заходите в наш магазин, выбирайте \nприглянувшийся товар и делайте заказ! Через короткое время курьерская \nслужба доставит его вам. Для того, чтобы сделать покупку, не нужно \nвыходить из дому — заходите в наш магазин, выбирайте! Не нужно выходить \nиз дому — заходите в наш магазин, выбирайте приглянувшийся товар и \nделайте заказ! Через короткое время курьерская служба доставит его вам. \nДля того, чтобы сделать покупку, не нужно выходить из дому — заходите в \nнаш магазин, выбирайте!</p>\n                                </li><li class=\"faq_li\">\n                                    Когда Михаэль Шумахер выйдет из комы\n                                    <p>Для того, чтобы сделать покупку, \nне нужно выходить из дому — заходите в наш магазин, выбирайте \nприглянувшийся товар и делайте заказ! Через короткое время курьерская \nслужба доставит его вам. Для того, чтобы сделать покупку, не нужно \nвыходить из дому — заходите в наш магазин, выбирайте! Не нужно выходить \nиз дому — заходите в наш магазин, выбирайте приглянувшийся товар и \nделайте заказ! Через короткое время курьерская служба доставит его вам. \nДля того, чтобы сделать покупку, не нужно выходить из дому — заходите в \nнаш магазин, выбирайте!</p>\n                                </li><li class=\"faq_li\">\n                                    Как и зачем Алиса пробралась в Зазеркалье\n                                    <p>Для того, чтобы сделать покупку, \nне нужно выходить из дому — заходите в наш магазин, выбирайте \nприглянувшийся товар и делайте заказ! Через короткое время курьерская \nслужба доставит его вам. <br><br> Для того, чтобы сделать покупку, не \nнужно выходить из дому — заходите в наш магазин, выбирайте! Не нужно \nвыходить из дому — заходите в наш магазин, выбирайте приглянувшийся \nтовар и делайте заказ! Через короткое время курьерская служба доставит \nего вам. Для того, чтобы сделать покупку, не нужно выходить из дому — \nзаходите в наш магазин, выбирайте! Для того, чтобы сделать покупку, не \nнужно выходить из дому — заходите в наш магазин, выбирайте \nприглянувшийся товар и делайте заказ! Через короткое время курьерская \nслужба доставит его вам. Для того, чтобы сделать покупку, не нужно \nвыходить из дому — заходите в наш магазин, выбирайте! Не нужно выходить \nиз дому — заходите в наш магазин, выбирайте приглянувшийся товар и \nделайте заказ! Через короткое время курьерская служба доставит его вам. \nДля того, чтобы сделать покупку, не нужно выходить из дому — заходите в \nнаш магазин, выбирайте!</p>\n                                </li></ul>'),('main-prizes','RU','текст над призами'),('main-prizes','UA','текст над призами<br>'),('main-rules','RU','\n                                    \n                                        <p>Выиграть в этой игре очень \nпросто! Угадывайте цифры и получайте вознаграждения в виде денег и \nбаллов. Баллы можно накопить и обменять на ценные призы.</p>\n                                        <p>Вам необходимо всего лишь подключить интуицию и выбрать 6 цифр из 49.</p>\n                                        <p>Розыгрыш проводится ежедневно. Осталось проследить за его результатами и обрад оваться своему выигрышу!</p>\n                                        <p>Выигранные деньги и баллы плюсуются в личном кабинете. Денежные призы легко обналичить, а баллы обменять на призы.</p>\n                                    \n                                '),('main-rules','UA','\n                                    \n                                        <p>Выиграть в этой игре очень \nпросто! Угадывайте цифры и получайте вознаграждения в виде денег и \nбаллов. Баллы можно накопить и обменять на ценные призы.</p>\n                                        <p>Вам необходимо всего лишь подключить интуицию и выбрать 6 цифр из 49.</p>\n                                        <p>Розыгрыш проводится ежедневно. Осталось проследить за его результатами и обрад оваться своему выигрышу!</p>\n                                        <p>Выигранные деньги и баллы плюсуются в личном кабинете. Денежные призы легко обналичить, а баллы обменять на призы.</p>\n                                    \n                                '),('prizes-order-popup','RU','prizes-order-popup'),('prizes-order-popup','UA','<span class=\"identifier\">prizes-order-popup</span>'),('prizes-popup-success','RU','Обмен будет произведен в течение суток. Приз будет выслан по указанному адресу.'),('prizes-popup-success','UA','Обмен будет произведен в течение суток. Приз будет выслан по указанному адресу.'),('prizes-popup-text','RU','текст на попапе призов'),('prizes-popup-text','UA','текст на попапе призов<br>'),('profile-bonus','RU','бонусы'),('profile-bonus','UA','бонусы'),('promo-comments','ru','Текст блока отзывы игроков'),('promo-comments','ua','Текст блока отзывы игроков<br>'),('promo-game-mechanic','ru','Играть легко и просто! А главное абсолютно бесплатно. Убедитесь в этом \nсами, ознакомившись с правилами игры представленными ниже.'),('promo-game-mechanic','ua','Играть легко и просто! А главное абсолютно бесплатно. Убедитесь в этом \nсами, ознакомившись с правилами игры представленными ниже.'),('promo-game-mechanic-1','ru','Игровая механика пункт 1'),('promo-game-mechanic-1','ua','Игровая механика пункт 1<br>'),('promo-game-mechanic-2','ru','promo-game-mechanic-2'),('promo-game-mechanic-2','ua','promo-game-mechanic-2'),('promo-game-mechanic-3','ru','promo-game-mechanic-3'),('promo-game-mechanic-3','ua','promo-game-mechanic-3'),('promo-game-mechanic-4','ru','promo-game-mechanic-4'),('promo-game-mechanic-4','ua','promo-game-mechanic-4'),('promo-login-rules','RU','<p><span style=\"font-weight: bold;\">1. Предоставление ограниченной пользовательской лицензии\n                                </span></p><p></p><p>В случае вашего согласия и при  \nусловии постоянного соблюдения вами данного Лицензионного соглашения \nкомпания Blizzard настоящим предоставляет, а вы настоящим принимаете \nограниченную, отзывную, не подлежащую передаче другим лицам, \nнеисключительную лицензию (а) на установку Игры на одном или более \nкомпьютерах, находящихся в вашей полноправной собственности или под \nвашим законным контролем; (b) на пользование Игрой посредством Услуги \nисключительно в некоммерческих развлекательных целях, которое \nрегулируется условиями, определенными ниже в Разделе 2.3; (с) для \nбесплатного создания и свободного распространения копий Игры среди \nдругих потенциальных пользователей в целях ее исключительного \nиспользования посредством Услуги. Любое использование Игры регулируется \nЛицензионным соглашением и Условиями пользования, которые конечный \nпользователь обязуется принять перед началом Игры. В случае противоречий\n между условиями настоящего Соглашения и Условиями пользования, \nнастоящее Соглашение имеет преимущественную силу над Условиями \nпользования.<br><br>бла бла бла<br></p><p></p>'),('promo-login-rules','UA','<p><span style=\"font-weight: bold;\">1. Предоставление ограниченной пользовательской лицензии\n                                </span></p><p></p><p>В случае вашего согласия и при  \nусловии постоянного соблюдения вами данного Лицензионного соглашения \nкомпания Blizzard настоящим предоставляет, а вы настоящим принимаете \nограниченную, отзывную, не подлежащую передаче другим лицам, \nнеисключительную лицензию (а) на установку Игры на одном или более \nкомпьютерах, находящихся в вашей полноправной собственности или под \nвашим законным контролем; (b) на пользование Игрой посредством Услуги \nисключительно в некоммерческих развлекательных целях, которое \nрегулируется условиями, определенными ниже в Разделе 2.3; (с) для \nбесплатного создания и свободного распространения копий Игры среди \nдругих потенциальных пользователей в целях ее исключительного \nиспользования посредством Услуги. Любое использование Игры регулируется \nЛицензионным соглашением и Условиями пользования, которые конечный \nпользователь обязуется принять перед началом Игры. В случае противоречий\n между условиями настоящего Соглашения и Условиями пользования, \nнастоящее Соглашение имеет преимущественную силу над Условиями \nпользования.<br><br>бла бла бла<br></p><p></p>'),('promo-partners','ru','Текст блока партнеры<br>'),('promo-partners','ua','Текст блока партнеры<br>'),('promo-top','ru','Lotzon — необычный проект, сутью которого бесплатная игра, участники \nкоторой могут стать обладателями денежных участники которой могут стать \nобладателями денежных и других ценных призов участники которой могут \nстать обладателями денежных и других ценных призов'),('promo-top','ua','Lotzon — необычный проект, сутью которого бесплатная игра, участники \nкоторой могут стать обладателями денежных участники которой могут стать \nобладателями денежных и других ценных призов участники которой могут \nстать обладателями денежных и других ценных призов<br>'),('promo-top-2','ru','\n                            Каждый день у нас \nновые победители, которые выигрывают денежные и другие призы. Участие в \nрозыгрыше всегда будет бесплатным. Присоединяйтесь.'),('promo-top-2','ua','\n                            Каждый день у нас \nновые победители, которые выигрывают денежные и другие призы. Участие в \nрозыгрыше всегда будет бесплатным. Присоединяйтесь.'),('tickets-complete-text','UA','Текст на результатах заполнения билетов<br>');
/*!40000 ALTER TABLE `SiteStaticTexts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `SupportedCountries`
--

DROP TABLE IF EXISTS `SupportedCountries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SupportedCountries` (
  `CountryCode` varchar(2) NOT NULL DEFAULT '',
  `Title` varchar(30) NOT NULL DEFAULT '',
  `Enabled` tinyint(1) NOT NULL DEFAULT '1',
  `Lang` varchar(2) NOT NULL DEFAULT 'ru',
  `Position` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`Position`,`CountryCode`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `SupportedCountries`
--

LOCK TABLES `SupportedCountries` WRITE;
/*!40000 ALTER TABLE `SupportedCountries` DISABLE KEYS */;
INSERT INTO `SupportedCountries` VALUES ('UA','Украина',1,'ua',1),('RU','Россия',1,'ru',2),('BY','Белорусь',1,'ru',3);
/*!40000 ALTER TABLE `SupportedCountries` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-10-13  1:59:48
