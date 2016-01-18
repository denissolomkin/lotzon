CREATE TABLE IF NOT EXISTS `GamesTmpBots` (
    `id` int(11) UNSIGNED NOT NULL DEFAULT '0',
    `lang` varchar(3) NOT NULL,
    `country` varchar(3) NOT NULL,
    `name` varchar(64) DEFAULT NULL,
    `avatar` varchar(32) NOT NULL,
    `utc` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
    PRIMARY KEY (`id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;