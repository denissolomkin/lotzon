call isCol('Transactions','CurrencyId','SELECT null',"
CREATE TABLE IF NOT EXISTS `TransactionsTmp` (
`Id` int(11) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
`ObjectType` varchar(32) DEFAULT NULL,
`ObjectId` int(11) unsigned DEFAULT NULL,
`ObjectUid` varchar(16) DEFAULT NULL,
`PlayerId` int(11) unsigned NOT NULL DEFAULT '0',
`Currency` varchar(20) NOT NULL DEFAULT '',
`CurrencyId` TINYINT(1) unsigned NOT NULL DEFAULT '0',
`Equivalent` float(9,2) NOT NULL DEFAULT '0.00',
`Sum` float(9,2) NOT NULL DEFAULT '0.00',
`Balance` float(8,2) DEFAULT NULL,
`Description` varchar(255) NOT NULL DEFAULT '',
`Date` int(11) NOT NULL DEFAULT '0',
KEY `idx_Admin` (`Date`,`ObjectType`),
KEY `idx_Player` (`PlayerId`,`Date`,`ObjectType`),
KEY `idx_Object` (`ObjectType`,`ObjectId`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
");

call isCol('Transactions','CurrencyId','SELECT null',"
INSERT INTO `TransactionsTmp` (`Id`, `ObjectType`, `ObjectId`, `ObjectUid`, `PlayerId`, `Currency`, `Sum`, `Balance`, `Description`, `Date`)
SELECT `Id`, `ObjectType`, `ObjectId`, `ObjectUid`, `PlayerId`, `Currency`, `Sum`, `Balance`, `Description`, `Date`
FROM `Transactions` WHERE `Transactions`.Id > IFNULL((SELECT max(Id) FROM `TransactionsTmp`),0)
");

call isCol('Transactions','CurrencyId','SELECT null',"
UPDATE `TransactionsTmp`
LEFT JOIN `Players` ON `TransactionsTmp`.`PlayerId` = `Players`.`Id`
LEFT JOIN `MUICountries` ON `MUICountries`.`Code` = `Players`.`Currency`
LEFT JOIN `MUICurrency` ON `MUICountries`.Currency = `MUICurrency`.Id
SET `CurrencyId` = IF(`TransactionsTmp`.Currency = 'MONEY', `MUICurrency`.`Id`, 0),
	`TransactionsTmp`.`Equivalent` = IF(`TransactionsTmp`.Currency = 'MONEY', `TransactionsTmp`.`Sum` / `MUICurrency`.Coefficient, `TransactionsTmp`.`Sum` / (`MUICurrency`.Coefficient * `MUICurrency`.Rate))
WHERE `TransactionsTmp`.Equivalent = 0
");

call isCol('Transactions','CurrencyId','SELECT null',"
INSERT INTO `TransactionsTmp` (`Id`, `ObjectType`, `ObjectId`, `ObjectUid`, `PlayerId`, `Currency`, `Sum`, `Balance`, `Description`, `Date`)
SELECT `Id`, `ObjectType`, `ObjectId`, `ObjectUid`, `PlayerId`, `Currency`, `Sum`, `Balance`, `Description`, `Date`
FROM `Transactions` WHERE `Transactions`.Id > IFNULL((SELECT max(Id) FROM `TransactionsTmp`),0)
");

call isCol('Transactions','CurrencyId','SELECT null',"
UPDATE `TransactionsTmp`
LEFT JOIN `Players` ON `TransactionsTmp`.`PlayerId` = `Players`.`Id`
LEFT JOIN `MUICountries` ON `MUICountries`.`Code` = `Players`.`Currency`
LEFT JOIN `MUICurrency` ON `MUICountries`.Currency = `MUICurrency`.Id
SET `CurrencyId` = IF(`TransactionsTmp`.Currency = 'MONEY', `MUICurrency`.`Id`, 0),
`TransactionsTmp`.`Equivalent` = IF(`TransactionsTmp`.Currency = 'MONEY', `TransactionsTmp`.`Sum` / `MUICurrency`.Coefficient, `TransactionsTmp`.`Sum` / (`MUICurrency`.Coefficient * `MUICurrency`.Rate))
WHERE `TransactionsTmp`.Equivalent = 0
");

call isCol('Transactions','CurrencyId','SELECT null',"
INSERT INTO `TransactionsTmp` (`Id`, `ObjectType`, `ObjectId`, `ObjectUid`, `PlayerId`, `Currency`, `Sum`, `Balance`, `Description`, `Date`)
SELECT `Id`, `ObjectType`, `ObjectId`, `ObjectUid`, `PlayerId`, `Currency`, `Sum`, `Balance`, `Description`, `Date`
FROM `Transactions` WHERE `Transactions`.Id > IFNULL((SELECT max(Id) FROM `TransactionsTmp`),0)
");

call isCol('Transactions','CurrencyId','SELECT null',"
UPDATE `TransactionsTmp`
LEFT JOIN `Players` ON `TransactionsTmp`.`PlayerId` = `Players`.`Id`
LEFT JOIN `MUICountries` ON `MUICountries`.`Code` = `Players`.`Currency`
LEFT JOIN `MUICurrency` ON `MUICountries`.Currency = `MUICurrency`.Id
SET `CurrencyId` = IF(`TransactionsTmp`.Currency = 'MONEY', `MUICurrency`.`Id`, 0),
`TransactionsTmp`.`Equivalent` = IF(`TransactionsTmp`.Currency = 'MONEY', `TransactionsTmp`.`Sum` / `MUICurrency`.Coefficient, `TransactionsTmp`.`Sum` / (`MUICurrency`.Coefficient * `MUICurrency`.Rate))
WHERE `TransactionsTmp`.Equivalent = 0
");

call isCol('Transactions','CurrencyId','SELECT null',"
RENAME TABLE `Transactions` TO `TransactionsOld`
");

call isCol('Transactions','CurrencyId','SELECT null',"
RENAME TABLE `TransactionsTmp` TO `Transactions`
");

call dropTbl('TransactionsOld');