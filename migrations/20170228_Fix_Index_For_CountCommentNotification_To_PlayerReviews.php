ALTER TABLE `PlayerReviews` DROP INDEX `getCountNotification`, ADD INDEX `getCountNotification` (`Module`, `Status`, `ObjectId`, `Date`, `PlayerId`);
