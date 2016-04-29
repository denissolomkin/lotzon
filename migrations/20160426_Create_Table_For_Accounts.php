CREATE TABLE IF NOT EXISTS `PlayerAccounts` (
`PlayerId` int(11) unsigned NOT NULL,
`AccountName` enum('WebMoney','YandexMoney','Qiwi','Phone') CHARACTER SET latin1 NOT NULL,
`AccountId` varchar(16) CHARACTER SET latin1 NOT NULL,
`Date` int(11) unsigned NOT NULL,
`Enabled` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `PlayerAccounts`
ADD PRIMARY KEY (`PlayerId`,`AccountName`,`AccountId`), ADD INDEX `AccountsEnabled` (`PlayerId`,`Enabled`), ADD INDEX `AccountId` (`AccountId`);

INSERT INTO `PlayerAccounts` (`PlayerId`, `AccountName`, `AccountId`, `Date`)
(SELECT * FROM
(SELECT Id PlayerId, "WebMoney" AccountName, WebMoney AccountId, 0 FROM `Players`
WHERE `WebMoney` IS NOT NULL AND `WebMoney` <> '0' AND `WebMoney` <> ''
UNION ALL
SELECT Id PlayerId, "YandexMoney" AccountName, YandexMoney AccountId, 0 FROM `Players`
WHERE `YandexMoney` IS NOT NULL AND `YandexMoney` <> 0
UNION ALL
SELECT Id PlayerId, 'Qiwi' AccountName, Qiwi AccountId, 0 FROM `Players`
WHERE `Qiwi` IS NOT NULL AND `Qiwi` <> 0
UNION ALL
SELECT Id PlayerId, 'Phone' AccountName, Phone AccountId, 0 FROM `Players`
WHERE `Phone` IS NOT NULL AND `Phone` <> '0' AND `Phone` <> '') temp
ORDER BY temp.PlayerId
)