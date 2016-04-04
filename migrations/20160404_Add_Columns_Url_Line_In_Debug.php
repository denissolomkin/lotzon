call addCol('Debug','Url','VARCHAR(255) NULL DEFAULT NULL AFTER `Log`');
call addCol('Debug','Line','INT UNSIGNED NULL DEFAULT NULL AFTER `Url`');
ALTER TABLE `Debug` CHANGE `Log` `Log` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `Debug` ADD INDEX `LogIdx` (`Log`);
