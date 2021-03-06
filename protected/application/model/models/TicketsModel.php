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

    public function getCountUnplayedTickets($id=0)
    {
        return $this->getProcessor()->getCountUnplayedTickets($id);
    }

    public function getAllUnplayedTickets($id=0)
    {
        return $this->getProcessor()->getAllUnplayedTickets($id);
    }

}