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
-- Table structure for table `ChanceGames`
--

DROP TABLE IF EXISTS `ChanceGames`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ChanceGames` (
  `Identifier` varchar(20) NOT NULL DEFAULT '',
  `MinFrom` int(11) NOT NULL DEFAULT '0',
  `MinTo` int(11) NOT NULL DEFAULT '0',
  `Prizes` varchar(255) NOT NULL DEFAULT '',
  `GameTitle` varchar(255) NOT NULL DEFAULT '',
  `GamePrice` varchar(255) NOT NULL DEFAULT '',
  `PointsWin` int(11) NOT NULL DEFAULT '0',
  `TriesCount` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ChanceGames`
--

LOCK TABLES `ChanceGames` WRITE;
/*!40000 ALTER TABLE `ChanceGames` DISABLE KEYS */;
INSERT INTO `ChanceGames` VALUES ('33',0,0,'a:1:{i:0;s:1:\"6\";}','Трешки','800',0,0),('44',0,0,'a:1:{i:0;s:1:\"5\";}','Четверки','900',0,0),('55',0,0,'a:3:{i:0;s:1:\"8\";i:1;s:1:\"9\";i:2;s:2:\"23\";}','Пятерки','1200',0,7),('moment',1,3,'a:0:{}','','0',50000,0);
/*!40000 ALTER TABLE `ChanceGames` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-10-15  5:26:01
