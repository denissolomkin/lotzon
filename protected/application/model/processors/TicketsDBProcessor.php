<?php
Application::import(PATH_INTERFACES . 'IProcessor.php');

class TicketsDBProcessor implements IProcessor
{
    public function create(Entity $ticket)
    {
        $sql = "INSERT INTO `LotteryTickets` (`PlayerId`, `Combination`, `DateCreated`, `TicketNum`) VALUES (:playerid, :combination, :dc, :tn)";

        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':playerid'    => $ticket->getPlayerId(),
                ':combination' => @serialize($ticket->getCombination()),
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

    public function getPlayerTickets(Player $player)
    {
        $sql = "SELECT * FROM `LotteryTickets` WHERE `PlayerId` = :plid ORDER BY `DateCreated` ASC";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ":plid" => $player->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $ticketsData = $sth->fetchAll();
        $tickets = array();

        foreach ($ticketsData as $ticketData) {
            $ticket = new LotteryTicket();

            $ticket->formatFrom('DB', $ticketData);
            $tickets[$ticket->getTicketNum()] = $ticket;
        }

        return $tickets;
    }

    public function getAllUnplayedTickets() 
    {
        $sql = "SELECT * FROM `LotteryTickets` WHERE `LotteryId` = 0";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute();
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

    public function getPlayerLotteryTickets($lotteryId, $playerId)
    {
        $sql = "SELECT * FROM `LotteryTickets` WHERE `PlayerId` = :plid AND `LotteryId` = :lotid ORDER BY `DateCreated` ASC";
        
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ":plid"    => $playerId,
                ":lotid"   => $lotteryId,
            ));
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
}