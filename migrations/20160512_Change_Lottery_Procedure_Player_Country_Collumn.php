SET NAMES 'utf8' COLLATE 'utf8_general_ci';

DROP PROCEDURE IF EXISTS `applyLottery`;
CREATE PROCEDURE `applyLottery`(IN LotteryId INT)
BEGIN
#need for cursor
DECLARE done INT DEFAULT 0;
#from lottery
DECLARE lotteryLastTicketId INT;
DECLARE B1,B2,B3,B4,B5,B6,B7,B8,B9,B10,B11,B12,B13,B14,B15,B16,B17,B18,B19,B20,B21,B22,B23,B24,B25,B26,B27,B28,B29,B30,B31,B32,B33,B34,B35,B36,B37,B38,B39,B40,B41,B42,B43,B44,B45,B46,B47,B48,B49 TINYINT;
DECLARE lotteryDate INT;
#for tickets
DECLARE ticketId INT;
DECLARE ticketPlayerId INT;
DECLARE ticketCombination VARCHAR(255);
DECLARE ticketDateCreated INT;
DECLARE ticketTicketNum INT;
DECLARE ticketIsGold INT;
DECLARE tB1, tB2, tB3, tB4, tB5, tB6, tB7, tB8, tB9, tB10, tB11, tB12, tB13, tB14, tB15, tB16, tB17, tB18, tB19, tB20, tB21, tB22, tB23, tB24, tB25, tB26, tB27, tB28, tB29, tB30, tB31, tB32, tB33, tB34, tB35, tB36, tB37, tB38, tB39, tB40, tB41, tB42, tB43, tB44, tB45, tB46, tB47, tB48, tB49 TINYINT;
#results
DECLARE winBallsCount INT;
DECLARE winTicketWin FLOAT;
DECLARE winTicketWinCurrency VARCHAR(255);
#settings
DECLARE settingsCount INT;
#cursor for all tickets
DECLARE rCursor CURSOR FOR
SELECT
lt.Id,
lt.PlayerId,
lt.Combination,
lt.DateCreated,
lt.TicketNum,
lt.IsGold,
lt.B1, lt.B2, lt.B3, lt.B4, lt.B5, lt.B6, lt.B7, lt.B8, lt.B9, lt.B10, lt.B11, lt.B12, lt.B13, lt.B14, lt.B15, lt.B16, lt.B17, lt.B18, lt.B19, lt.B20, lt.B21, lt.B22, lt.B23, lt.B24, lt.B25, lt.B26, lt.B27, lt.B28, lt.B29, lt.B30, lt.B31, lt.B32, lt.B33, lt.B34, lt.B35, lt.B36, lt.B37, lt.B38, lt.B39, lt.B40, lt.B41, lt.B42, lt.B43, lt.B44, lt.B45, lt.B46, lt.B47, lt.B48, lt.B49
FROM
LotteryTickets AS lt
WHERE
lt.id<=lotteryLastTicketId
AND
lt.LotteryId=0;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET done=1;
#get info about lottery
SELECT
l.LastTicketId,
l.Date,
l.B1, l.B2, l.B3, l.B4, l.B5, l.B6, l.B7, l.B8, l.B9, l.B10, l.B11, l.B12, l.B13, l.B14, l.B15, l.B16, l.B17, l.B18, l.B19, l.B20, l.B21, l.B22, l.B23, l.B24, l.B25, l.B26, l.B27, l.B28, l.B29, l.B30, l.B31, l.B32, l.B33, l.B34, l.B35, l.B36, l.B37, l.B38, l.B39, l.B40, l.B41, l.B42, l.B43, l.B44, l.B45, l.B46, l.B47, l.B48, l.B49
INTO
lotteryLastTicketId,
lotteryDate,
B1,B2,B3,B4,B5,B6,B7,B8,B9,B10,B11,B12,B13,B14,B15,B16,B17,B18,B19,B20,B21,B22,B23,B24,B25,B26,B27,B28,B29,B30,B31,B32,B33,B34,B35,B36,B37,B38,B39,B40,B41,B42,B43,B44,B45,B46,B47,B48,B49
FROM
Lotteries AS l
WHERE
l.id = LotteryId;
#clear tmp table
DELETE FROM `LotteryTmp`;
#for all tickets
OPEN rCursor;
curloop :
LOOP
FETCH rCursor INTO ticketId, ticketPlayerId, ticketCombination, ticketDateCreated, ticketTicketNum, ticketIsGold, tB1, tB2, tB3, tB4, tB5, tB6, tB7, tB8, tB9, tB10, tB11, tB12, tB13, tB14, tB15, tB16, tB17, tB18, tB19, tB20, tB21, tB22, tB23, tB24, tB25, tB26, tB27, tB28, tB29, tB30, tB31, tB32, tB33, tB34, tB35, tB36, tB37, tB38, tB39, tB40, tB41, tB42, tB43, tB44, tB45, tB46, tB47, tB48, tB49;
IF done THEN
LEAVE curloop;
END IF;
#count of good balls
SELECT (IFNULL(B1*tB1,0) + IFNULL(B2*tB2,0) + IFNULL(B3*tB3,0) + IFNULL(B4*tB4,0) + IFNULL(B5*tB5,0) + IFNULL(B6*tB6,0) + IFNULL(B7*tB7,0) + IFNULL(B8*tB8,0) + IFNULL(B9*tB9,0) + IFNULL(B10*tB10,0) + IFNULL(B11*tB11,0) + IFNULL(B12*tB12,0) + IFNULL(B13*tB13,0) + IFNULL(B14*tB14,0) + IFNULL(B15*tB15,0) + IFNULL(B16*tB16,0) + IFNULL(B17*tB17,0) + IFNULL(B18*tB18,0) + IFNULL(B19*tB19,0) + IFNULL(B20*tB20,0) + IFNULL(B21*tB21,0) + IFNULL(B22*tB22,0) + IFNULL(B23*tB23,0) + IFNULL(B24*tB24,0) + IFNULL(B25*tB25,0) + IFNULL(B26*tB26,0) + IFNULL(B27*tB27,0) + IFNULL(B28*tB28,0) + IFNULL(B29*tB29,0) + IFNULL(B30*tB30,0) + IFNULL(B31*tB31,0) + IFNULL(B32*tB32,0) + IFNULL(B33*tB33,0) + IFNULL(B34*tB34,0) + IFNULL(B35*tB35,0) + IFNULL(B36*tB36,0) + IFNULL(B37*tB37,0) + IFNULL(B38*tB38,0) + IFNULL(B39*tB39,0) + IFNULL(B40*tB40,0) + IFNULL(B41*tB41,0) + IFNULL(B42*tB42,0) + IFNULL(B43*tB43,0) + IFNULL(B44*tB44,0) + IFNULL(B45*tB45,0) + IFNULL(B46*tB46,0) + IFNULL(B47*tB47,0) + IFNULL(B48*tB48,0) + IFNULL(B49*tB49,0)) INTO winBallsCount;
SELECT
COUNT(ls.Prize)
INTO
settingsCount
FROM
LotterySettings AS ls
JOIN
Players AS p
ON
ls.CountryCode = IFNULL((SELECT DISTINCT CountryCode FROM `LotterySettings` WHERE CountryCode=p.Currency),'RU')
WHERE
p.Id = ticketPlayerId
AND
ls.BallsCount = winBallsCount
AND
ls.Gold = ticketIsGold
LIMIT 1;
IF (settingsCount>0) THEN
#select win and currency for player
SELECT
ls.Prize,ls.Currency
INTO
winTicketWin, winTicketWinCurrency
FROM
LotterySettings AS ls
JOIN
Players AS p
ON
ls.CountryCode = IFNULL((SELECT DISTINCT CountryCode FROM `LotterySettings` WHERE CountryCode=p.Currency),'RU')
WHERE
p.Id = ticketPlayerId
AND
ls.BallsCount = winBallsCount
AND
ls.Gold = ticketIsGold
LIMIT 1;
#inserting lotteryticket to archive
INSERT INTO LotteryTicketsArchive
(Id, LotteryId, PlayerId, Combination, DateCreated, TicketNum, TicketWin, TicketWinCurrency, IsGold)
VALUES
(ticketId, LotteryId, ticketPlayerId, ticketCombination, ticketDateCreated, ticketTicketNum, winTicketWin, winTicketWinCurrency, ticketIsGold);
#inserting into memory temp table
IF (winTicketWinCurrency='POINT') THEN
INSERT INTO LotteryTmp
(playerId, points)
VALUES
(ticketPlayerId, winTicketWin)
ON DUPLICATE KEY UPDATE
points=points+winTicketWin;
ELSE
INSERT INTO LotteryTmp
(playerId, money)
VALUES
(ticketPlayerId, winTicketWin)
ON DUPLICATE KEY UPDATE
money=money+winTicketWin;
END IF;
ELSE
INSERT INTO LotteryTicketsArchive
(Id, LotteryId, PlayerId, Combination, DateCreated, TicketNum, TicketWin, TicketWinCurrency, IsGold)
VALUES
(ticketId, LotteryId, ticketPlayerId, ticketCombination, ticketDateCreated, ticketTicketNum, 0, '', ticketIsGold);
INSERT IGNORE INTO LotteryTmp
(playerId, points, money)
VALUES
(ticketPlayerId, 0, 0);
END IF;
END LOOP;
#for not showing warning of fetch
SELECT
lt.Id
INTO
ticketId
FROM
LotteryTickets AS lt
LIMIT 1;
CLOSE rCursor;
#save transactions and update players balance and statistic
CALL saveLottery(lotteryDate, LotteryId);
CALL cleanLottery(LotteryId);
CALL applyReferralsPay();
CALL saveReferralsPay(lotteryDate, LotteryId);
#delete temp
DELETE FROM LotteryTmp;
END;

