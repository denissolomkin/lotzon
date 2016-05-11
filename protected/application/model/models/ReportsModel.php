<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/processors/ReportsDBProcessor.php');


class ReportsModel extends Model
{
    public function init()
    {
        $this->setProcessor(new ReportsDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function updateMoneyOrders()
    {
        return $this->getProcessor()->updateMoneyOrders();
    }

    public function getMoneyOrders($dateFrom,$dateTo,$args)
    {
        return $this->getProcessor()->getMoneyOrders($dateFrom,$dateTo,$args);
    }

    public function getUserRegistrations($dateFrom,$dateTo,$args)
    {
        return $this->getProcessor()->getUserRegistrations($dateFrom,$dateTo,$args);
    }

    public function getUserReviews($dateFrom,$dateTo,$args)
    {
        return $this->getProcessor()->getUserReviews($dateFrom,$dateTo,$args);
    }

    public function getOnlineGames($dateFrom,$dateTo,$args)
    {
        return $this->getProcessor()->getOnlineGames($dateFrom,$dateTo,$args);
    }

    public function getTopOnlineGames($dateFrom,$dateTo,$args)
    {
        return $this->getProcessor()->getTopOnlineGames($dateFrom,$dateTo,$args);
    }

    public function getShopOrders($dateFrom,$dateTo,$args)
    {
        return $this->getProcessor()->getShopOrders($dateFrom,$dateTo,$args);
    }
    
    public function getBotWins($dateFrom,$dateTo,$args)
    {
        return $this->getProcessor()->getBotWins($dateFrom,$dateTo,$args);
    }

    public function getSlotsWins($dateFrom,$dateTo,$args)
    {
        return $this->getProcessor()->getSlotsWins($dateFrom,$dateTo,$args);
    }

    public function getLotteryWins($dateFrom,$dateTo,$args)
    {
        return $this->getProcessor()->getLotteryWins($dateFrom,$dateTo,$args);
    }

    public function getGoldTicketOrders($dateFrom,$dateTo,$args)
    {
        return $this->getProcessor()->getGoldTicketOrders($dateFrom,$dateTo,$args);
    }
}