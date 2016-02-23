call addCol('Lotteries','PlayersCount','INT(11) DEFAULT 0 NOT NULL AFTER `LastTicketId`');
call addCol('Lotteries','PlayersCountIncr','INT(11) DEFAULT 0 NOT NULL AFTER `PlayersCount`');
call addCol('Lotteries','WinnersCountIncr','INT(11) DEFAULT 0 NOT NULL AFTER `WinnersCount`');
call addCol('Lotteries','BallsTotalIncr','TEXT CHARSET utf8 COLLATE utf8_general_ci NOT NULL AFTER `BallsTotal`');
call addCol('Lotteries','Prizes','TEXT CHARSET utf8 COLLATE utf8_general_ci NOT NULL AFTER `BallsTotalIncr`');
call addCol('Lotteries','PrizesGold','TEXT CHARSET utf8 COLLATE utf8_general_ci NOT NULL AFTER `Prizes`');
