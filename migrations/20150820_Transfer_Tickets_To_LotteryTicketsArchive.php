call isCol('LotteryTickets','IsGold',"SELECT null","
INSERT INTO LotteryTicketsArchive (Id, LotteryId, PlayerId, Combination, DateCreated, TicketNum, TicketWin, TicketWinCurrency) 
SELECT Id, LotteryId, PlayerId, Combination, DateCreated, TicketNum, TicketWin, TicketWinCurrency FROM LotteryTickets WHERE LotteryTickets.LotteryId>0 AND LotteryTickets.Id > IFNULL((SELECT Id FROM LotteryTicketsArchive ORDER BY Id DESC LIMIT 1),0)
");