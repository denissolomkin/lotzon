ALTER TABLE  `Transactions` ADD  `ObjectType` VARCHAR( 32 ) NULL DEFAULT NULL AFTER  `Id` ,
ADD  `ObjectId` VARCHAR( 16 ) NULL DEFAULT NULL AFTER  `ObjectType` ,
ADD INDEX (  `ObjectType` ,  `ObjectId` )