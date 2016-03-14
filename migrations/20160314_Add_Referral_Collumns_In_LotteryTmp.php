call addCol('LotteryTmp','referralsProfit','FLOAT(9,2) DEFAULT 0.00 NOT NULL AFTER `money`');
call addCol('LotteryTmp','referralPay','FLOAT(9,2) DEFAULT 0.00 NOT NULL AFTER `referralsProfit`');
