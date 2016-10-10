CREATE TABLE IF NOT EXISTS `BannersHit` (
`Id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`Date` int(11) unsigned NOT NULL,
`UserId` int(10) unsigned DEFAULT NULL,
`Device` enum('desktop','tablet','mobile') DEFAULT NULL,
`Location` enum('brand','top','right','teaser') DEFAULT NULL,
`Page` enum('default','blog','lottery','games','communication','users','prizes') DEFAULT NULL,
`Title` char(255) NOT NULL,
`Country` char(2) NOT NULL,
PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
