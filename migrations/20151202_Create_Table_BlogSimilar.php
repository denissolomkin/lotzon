CREATE TABLE IF NOT EXISTS `BlogSimilar` (
`BlogId` int(10) unsigned NOT NULL,
`SimilarBlogId` int(10) unsigned NOT NULL,
UNIQUE KEY `full` (`BlogId`,`SimilarBlogId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;