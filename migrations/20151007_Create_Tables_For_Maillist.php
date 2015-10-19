CREATE TABLE IF NOT EXISTS `MaillistTemplates`(
    `Id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `FileName` VARCHAR(255) NOT NULL,
    `Variables` TEXT NOT NULL COMMENT 'serialized',
    `Description` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `MaillistMessages` (
    `Id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `Description` VARCHAR(255) NOT NULL,
    `TemplateId` INT UNSIGNED NOT NULL,
    `Values` TEXT NOT NULL COMMENT 'serialized, multilanguage',
    `Settings` TEXT NOT NULL COMMENT 'serialized',
    PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `MaillistTasks` (
    `Id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `Description` VARCHAR(255) NOT NULL,
    `MessageId` INT UNSIGNED NOT NULL,
    `Schedule` TINYINT(1) NOT NULL DEFAULT '0',
    `Settings` TEXT NOT NULL COMMENT 'serialized, events and filters',
    `Enable` TINYINT(1) NOT NULL DEFAULT '0',
    `Status` ENUM('waiting','in progress','done','archived','disable') DEFAULT NULL,
    PRIMARY KEY (`Id`),
    KEY `status` (`Status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `MaillistHistory` (
    `Id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `TaskId` INT NOT NULL,
    `Date` DATETIME NOT NULL DEFAULT 0,
    `PlayerId` INT NOT NULL,
    `Email` VARCHAR(255) NOT NULL,
    `Header` VARCHAR(255) NOT NULL,
    `Body` MEDIUMBLOB NOT NULL COMMENT 'gziped',
    `Status` ENUM('ok','error','spam','send') NOT NULL DEFAULT 'send',
    PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
