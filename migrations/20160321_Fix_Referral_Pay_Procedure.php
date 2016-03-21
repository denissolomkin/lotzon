SET NAMES 'utf8' COLLATE 'utf8_general_ci';
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
WHERE
p.Id = tmpPlayerId;
IF (playerCounter>0) THEN
SELECT
p.Points
INTO
balPoints
FROM
Players AS p
WHERE
p.Id = tmpPlayerId;
INSERT INTO Transactions
(PlayerId, Currency, SUM, DATE, Balance, Description, ObjectType, ObjectId)
VALUES
( tmpPlayerId, 'POINT', tmpReferralsProfit, lotteryDate, balPoints, 'Начисление за активность рефералов', 'Referrals', lotteryId);
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
