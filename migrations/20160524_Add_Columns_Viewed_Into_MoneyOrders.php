ALTER TABLE `MoneyOrders` ADD COLUMN `Viewed` TINYINT UNSIGNED DEFAULT 0 NOT NULL AFTER `Data`, ADD COLUMN `ViewedDate` INT(11) DEFAULT 0 NOT NULL AFTER `Viewed`;
UPDATE `MoneyOrders` SET `Viewed` = 1 WHERE `Status`>0;
ALTER TABLE `MoneyOrders` ADD INDEX `Notifications` (`PlayerId`, `Viewed`, `DateProcessed`, `Status`, `Type`);
