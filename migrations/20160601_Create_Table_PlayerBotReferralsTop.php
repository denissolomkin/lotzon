CREATE TABLE IF NOT EXISTS `PlayerBotReferralsIncr`(
`PlayerId` INT UNSIGNED NOT NULL,
`ReferralsIncr` INT NOT NULL,
`ActivePerc` INT NOT NULL,
`IncrementFrom` INT NOT NULL,
`IncrementTo` INT NOT NULL,
`ActivePercFrom` INT NOT NULL,
`ActivePercTo` INT NOT NULL,
`LastUpdate` INT UNSIGNED NOT NULL,
PRIMARY KEY (`PlayerId`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
