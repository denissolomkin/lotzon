CREATE TABLE `PlayersPreregistration` (
`Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`Email` varchar(255) NOT NULL DEFAULT '',
`DateRegistration` int(11) NOT NULL DEFAULT '0',
`Ip` varchar(255) NOT NULL DEFAULT '',
`Hash` varchar(255) NOT NULL DEFAULT '',
`CookieId` int(11) unsigned NOT NULL DEFAULT '0',
`InviterId` int(11) unsigned NOT NULL DEFAULT '0',
`ReferalId` int(11) NOT NULL DEFAULT '0',
`SocialId` varchar(32) NOT NULL,
`SocialName` enum('Facebook','Twitter','Vkontakte','Google','Odnoklassniki') NOT NULL,
`SocialEmail` varchar(255) NOT NULL,
PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
