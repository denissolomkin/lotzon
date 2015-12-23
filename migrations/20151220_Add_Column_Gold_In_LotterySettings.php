call addCol('LotterySettings','Gold','TINYINT(1) DEFAULT 0 NOT NULL AFTER `Currency`');
ALTER TABLE `LotterySettings` DROP PRIMARY KEY, ADD PRIMARY KEY (`BallsCount`, `CountryCode`, `Gold`);
