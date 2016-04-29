ALTER TABLE `ShopOrders` CHANGE `AdminProcessed` `Sum` INT NULL DEFAULT NULL;
ALTER TABLE `ShopOrders` ADD `Equivalent` FLOAT(9,2) NULL DEFAULT NULL AFTER `Sum`;
UPDATE `ShopOrders` LEFT JOIN `ShopItems` ON `ShopItems`.Id = `ShopOrders`.ItemId SET `ShopOrders`.`Sum` = `ShopItems`.`Price`,`ShopOrders`.`Equivalent` = `ShopItems`.`Price` / 100 WHERE 1;
ALTER TABLE `MoneyOrders` ADD `Currency` VARCHAR(3) NULL DEFAULT NULL AFTER `Number`;
ALTER TABLE `MoneyOrders` ADD `Equivalent` FLOAT(9,2) NULL DEFAULT NULL AFTER `Sum`;
UPDATE `MoneyOrders` SET `Equivalent` = `Sum`,`Sum` = NULL WHERE 1;
UPDATE `MoneyOrders`
LEFT JOIN `Players` ON `Players`.`Id` = `MoneyOrders`.PlayerId
LEFT JOIN `MUICountries` ON `MUICountries`.`Code` = `Players`.`Country`
LEFT JOIN `MUICurrency` ON `MUICurrency`.`Id` = `MUICountries`.`Currency`
SET `MoneyOrders`.`Currency` = IFNULL(`MUICurrency`.`Code`,'RUB'),
`MoneyOrders`.`Sum` = IFNULL(`MoneyOrders`.`Sum`,`MoneyOrders`.`Equivalent` * IFNULL(`MUICurrency`.`Coefficient`,'3'))
WHERE `MoneyOrders`.`Currency` IS NULL AND `Players`.`Id` IS NOT NULL;
