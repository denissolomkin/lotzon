ALTER TABLE `GamesTmpPlayers` ADD `Currency` VARCHAR(3) NOT NULL AFTER `Lang`;
ALTER TABLE `GamesTmpBots` ADD `currency` VARCHAR(3) NOT NULL AFTER `lang`;
ALTER TABLE `Players` ADD `Currency` VARCHAR(3) NOT NULL AFTER `Birthday`;
UPDATE `Players` LEFT JOIN `MUICountries` ON `MUICountries`.`Code` = `Players`.`Country` SET `Players`.`Currency` = IFNULL(`MUICountries`.`Code`,'RU') WHERE 1;
