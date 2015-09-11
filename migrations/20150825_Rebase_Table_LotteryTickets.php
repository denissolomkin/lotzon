call isCol('LotteryTickets','IsGold','SELECT null',"
CREATE TABLE IF NOT EXISTS `LotteryTicketsTmp` 
(
`Id` int(11) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
 `LotteryId` int(11) NOT NULL DEFAULT '0',
 `PlayerId` int(11) NOT NULL DEFAULT '0',
 `Combination` varchar(255) NOT NULL,
 `DateCreated` int(11) NOT NULL DEFAULT '0',
 `TicketNum` tinyint(1) NOT NULL DEFAULT '1',
 `TicketWin` float(9,2) NOT NULL DEFAULT '0.00',
 `TicketWinCurrency` varchar(255) NOT NULL DEFAULT '',
 `IsGold` tinyint(1) NOT NULL DEFAULT '0',
 `B1` tinyint(1) DEFAULT NULL,
 `B2` tinyint(1) DEFAULT NULL,
 `B3` tinyint(1) DEFAULT NULL,
 `B4` tinyint(1) DEFAULT NULL,
 `B5` tinyint(1) DEFAULT NULL,
 `B6` tinyint(1) DEFAULT NULL,
 `B7` tinyint(1) DEFAULT NULL,
 `B8` tinyint(1) DEFAULT NULL,
 `B9` tinyint(1) DEFAULT NULL,
 `B10` tinyint(1) DEFAULT NULL,
 `B11` tinyint(1) DEFAULT NULL,
 `B12` tinyint(1) DEFAULT NULL,
 `B13` tinyint(1) DEFAULT NULL,
 `B14` tinyint(1) DEFAULT NULL,
 `B15` tinyint(1) DEFAULT NULL,
 `B16` tinyint(1) DEFAULT NULL,
 `B17` tinyint(1) DEFAULT NULL,
 `B18` tinyint(1) DEFAULT NULL,
 `B19` tinyint(1) DEFAULT NULL,
 `B20` tinyint(1) DEFAULT NULL,
 `B21` tinyint(1) DEFAULT NULL,
 `B22` tinyint(1) DEFAULT NULL,
 `B23` tinyint(1) DEFAULT NULL,
 `B24` tinyint(1) DEFAULT NULL,
 `B25` tinyint(1) DEFAULT NULL,
 `B26` tinyint(1) DEFAULT NULL,
 `B27` tinyint(1) DEFAULT NULL,
 `B28` tinyint(1) DEFAULT NULL,
 `B29` tinyint(1) DEFAULT NULL,
 `B30` tinyint(1) DEFAULT NULL,
 `B31` tinyint(1) DEFAULT NULL,
 `B32` tinyint(1) DEFAULT NULL,
 `B33` tinyint(1) DEFAULT NULL,
 `B34` tinyint(1) DEFAULT NULL,
 `B35` tinyint(1) DEFAULT NULL,
 `B36` tinyint(1) DEFAULT NULL,
 `B37` tinyint(1) DEFAULT NULL,
 `B38` tinyint(1) DEFAULT NULL,
 `B39` tinyint(1) DEFAULT NULL,
 `B40` tinyint(1) DEFAULT NULL,
 `B41` tinyint(1) DEFAULT NULL,
 `B42` tinyint(1) DEFAULT NULL,
 `B43` tinyint(1) DEFAULT NULL,
 `B44` tinyint(1) DEFAULT NULL,
 `B45` tinyint(1) DEFAULT NULL,
 `B46` tinyint(1) DEFAULT NULL,
 `B47` tinyint(1) DEFAULT NULL,
 `B48` tinyint(1) DEFAULT NULL,
 `B49` tinyint(1) DEFAULT NULL,
 KEY `idx_LotteryId` (`LotteryId`), KEY `idx_PlayerId` (`PlayerId`), KEY `L1` (`LotteryId`,`B1`), KEY `L2` (`LotteryId`,`B2`), KEY `L3` (`LotteryId`,`B3`), KEY `L4` (`LotteryId`,`B4`), KEY `L5` (`LotteryId`,`B5`), KEY `L6` (`LotteryId`,`B6`), KEY `L7` (`LotteryId`,`B7`), KEY `L8` (`LotteryId`,`B8`), KEY `L9` (`LotteryId`,`B9`), KEY `L10` (`LotteryId`,`B10`), KEY `L11` (`LotteryId`,`B11`), KEY `L12` (`LotteryId`,`B12`), KEY `L13` (`LotteryId`,`B13`), KEY `L14` (`LotteryId`,`B14`), KEY `L15` (`LotteryId`,`B15`), KEY `L16` (`LotteryId`,`B16`), KEY `L17` (`LotteryId`,`B17`), KEY `L18` (`LotteryId`,`B18`), KEY `L19` (`LotteryId`,`B19`), KEY `L20` (`LotteryId`,`B20`), KEY `L21` (`LotteryId`,`B21`), KEY `L22` (`LotteryId`,`B22`), KEY `L23` (`LotteryId`,`B23`), KEY `L24` (`LotteryId`,`B24`), KEY `L25` (`LotteryId`,`B25`), KEY `L26` (`LotteryId`,`B26`), KEY `L27` (`LotteryId`,`B27`), KEY `L28` (`LotteryId`,`B28`), KEY `L29` (`LotteryId`,`B29`), KEY `L30` (`LotteryId`,`B30`), KEY `L31` (`LotteryId`,`B31`), KEY `L32` (`LotteryId`,`B32`), KEY `L33` (`LotteryId`,`B33`), KEY `L34` (`LotteryId`,`B34`), KEY `L35` (`LotteryId`,`B35`), KEY `L36` (`LotteryId`,`B36`), KEY `L37` (`LotteryId`,`B37`), KEY `L38` (`LotteryId`,`B38`), KEY `L39` (`LotteryId`,`B39`), KEY `L40` (`LotteryId`,`B40`), KEY `L41` (`LotteryId`,`B41`), KEY `L42` (`LotteryId`,`B42`), KEY `L43` (`LotteryId`,`B43`), KEY `L44` (`LotteryId`,`B44`), KEY `L45` (`LotteryId`,`B45`), KEY `L46` (`LotteryId`,`B46`), KEY `L47` (`LotteryId`,`B47`), KEY `L48` (`LotteryId`,`B48`), KEY `L49` (`LotteryId`,`B49`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
");

call isCol('LotteryTickets','IsGold','SELECT null',"
INSERT INTO `LotteryTicketsTmp`(`Id`, `PlayerId`, `Combination`, `DateCreated`, `TicketNum`, `B1`, `B2`, `B3`, `B4`, `B5`, `B6`, `B7`, `B8`, `B9`, `B10`, `B11`, `B12`, `B13`, `B14`, `B15`, `B16`, `B17`, `B18`, `B19`, `B20`, `B21`, `B22`, `B23`, `B24`, `B25`, `B26`, `B27`, `B28`, `B29`, `B30`, `B31`, `B32`, `B33`, `B34`, `B35`, `B36`, `B37`, `B38`, `B39`, `B40`, `B41`, `B42`, `B43`, `B44`, `B45`, `B46`, `B47`, `B48`, `B49`) 
SELECT
`Id`, `PlayerId`, `Combination`, `DateCreated`, `TicketNum`, `B1`, `B2`, `B3`, `B4`, `B5`, `B6`, `B7`, `B8`, `B9`, `B10`, `B11`, `B12`, `B13`, `B14`, `B15`, `B16`, `B17`, `B18`, `B19`, `B20`, `B21`, `B22`, `B23`, `B24`, `B25`, `B26`, `B27`, `B28`, `B29`, `B30`, `B31`, `B32`, `B33`, `B34`, `B35`, `B36`, `B37`, `B38`, `B39`, `B40`, `B41`, `B42`, `B43`, `B44`, `B45`, `B46`, `B47`, `B48`, `B49`
FROM `LotteryTickets` WHERE LotteryTickets.LotteryId=0 AND LotteryTickets.Id>IFNULL((SELECT Id FROM `LotteryTicketsTmp` ORDER BY Id DESC LIMIT 1),0)
");

call isCol('LotteryTickets','IsGold','SELECT null',"
INSERT INTO `LotteryTicketsTmp`(`PlayerId`, `Combination`, `DateCreated`, `TicketNum`, `B1`, `B2`, `B3`, `B4`, `B5`, `B6`, `B7`, `B8`, `B9`, `B10`, `B11`, `B12`, `B13`, `B14`, `B15`, `B16`, `B17`, `B18`, `B19`, `B20`, `B21`, `B22`, `B23`, `B24`, `B25`, `B26`, `B27`, `B28`, `B29`, `B30`, `B31`, `B32`, `B33`, `B34`, `B35`, `B36`, `B37`, `B38`, `B39`, `B40`, `B41`, `B42`, `B43`, `B44`, `B45`, `B46`, `B47`, `B48`, `B49`) 
SELECT
`PlayerId`, `Combination`, `DateCreated`, `TicketNum`, `B1`, `B2`, `B3`, `B4`, `B5`, `B6`, `B7`, `B8`, `B9`, `B10`, `B11`, `B12`, `B13`, `B14`, `B15`, `B16`, `B17`, `B18`, `B19`, `B20`, `B21`, `B22`, `B23`, `B24`, `B25`, `B26`, `B27`, `B28`, `B29`, `B30`, `B31`, `B32`, `B33`, `B34`, `B35`, `B36`, `B37`, `B38`, `B39`, `B40`, `B41`, `B42`, `B43`, `B44`, `B45`, `B46`, `B47`, `B48`, `B49`
FROM `LotteryTickets` WHERE LotteryTickets.LotteryId=0 AND LotteryTickets.Id>IFNULL((SELECT Id FROM `LotteryTicketsTmp` ORDER BY Id DESC LIMIT 1),0)
");

call isCol('LotteryTickets','IsGold','SELECT null',"
RENAME TABLE LotteryTickets TO LotteryTicketsOld
");

call isCol('LotteryTickets','IsGold','SELECT null',"
RENAME TABLE LotteryTicketsTmp TO LotteryTickets
");

call dropTbl('LotteryTicketsOld');