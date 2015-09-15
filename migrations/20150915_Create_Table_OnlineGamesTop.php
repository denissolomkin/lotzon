CREATE TABLE IF NOT EXISTS `OnlineGamesTop` (

	`Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  
	`PlayerId` int(11) unsigned NOT NULL,
  
	`GameId` int(11) unsigned NOT NULL,
  
	`Month` int(11) unsigned NOT NULL,
  
	`Currency` varchar(16) CHARACTER SET latin1 NOT NULL,
  
	`Rating` int(11) DEFAULT NULL,
  
	`Increment` int(11) NOT NULL DEFAULT '0',
  
	`Period` int(11) NOT NULL,
  
	`Start` int(11) NOT NULL,
  
	`End` int(11) NOT NULL,
  
	`LastUpdate` int(11) unsigned NOT NULL,
	PRIMARY KEY (`Id`), 
	UNIQUE KEY `MonthRating` (`Month`,`GameId`,`PlayerId`,`Currency`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
