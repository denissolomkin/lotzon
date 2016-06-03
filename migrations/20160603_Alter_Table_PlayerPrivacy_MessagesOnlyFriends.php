ALTER TABLE `PlayerPrivacy` CHANGE `Message` `Message` TINYINT(1) NOT NULL DEFAULT '1';
UPDATE `PlayerPrivacy` SET `Message`=1 WHERE `Message`=2;