CREATE TABLE IF NOT EXISTS `LotteryTicketsArchive` (
`Id` int(11) NOT NULL AUTO_INCREMENT, 
`LotteryId` int(11) NOT NULL DEFAULT '0', 
`PlayerId` int(11) NOT NULL DEFAULT '0', 
`Combination` varchar(255) NOT NULL,
`DateCreated` int(11) NOT NULL DEFAULT '0',
`TicketNum` tinyint(1) NOT NULL DEFAULT '1',
`TicketWin` float(9,2) NOT NULL DEFAULT '0.00',
`TicketWinCurrency` varchar(255) NOT NULL DEFAULT '',
`IsGold` BOOLEAN NOT NULL DEFAULT FALSE,
 PRIMARY KEY (`Id`),
  KEY `idx_LotteryId` (`LotteryId`),
  KEY `idx_PlayerId` (`PlayerId`)) 
ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1