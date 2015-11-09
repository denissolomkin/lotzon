ALTER TABLE `MaillistHistory` DROP INDEX `statistic`, ADD INDEX `statistic` (`TaskId`, `PlayerId`, `Date`);
