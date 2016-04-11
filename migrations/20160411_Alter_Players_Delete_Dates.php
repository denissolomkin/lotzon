ALTER TABLE `Players`
DROP `DateRegistered`,
DROP `DateLogined`,
DROP `DateNoticed`,
DROP `DateChanced`,
DROP `Online`,
DROP `OnlineTime`,
DROP `AdBlock`,
DROP `DateAdBlocked`,
DROP `WebSocket`,
DROP `InvitesCount`,
DROP `SocialPostsCount`;
call dropCol('PlayerDates','Logined')