DROP PROCEDURE IF EXISTS `saveLottery`;
CREATE PROCEDURE `saveLottery`(IN lotteryDate INT, IN lotteryId INT)
BEGIN
DECLARE done INT DEFAULT 0;
DECLARE playerid INT;
DECLARE playerMoney FLOAT;
DECLARE playerPoints FLOAT;
DECLARE playerCurrency INT;
DECLARE currencyCoef FLOAT;
DECLARE currencyRate INT;
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
p.Points,p.Money,mc.Currency,mcu.Coefficient,mcu.Rate
INTO
balPoints,balMoney,playerCurrency,currencyCoef,currencyRate
FROM
Players AS p
JOIN
MUICountries as mc
ON
mc.Code = IFNULL((SELECT DISTINCT CountryCode FROM `LotterySettings` WHERE CountryCode=p.Currency),'RU')
JOIN
MUICurrency as mcu
ON
mcu.Id = mc.Currency
WHERE
p.Id = playerid;

IF (playerMoney>0) THEN
INSERT INTO Transactions
(PlayerId, Currency, SUM, DATE, Balance, CurrencyId, Equivalent, Description, ObjectType, ObjectId)
VALUES
( playerid, 'MONEY', playerMoney, lotteryDate, balMoney, playerCurrency, playerMoney/currencyCoef, 'Выигрыш в розыгрыше', 'Lottery', lotteryId);
END IF;
IF (playerPoints>0) THEN
INSERT INTO Transactions
(PlayerId, Currency, SUM, DATE, Balance, CurrencyId, Equivalent, Description, ObjectType, ObjectId)
VALUES
( playerid, 'POINT', playerPoints, lotteryDate, balPoints, 0, (playerPoints/(currencyCoef*currencyRate)), 'Выигрыш в розыгрыше', 'Lottery', lotteryId);
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

