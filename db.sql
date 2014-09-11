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
INSERT INTO `Admins` VALUES ('rav','7e43742ba2a5f674b7df938d75e91242','5404eb6757dd0',1410427814,'127.0.0.1','ADMIN');
/*!40000 ALTER TABLE `Admins` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `GamesSettings`
--

LOCK TABLES `GamesSettings` WRITE;
/*!40000 ALTER TABLE `GamesSettings` DISABLE KEYS */;
INSERT INTO `GamesSettings` VALUES (1,43200),(2,50400),(3,57600);
/*!40000 ALTER TABLE `GamesSettings` ENABLE KEYS */;
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
  PRIMARY KEY (`BallsCount`,`CountryCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `LotterySettings`
--

LOCK TABLES `LotterySettings` WRITE;
/*!40000 ALTER TABLE `LotterySettings` DISABLE KEYS */;
INSERT INTO `LotterySettings` VALUES (1,'BY',123123,'POINT',2147483647,1),(1,'RU',123,'POINT',324565234,0),(1,'UA',12,'POINT',123235346,1),(2,'BY',123123,'POINT',2147483647,1),(2,'RU',234,'POINT',324565234,0),(2,'UA',23,'POINT',123235346,1),(3,'BY',1231231,'POINT',2147483647,1),(3,'RU',345,'MONEY',324565234,0),(3,'UA',34,'POINT',123235346,1),(4,'BY',1231231,'MONEY',2147483647,1),(4,'RU',456,'MONEY',324565234,0),(4,'UA',56,'MONEY',123235346,1),(5,'BY',231231,'MONEY',2147483647,1),(5,'RU',567,'MONEY',324565234,0),(5,'UA',67,'MONEY',123235346,1),(6,'BY',3123,'MONEY',2147483647,1),(6,'RU',678,'MONEY',324565234,0),(6,'UA',78,'MONEY',123235346,1);
/*!40000 ALTER TABLE `LotterySettings` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `News`
--

LOCK TABLES `News` WRITE;
/*!40000 ALTER TABLE `News` DISABLE KEYS */;
INSERT INTO `News` VALUES (1,'test2','ru',1410434099,'\n                            test                        '),(6,'EN test','en',1410437140,'\n                            <p>test EN news</p><p><br>\n		</p>\n\n\n\n	<p class=\"text_materials_title\">\n		материалы по теме:\n	</p>\n\n<p class=\"text_materials_list_item\">\n	<a href=\"http://comments.ua/politics/486443-za-selo-fermerov-parlamente.html\" class=\"link_live\" title=\"\">\nЗа село и фермеров в парламенте будет бороться «ЗАСТУП»\n	</a>\n</p>\n\n<p class=\"text_materials_list_item\">\n	<a href=\"http://comments.ua/politics/485154-obse-otpravit-ukrainu-680.html\" class=\"link_live\" title=\"\">\nОБСЕ отправит в Украину 680 наблюдателей - МИД\n	</a>\n</p>\n\n<p class=\"text_materials_list_item\">\n	<a href=\"http://comments.ua/politics/485085-turchinov-rasskazal-dolzhni-prohodit.html\" class=\"link_live\" title=\"\">\nТурчинов рассказал, как должны проходить выборы\n	</a>\n</p>\n\n<p class=\"text_materials_list_item\">\n	<a href=\"http://comments.ua/politics/484963-zastup-prezentoval-proekt-programmi-za.html\" class=\"link_live\" title=\"\">\n«ЗАСТУП» презентовал проект программы «За родную землю!»\n	</a>\n</p>\n	\n\n				\n\n		\n        \n		\n\n		\n		\n		\n			\n				\n			\n		\n        \n\n	\n    \n		\n		\n	\n	\n\n\n\n\n<span style=\"vertical-align: bottom; width: 106px; height: 20px;\"></span>\n\n	\n	\n        \n        \n	\n	\n\n\n\n\n\n\n	\n\n        \n        \n        \n		\nВ Верховную Раду не должны попасть депутаты с сомнительным прошлым - Пасхавер\n		\n		\n		\n			\n				\n					\n						<p class=\"text_news_header_date\" itemprop=\"datePublished\" datetime=\"2014-09-11T14:55:16+03:00\">\n11/09/2014 14:55\n						</p>\n						\n    						<p class=\"text_comments\"><a href=\"http://comments.ua/politics/486622-v-verhovnuyu-radu-dolzhni-popast.html#disqus_thread\">0</a></p>\n						\n						\n						\n					\n				\n				\n					\n    					\n					\n				<br>\n			\n		\n		\n		\n        \n\n        \n\n\n<p class=\"news_anons\" itemprop=\"description\">Директор Института \nэкономических исследований и&nbsp;политических консультаций Александр \nПасхавер отмечает в&nbsp;своем материале на&nbsp;«Голос.UA», что попадание \nсомнительных кандидатов от&nbsp;демократических сил в&nbsp;Верховную Раду может \nподорвать авторитет нынешней власти и&nbsp;привести к&nbsp;новой политической \nнестабильности</p>\n<p>Об этом передает <a href=\"http://www.unn.com.ua/ru/news/1384374-do-verkhovnoyi-radi-ne-povinni-potrapiti-deputati-z-sumnivnim-minulim-o-paskhaver\" target=\"_blank\">«УНН».</a></p>\n<p>«Выдвижение сомнительных кандидатов от коалиции после Евромайдана, \nобщество уже не простит и не попустит. Такие шаги бросят тень на всю \nнынешнюю систему власти. Таких примеров сегодня, к сожалению, множество,\n в том числе и в Полтавской области», - говорит эксперт.</p>\n<p>Как пишет «Голос.UA», в этом регионе по 145 округу баллотируется в \nВерховную Раду Юрий Бублик (сейчас народный депутат от партии \n«Свобода»), который, по информации издания, подозревается в организации \nубийства мэра и судьи Кременчуга. Бублик действовал через своего \nпомощника Александра Мельника, который недавно был арестован судом по \nподозрению в заказе убийства мэра.</p>\n<p>Кроме того, у господина Бублика уже есть криминальное прошлое. В \nчастности в 2011 году против него за превышение служебных полномочий, \nкоторые нанесли ущерб государству в размере 31 тыс гривен, было \nвозбуждено уголовное дело. Полтавский районный суд в 2012 году \nприговорил Юрия Бублика к трем годам условно, а также наложил запрет \nсроком на три года занимать руководящие должности.</p>\n \n\n\n			<p class=\"text_news_source\">\n				Читайте новости <a href=\"http://comments.ua/\">Comments.UA</a> в социальных сетях <a rel=\"nofollow\" href=\"http://www.facebook.com/comments.ua\" style=\"text-decoration: underline;\" target=\"_blank\">facebook</a> и <a rel=\"nofollow\" href=\"http://twitter.com/commentsUA\" style=\"text-decoration: underline;\" target=\"_blank\">twitter</a>.\n			</p>                        '),(7,'test3test3','ru',1410437247,'test3'),(8,'test4','ru',1410437255,'test4'),(9,'test5','ru',1410437261,'test5'),(10,'test6','ru',1410437272,'test6'),(11,'test7','ru',1410437280,'test7'),(12,'test8','ru',1410437290,'test8'),(13,'test9','ru',1410437299,'test9'),(14,'test10','ru',1410437306,'test10'),(15,'test11','ru',1410437318,'<p>&nbsp; test11<br></p>');
/*!40000 ALTER TABLE `News` ENABLE KEYS */;
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
INSERT INTO `SiteStaticTexts` VALUES ('faq','en','Вопросики ответики'),('faq','ru','Вопросики ответики'),('faq','ua','Вопросики ответики<br>'),('rules','en','<p>Game rules</p><ul><li>blah blah blah</li><li>blah blah blah</li><li>blah blah blah</li></ul>'),('rules','ru','<p>Правила игры </p><ul><li>бла бла бла</li><li>бла бла бла</li><li>бла бла бла</li><li>уху хухуху<br></li></ul>'),('rules','ua','<p>Правила игры </p><ul><li>бла бла бла</li><li>бла бла бла</li><li>бла бла бла</li><li>пущь пущь пущь<br></li></ul>');
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

-- Dump completed on 2014-09-11 15:22:37
