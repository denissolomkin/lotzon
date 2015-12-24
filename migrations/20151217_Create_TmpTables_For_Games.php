CREATE TABLE IF NOT EXISTS `GamesTmpApps` (
  `Uid` varchar(32) NOT NULL DEFAULT '',
  `Id` tinyint(1) NOT NULL,
  `Key` varchar(32) NOT NULL DEFAULT '',
  `Mode` varchar(128) NOT NULL DEFAULT '',
  `AppData` varchar(20644) NOT NULL DEFAULT '',
  `Players` varchar(512) DEFAULT NULL,
  `RequiredPlayers` int(11) NOT NULL DEFAULT '0',
  `IsRun` int(1) NOT NULL DEFAULT '0',
  `IsOver` int(1) NOT NULL DEFAULT '0',
  `IsSaved` int(1) NOT NULL DEFAULT '0',
  `Ping` int(11) NOT NULL DEFAULT '0'
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `GamesTmpPlayers` (
  `PlayerId` int(11) NOT NULL DEFAULT '0',
  `Lang` varchar(3) NOT NULL,
  `Country` varchar(3) NOT NULL,
  `Admin` int(1) DEFAULT NULL,
  `Bot` tinyint(1) DEFAULT NULL,
  `Name` varchar(64) DEFAULT NULL,
  `Avatar` varchar(32) NOT NULL,
  `AppId` int(11) DEFAULT NULL,
  `AppName` varchar(32) DEFAULT '',
  `AppMode` varchar(128) DEFAULT '',
  `AppUid` varchar(32) DEFAULT NULL,
  `Ping` int(11) NOT NULL DEFAULT '0'
) ENGINE=MEMORY DEFAULT CHARSET=utf8;