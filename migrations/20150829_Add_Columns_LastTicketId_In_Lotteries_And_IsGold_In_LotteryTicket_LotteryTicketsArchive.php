ALTER TABLE `Lotteries` ADD `LastTicketId` BIGINT UNSIGNED NULL DEFAULT NULL AFTER `Combination`;
ALTER TABLE `LotteryTickets` ADD `IsGold` BOOLEAN NOT NULL DEFAULT FALSE AFTER `TicketWinCurrency`;