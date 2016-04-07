ALTER TABLE `Players` CHANGE `Birthday` `Birthday` INT(11) NULL DEFAULT NULL;
UPDATE `Players` SET `Birthday` = NULL WHERE `Birthday` = 0 OR `Birthday` < -989884800;
