call isCol('LotteryTickets','IsGold',"SELECT null","
INSERT INTO LotteryTicketsArchive ( LotteryId, PlayerId, Combination, DateCreated, TicketNum, TicketWin, TicketWinCurrency) SELECT LotteryId, PlayerId, Combination, DateCreated, TicketNum, TicketWin, TicketWinCurrency FROM LotteryTickets WHERE LotteryTickets.LotteryId>0
");