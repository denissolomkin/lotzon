CREATE TABLE IF NOT EXISTS `Friends`(
`UserId` INT UNSIGNED NOT NULL,
`FriendId` INT UNSIGNED NOT NULL,
`Status` INT UNSIGNED NOT NULL DEFAULT 0,
`ModifyDate` INT(11) UNSIGNED NOT NULL,
UNIQUE KEY `userFriend` (`UserId`,`FriendId`),
KEY `search` (`UserId`,`FriendId`,`Status`)
) ENGINE=MYISAM CHARSET=utf8 COLLATE=utf8_general_ci;
