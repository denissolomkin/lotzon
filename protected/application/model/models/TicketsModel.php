<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/entities/LotteryTicket.php');
Application::import(PATH_APPLICATION . 'model/processors/TicketsDBProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/TicketsCacheProcessor.php');


class TicketsModel extends Model
{
    public function init()
    {
        //$this->setProcessor(Config::instance()->cacheEnabled ? new TicketsCacheProcessor() : new TicketsDBProcessor());
        $this->setProcessor(new TicketsDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function getPlayerTickets(Player $player, $lotteryId = null)
    {
        return $this->getProcessor()->getPlayerTickets($player, $lotteryId);
    }

    public function getPlayerUnplayedTickets(Player $player)
    {
        return $this->getProcessor()->getPlayerUnplayedTickets($player);
    }

    public function getCountUnplayedTickets($id = 0)
    {
        return $this->getProcessor()->getCountUnplayedTickets($id);
    }

    public function getAllUnplayedTickets($id = 0)
    {
        return $this->getProcessor()->getAllUnplayedTickets($id);
    }

    /**
     * Проверка доступности билета для пользователя
     *
     * @param $playerId
     * @param $ticketNumber
     */
    public function isAvailableTicket($playerId, $ticketNumber)
    {
        switch ($ticketNumber) {
            case 1:
            case 2:
            case 3:
                return true;
                break;
            case 4:
                $player = new Player();
                $player->setId($playerId)->fetch();
                if ($player->getGamesPlayed() >= 100) {
                    return true;
                }
                break;
            case 5:
                if (\PlayersModel::instance()->getReferralsCount($playerId) >= 10) {
                    return true;
                }
                break;
            case 6:
                if (\PlayersModel::instance()->getReferralsCount($playerId) >= 50) {
                    return true;
                }
                break;
            case 7:
                $player = new Player();
                $player->setId($playerId)->fetch();
                if ($player->getGoldTicket() > 0) {
                    return true;
                }
                $tickets = $this->getProcessor()->getUnplayedTickets($playerId);
                if (isset($tickets[8])) {
                    return true;
                }
                break;
            case 8:
                $player = new Player();
                $player->setId($playerId)->fetch();
                if ($player->getGoldTicket() > 0) {
                    return true;
                }
                break;
            default:
                return false;
        }
        return false;
    }

    /**
     * Список всех заполненных билетов на розыгрыши, которых ещё не было
     *
     * @param $playerId
     */
    public function getUnplayedTickets($playerId)
    {
        $uplayedTickets = $this->getProcessor()->getUnplayedTickets($playerId);
        $tickets = array();
        for ($i=1; $i<=8; $i++) {
            if (isset($uplayedTickets[$i])) {
                $tickets[$i] = $uplayedTickets[$i]->getCombination();
            } else {
                if ($this->isAvailableTicket($playerId, $i)) {
                    $tickets[$i] = null;
                } else {
                    $tickets[$i] = false;
                }
            }
        }
        return $tickets;
    }

    public function beginTransaction()
    {
        return $this->getProcessor()->beginTransaction();
    }

    public function commit()
    {
        return $this->getProcessor()->commit();
    }

    public function rollBack()
    {
        return $this->getProcessor()->rollBack();
    }

}