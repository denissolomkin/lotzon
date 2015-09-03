call isCol('Transactions','ObjectType','SELECT null',"
CREATE TABLE IF NOT EXISTS `TransactionsTmp` (
`Id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `ObjectType` varchar(32) DEFAULT NULL,
  `ObjectId` varchar(16) DEFAULT NULL,
  `PlayerId` int(11) NOT NULL DEFAULT '0',
  `Currency` varchar(20) NOT NULL DEFAULT '',
  `Sum` float(9,2) NOT NULL DEFAULT '0.00',
  `Balance` float(8,2) DEFAULT NULL,
  `Description` varchar(255) NOT NULL DEFAULT '',
  `Date` int(11) NOT NULL DEFAULT '0',
   KEY `idx_Admin`  (`Date`,`ObjectType`),
   KEY `idx_Player` (`PlayerId`,`Date`,`ObjectType`),
   KEY `idx_Object` (`ObjectType`,`ObjectId`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
");

call isCol('Transactions','ObjectType','SELECT null',"
INSERT INTO `TransactionsTmp` (`Id`, `PlayerId`, `Currency`, `Sum`, `Balance`, `Description`, `Date`) 
SELECT
`Id`, `PlayerId`, `Currency`, `Sum`, `Balance`, `Description`, `Date`
FROM `Transactions` WHERE `Transactions`.Id > IFNULL((SELECT max(Id) FROM `TransactionsTmp`),0)
");

call isCol('Transactions','ObjectType','SELECT null',"
INSERT INTO `TransactionsTmp` (`Id`, `PlayerId`, `Currency`, `Sum`, `Balance`, `Description`, `Date`) 
SELECT
`Id`, `PlayerId`, `Currency`, `Sum`, `Balance`, `Description`, `Date`
FROM `Transactions` WHERE `Transactions`.Id > (SELECT max(Id) FROM `TransactionsTmp`)
");

call isCol('Transactions','ObjectType','SELECT null',"
INSERT INTO `TransactionsTmp` (`Id`, `PlayerId`, `Currency`, `Sum`, `Balance`, `Description`, `Date`) 
SELECT
`Id`, `PlayerId`, `Currency`, `Sum`, `Balance`, `Description`, `Date`
FROM `Transactions` WHERE `Transactions`.Id > (SELECT max(Id) FROM `TransactionsTmp`)
");

call isCol('Transactions','ObjectType','SELECT null',"
RENAME TABLE `Transactions` TO `TransactionsOld`
");

call isCol('Transactions','ObjectType','SELECT null',"
RENAME TABLE `TransactionsTmp` TO `Transactions`
");

call dropTbl('TransactionsOld');