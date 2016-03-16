SET NAMES 'utf8' COLLATE 'utf8_general_ci';
DROP PROCEDURE IF EXISTS `applyReferralsPay`;
CREATE PROCEDURE `applyReferralsPay`()
BEGIN
#need for cursor
DECLARE done INT DEFAULT 0;
#tmp table
DECLARE tmpPlayerId INT;
DECLARE tmpPoints FLOAT;
DECLARE tmpMoney FLOAT;
#refs
DECLARE ref1Id INT;
DECLARE ref2Id INT;
#cursor for all in tmp table
DECLARE rCursor CURSOR FOR
SELECT
ltmp.playerId,
ltmp.points,
ltmp.money
FROM
`LotteryTmp` AS ltmp;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET done=1;
#for all in tmp table
OPEN rCursor;
curloop :
LOOP
FETCH rCursor INTO tmpPlayerId, tmpPoints, tmpMoney;
IF done THEN
LEAVE curloop;
END IF;
SELECT
p.ReferalId AS ref1,
(SELECT p2.ReferalId AS ref2 FROM `Players` AS p2 WHERE p2.Id = ref1)
INTO
ref1Id, ref2Id
FROM
`Players` AS p
WHERE
p.Id = tmpPlayerId;
IF (ref1Id>0) THEN
INSERT INTO `LotteryTmp`
(playerId, referralsProfit)
VALUES
(ref1Id, 1)
ON DUPLICATE KEY UPDATE
referralsProfit = referralsProfit + 1;
INSERT INTO `LotteryTmp`
(playerId, referralPay)
VALUES
(tmpPlayerId, 1)
ON DUPLICATE KEY UPDATE
referralPay = referralPay + 1;
END IF;
IF (ref2Id>0) THEN
INSERT INTO `LotteryTmp`
(playerId, referralsProfit)
VALUES
(ref2Id, 0.5)
ON DUPLICATE KEY UPDATE
referralsProfit = referralsProfit + 0.5;
INSERT INTO `LotteryTmp`
(playerId, referralPay)
VALUES
(ref1Id, 0.5)
ON DUPLICATE KEY UPDATE
referralPay = referralPay + 0.5;
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
