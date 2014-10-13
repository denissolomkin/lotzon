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
-- Table structure for table `ShopCategories`
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
