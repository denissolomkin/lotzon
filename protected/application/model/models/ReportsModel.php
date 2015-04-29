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

    public function getMoneyOrders($dateFrom,$dateTo,$status)
    {
        return $this->getProcessor()->getMoneyOrders($dateFrom,$dateTo,$status);
    }

    public function getUserRegistrations($dateFrom,$dateTo,$status)
    {
        return $this->getProcessor()->getUserRegistrations($dateFrom,$dateTo,$status);
    }

    public function getUserReviews($dateFrom,$dateTo,$status)
    {
        return $this->getProcessor()->getUserReviews($dateFrom,$dateTo,$status);
    }

    public function getOnlineGames($dateFrom,$dateTo,$status)
    {
        return $this->getProcessor()->getOnlineGames($dateFrom,$dateTo,$status);
    }

    public function getShopOrders($dateFrom,$dateTo,$status)
    {
        return $this->getProcessor()->getShopOrders($dateFrom,$dateTo,$status);
    }
}