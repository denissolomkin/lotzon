ALTER TABLE `LotterySettings` DROP PRIMARY KEY, ADD PRIMARY KEY (`CountryCode`, `BallsCount`, `Gold`);
