call isCol('PlayerGames','Month','SELECT null',"
CREATE TABLE IF NOT EXISTS `PlayerGamesTmp` (
`Id` int(11) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `PlayerId` int(11) unsigned NOT NULL,
  `GameId` int(2) unsigned NOT NULL,
  `GameUid` varchar(13) CHARACTER SET latin1 NOT NULL,
  `Date` int(11) unsigned NOT NULL,
  `Month` int(11) unsigned DEFAULT NULL,
  `Win` tinyint(1) NOT NULL DEFAULT '0',
  `Lose` tinyint(1) NOT NULL DEFAULT '0',
  `Draw` tinyint(1) NOT NULL DEFAULT '0',
  `Prize` float(9,2) DEFAULT NULL,
  `Result` tinyint(1) NOT NULL DEFAULT '0',
  `Currency` enum('MONEY','POINT','LOTZON') CHARACTER SET latin1 NOT NULL,
  `IsFee` tinyint(1) DEFAULT NULL,
  `Price` float(9,2) NOT NULL,
   KEY `Rating` (`Month`,`IsFee`),
   KEY `PlayerId` (`PlayerId`), 
   KEY `GameUid` (`GameUid`), 
   KEY `GameId` (`GameId`), 
   KEY `Month` (`Month`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
");

call isCol('PlayerGames','Month','SELECT null',"
INSERT INTO `PlayerGamesTmp` (`Id`, `PlayerId`, `GameId`, `GameUid`, `Date`, `Month`, `Win`, `Lose`, `Draw`, `Prize`, `Result`, `Currency`,`IsFee`, `Price`) 
SELECT
`Id`, `PlayerId`, `GameId`, `GameUid`, `Date`, UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(`Date`),'%Y-%m-01')), `Win`, `Lose`, `Draw`, `Prize`, `Result`, `Currency`, IF(`Price`=0,0,1), `Price`
FROM `PlayerGames` WHERE `PlayerGames`.Id > IFNULL((SELECT max(Id) FROM `PlayerGamesTmp`),0)
");

call isCol('PlayerGames','Month','SELECT null',"
INSERT INTO `PlayerGamesTmp` (`Id`, `PlayerId`, `GameId`, `GameUid`, `Date`, `Month`, `Win`, `Lose`, `Draw`, `Prize`, `Result`, `Currency`, `IsFee`, `Price`) 
SELECT
`Id`, `PlayerId`, `GameId`, `GameUid`, `Date`, UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(`Date`),'%Y-%m-01')), `Win`, `Lose`, `Draw`, `Prize`, `Result`, `Currency`, IF(`Price`=0,0,1), `Price`
FROM `PlayerGames` WHERE `PlayerGames`.Id > (SELECT max(Id) FROM `PlayerGamesTmp`)
");

call isCol('PlayerGames','Month','SELECT null',"
INSERT INTO `PlayerGamesTmp` (`Id`, `PlayerId`, `GameId`, `GameUid`, `Date`, `Month`, `Win`, `Lose`, `Draw`, `Prize`, `Result`, `Currency`, `IsFee`, `Price`) 
SELECT
`Id`, `PlayerId`, `GameId`, `GameUid`, `Date`, UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(`Date`),'%Y-%m-01')), `Win`, `Lose`, `Draw`, `Prize`, `Result`, `Currency`, IF(`Price`=0,0,1), `Price`
FROM `PlayerGames` WHERE `PlayerGames`.Id > (SELECT max(Id) FROM `PlayerGamesTmp`)
");


call isCol('PlayerGames','Month','SELECT null',"
RENAME TABLE `PlayerGames` TO `PlayerGamesOld`
");

call isCol('PlayerGames','Month','SELECT null',"
RENAME TABLE `PlayerGamesTmp` TO `PlayerGames`
");

call dropTbl('PlayerGamesOld');