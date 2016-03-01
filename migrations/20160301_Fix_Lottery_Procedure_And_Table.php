SET NAMES 'utf8' COLLATE 'utf8_general_ci';

ALTER TABLE `LotteryTmp` CHANGE `points` `points` FLOAT(9,2) DEFAULT 0.00 NOT NULL;

DROP PROCEDURE IF EXISTS `saveLottery`;

CREATE PROCEDURE `saveLottery`(IN lotteryDate INT, IN lotteryId INT)
BEGIN

DECLARE done INT DEFAULT 0;

DECLARE playerid INT;
DECLARE playerMoney FLOAT;
DECLARE playerPoints FLOAT;

DECLARE balMoney FLOAT;
DECLARE balPoints FLOAT;

DECLARE rCursor CURSOR FOR
SELECT lt.playerId, lt.points, lt.money FROM LotteryTmp AS lt;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET done=1;

OPEN rCursor;
curloop :
LOOP
FETCH rCursor INTO playerid, playerPoints, playerMoney;
IF done THEN
LEAVE curloop;
END IF;

UPDATE
Players AS p
SET
p.Points      = p.Points + playerPoints,
p.Money       = p.Money + playerMoney,
p.GamesPlayed = p.GamesPlayed + 1
WHERE
p.Id = playerid;

SELECT
p.Points,p.Money
INTO
balPoints,balMoney
FROM
Players AS p
WHERE
p.Id = playerid;

IF (playerMoney>0) THEN
INSERT INTO Transactions
(PlayerId, Currency, SUM, DATE, Balance, Description, ObjectType, ObjectId)
VALUES
( playerid, 'MONEY', playerMoney, lotteryDate, balMoney, 'Выигрыш в розыгрыше', 'Lottery', lotteryId);
END IF;
IF (playerPoints>0) THEN
INSERT INTO Transactions
(PlayerId, Currency, SUM, DATE, Balance, Description, ObjectType, ObjectId)
VALUES
( playerid, 'POINT', playerPoints, lotteryDate, balPoints, 'Выигрыш в розыгрыше', 'Lottery', lotteryId);
END IF;
END LOOP;

SELECT
lt.playerId
INTO
playerid
FROM
LotteryTmp AS lt
LIMIT 1;
CLOSE rCursor;
END;