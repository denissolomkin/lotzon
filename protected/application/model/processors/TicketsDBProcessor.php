<?php
Application::import(PATH_INTERFACES . 'IProcessor.php');

class TicketsDBProcessor implements IProcessor
{
    public function create(Entity $ticket)
    {
		$comb	= $ticket->getCombination();
		$filds	= array();

		foreach((array)$comb as $ball)
		{
			$filds[]= 'B'.((int)$ball);
		}

		$sql = "INSERT INTO `LotteryTickets`	(`PlayerId`, `Combination`, `DateCreated`, `TicketNum`, ".implode(',', $filds).")
				VALUES							(:playerid, :combination, :dc, :tn, 1, 1, 1, 1, 1, 1)";

		try {
            DB::Connect()->prepare($sql)->execute(array(
                ':playerid'    => $ticket->getPlayerId(),
                ':combination' => @serialize($comb),
                ':dc'          => time(),
                ':tn'          => $ticket->getTicketNum(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $ticket->setId(DB::Connect()->lastInsertId());

        return $ticket;
    }

    public function update(Entity $ticket)
    {
        return $ticket;
    }

    public function delete(Entity $ticket)
    {
        return true;
    }

    public function fetch(Entity $ticket)
    {
        return $ticket;
    }

    public function getPlayerTickets(Player $player, $lotteryId=null)
    {

        $where = array("`PlayerId` = :plid");
        if(isset($lotteryId))
            $where[] = "`LotteryId` = :lotid";

        $sql = "SELECT * FROM `LotteryTickets` WHERE ".implode(' AND ', $where)." ORDER BY `DateCreated` ASC";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ":plid" => $player->getId(),
                ":lotid" => $lotteryId
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $ticketsData = $sth->fetchAll();
        $tickets = array();

        foreach ($ticketsData as $ticketData) {
            $ticket = new LotteryTicket();

            $ticket->formatFrom('DB', $ticketData);
            $tickets[$ticket->getLotteryId()][$ticket->getTicketNum()] = $ticket;
        }

        if(isset($lotteryId))
            $tickets = array_shift($tickets);

        return $tickets;
    }

    public function getPlayerUnplayedTickets(Player $player, $lotteryId = 0)
    {

        $where = array("`PlayerId` = :plid");
        if(isset($lotteryId))
            $where[] = "`LotteryId` = :lotid";

        $sql = "SELECT * FROM `LotteryTickets` WHERE ".implode(' AND ', $where)." ORDER BY `DateCreated` ASC";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ":plid" => $player->getId(),
                ":lotid" => $lotteryId
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $unplayedTicketsData = $sth->fetchAll();
        $unplayedTickets = array();

        foreach ($unplayedTicketsData as $ticketData) {
            $ticket = new LotteryTicket();

            $ticket->formatFrom('DB', $ticketData);
            $unplayedTickets[$ticket->getTicketNum()] = $ticket;
        }

        return $unplayedTickets;
    }

    public function getAllUnplayedTickets($id=0)
    {
        $sql = "SELECT * FROM `LotteryTickets` WHERE `LotteryId` = :id";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(':id'=>$id));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $ticketsData = $sth->fetchAll();
        $tickets = array();

        foreach ($ticketsData as $ticketData) {
            $ticket = new LotteryTicket();

            $ticket->formatFrom('DB', $ticketData);
            $tickets[$ticket->getId()] = $ticket;
        }

        return $tickets;
    }

    public function getCountUnplayedTickets($id=0)
    {
        $sql = "SELECT COUNT(*) FROM `LotteryTickets` WHERE `LotteryId` = :id";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(':id'=>$id));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        return $sth->fetchColumn(0);
    }

}