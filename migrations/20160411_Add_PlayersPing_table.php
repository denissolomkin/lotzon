CREATE TABLE `PlayerPing`( `PlayerId` INT UNSIGNED NOT NULL, `Ping` INT(11) UNSIGNED NOT NULL, PRIMARY KEY (`PlayerId`) ) ENGINE=MEMORY;
ALTER TABLE `PlayerDates` DROP COLUMN `Ping`;
