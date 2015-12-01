CREATE TABLE IF NOT EXISTS `PlayerReviewsLikes` (
`CommentId` int(10) unsigned NOT NULL,
`PlayerId` int(10) unsigned NOT NULL,
UNIQUE KEY `all` (`CommentId`,`PlayerId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