DROP PROCEDURE IF EXISTS `saveReferralsPay`;
CREATE PROCEDURE `saveReferralsPay`(IN lotteryDate INT, IN lotteryId INT)
BEGIN
#need for cursor
DECLARE done INT DEFAULT 0;
#for player balance
DECLARE playerCounter INT;
#tmp table
DECLARE tmpPlayerId INT;
DECLARE tmpReferralsProfit FLOAT;
DECLARE tmpReferralPay FLOAT;
#player
DECLARE balPoints FLOAT;
DECLARE currencyCoef FLOAT;
DECLARE currencyRate INT;
#cursor for all in tmp table
DECLARE rCursor CURSOR FOR
SELECT
ltmp.playerId,
ltmp.referralsProfit,
ltmp.referralPay
FROM
`LotteryTmp` AS ltmp;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET done=1;
#for all in tmp table
OPEN rCursor;
curloop :
LOOP
FETCH rCursor INTO tmpPlayerId, tmpReferralsProfit, tmpReferralPay;
IF done THEN
LEAVE curloop;
END IF;
UPDATE `Players`
SET
ReferralsProfit = ReferralsProfit + tmpReferralsProfit,
ReferralPay = ReferralPay + tmpReferralPay,
Points = Points + tmpReferralsProfit
WHERE Id = tmpPlayerId;
IF (tmpReferralsProfit>0) THEN
SELECT
COUNT(p.Id)
INTO
playerCounter
FROM
Players AS p
JOIN
MUICountries as mc
ON
mc.Code = IFNULL((SELECT DISTINCT CountryCode FROM `LotterySettings` WHERE CountryCode=p.Currency),'RU')
JOIN
MUICurrency as mcu
ON
mcu.Id = mc.Currency
WHERE
p.Id = tmpPlayerId;
IF (playerCounter>0) THEN
SELECT
p.Points,mcu.Coefficient,mcu.Rate
INTO
balPoints,currencyCoef,currencyRate
FROM
Players AS p
JOIN
MUICountries as mc
ON
mc.Code = IFNULL((SELECT DISTINCT CountryCode FROM `LotterySettings` WHERE CountryCode=p.Currency),'RU')
JOIN
MUICurrency as mcu
ON
mcu.Id = mc.Currency
WHERE
p.Id = tmpPlayerId;
INSERT INTO Transactions
(PlayerId, Currency, SUM, DATE, Balance, CurrencyId, Equivalent, Description, ObjectType, ObjectId)
VALUES
( tmpPlayerId, 'POINT', tmpReferralsProfit, lotteryDate, balPoints, 0, (tmpReferralsProfit/(currencyCoef * currencyRate)), 'Начисление за активность рефералов', 'Referrals', lotteryId);
END IF;
END IF;
END LOOP;
#for not showing warning of fetch
SELECT
ltmp.playerId
INTO
tmpPlayerId
FROM
`LotteryTmp` AS ltmp
LIMIT 1;
CLOSE rCursor;
END;
