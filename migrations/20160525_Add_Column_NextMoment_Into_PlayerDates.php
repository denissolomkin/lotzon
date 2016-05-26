ALTER TABLE `PlayerDates` ADD COLUMN `NextMoment` INT UNSIGNED  NOT NULL AFTER `Moment`;
UPDATE `PlayerDates` SET `NextMoment` = `Moment` + RAND() * 40 * 60 WHERE `Moment` > 0;
