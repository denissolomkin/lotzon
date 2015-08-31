

CREATE TABLE IF NOT EXISTS `LotteryTicketsTmp` 
(
`Id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
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
 `B49` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



ALTER TABLE `LotteryTicketsTmp`
 ADD KEY `idx_LotteryId` (`LotteryId`), ADD KEY `idx_PlayerId` (`PlayerId`), ADD KEY `L1` (`LotteryId`,`B1`), ADD KEY `L2` (`LotteryId`,`B2`), ADD KEY `L3` (`LotteryId`,`B3`), ADD KEY `L4` (`LotteryId`,`B4`), ADD KEY `L5` (`LotteryId`,`B5`), ADD KEY `L6` (`LotteryId`,`B6`), ADD KEY `L7` (`LotteryId`,`B7`), ADD KEY `L8` (`LotteryId`,`B8`), ADD KEY `L9` (`LotteryId`,`B9`), ADD KEY `L10` (`LotteryId`,`B10`), ADD KEY `L11` (`LotteryId`,`B11`), ADD KEY `L12` (`LotteryId`,`B12`), ADD KEY `L13` (`LotteryId`,`B13`), ADD KEY `L14` (`LotteryId`,`B14`), ADD KEY `L15` (`LotteryId`,`B15`), ADD KEY `L16` (`LotteryId`,`B16`), ADD KEY `L17` (`LotteryId`,`B17`), ADD KEY `L18` (`LotteryId`,`B18`), ADD KEY `L19` (`LotteryId`,`B19`), ADD KEY `L20` (`LotteryId`,`B20`), ADD KEY `L21` (`LotteryId`,`B21`), ADD KEY `L22` (`LotteryId`,`B22`), ADD KEY `L23` (`LotteryId`,`B23`), ADD KEY `L24` (`LotteryId`,`B24`), ADD KEY `L25` (`LotteryId`,`B25`), ADD KEY `L26` (`LotteryId`,`B26`), ADD KEY `L27` (`LotteryId`,`B27`), ADD KEY `L28` (`LotteryId`,`B28`), ADD KEY `L29` (`LotteryId`,`B29`), ADD KEY `L30` (`LotteryId`,`B30`), ADD KEY `L31` (`LotteryId`,`B31`), ADD KEY `L32` (`LotteryId`,`B32`), ADD KEY `L33` (`LotteryId`,`B33`), ADD KEY `L34` (`LotteryId`,`B34`), ADD KEY `L35` (`LotteryId`,`B35`), ADD KEY `L36` (`LotteryId`,`B36`), ADD KEY `L37` (`LotteryId`,`B37`), ADD KEY `L38` (`LotteryId`,`B38`), ADD KEY `L39` (`LotteryId`,`B39`), ADD KEY `L40` (`LotteryId`,`B40`), ADD KEY `L41` (`LotteryId`,`B41`), ADD KEY `L42` (`LotteryId`,`B42`), ADD KEY `L43` (`LotteryId`,`B43`), ADD KEY `L44` (`LotteryId`,`B44`), ADD KEY `L45` (`LotteryId`,`B45`), ADD KEY `L46` (`LotteryId`,`B46`), ADD KEY `L47` (`LotteryId`,`B47`), ADD KEY `L48` (`LotteryId`,`B48`), ADD KEY `L49` (`LotteryId`,`B49`);



INSERT INTO `LotteryTicketsTmp`(`PlayerId`, `Combination`, `DateCreated`, `TicketNum`, `B1`, `B2`, `B3`, `B4`, `B5`, `B6`, `B7`, `B8`, `B9`, `B10`, `B11`, `B12`, `B13`, `B14`, `B15`, `B16`, `B17`, `B18`, `B19`, `B20`, `B21`, `B22`, `B23`, `B24`, `B25`, `B26`, `B27`, `B28`, `B29`, `B30`, `B31`, `B32`, `B33`, `B34`, `B35`, `B36`, `B37`, `B38`, `B39`, `B40`, `B41`, `B42`, `B43`, `B44`, `B45`, `B46`, `B47`, `B48`, `B49`) 
SELECT
`PlayerId`, `Combination`, `DateCreated`, `TicketNum`, `B1`, `B2`, `B3`, `B4`, `B5`, `B6`, `B7`, `B8`, `B9`, `B10`, `B11`, `B12`, `B13`, `B14`, `B15`, `B16`, `B17`, `B18`, `B19`, `B20`, `B21`, `B22`, `B23`, `B24`, `B25`, `B26`, `B27`, `B28`, `B29`, `B30`, `B31`, `B32`, `B33`, `B34`, `B35`, `B36`, `B37`, `B38`, `B39`, `B40`, `B41`, `B42`, `B43`, `B44`, `B45`, `B46`, `B47`, `B48`, `B49`
FROM `LotteryTickets` WHERE LotteryTickets.LotteryId=0;

RENAME TABLE 
LotteryTickets 
TO LotteryTicketsOld;

RENAME TABLE LotteryTicketsTmp TO LotteryTickets;