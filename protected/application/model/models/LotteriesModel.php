<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/entities/Lottery.php');
Application::import(PATH_APPLICATION . 'model/processors/LotteriesDBProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/LotteriesCacheProcessor.php');


class LotteriesModel extends Model
{
    public function init()
    {
        $this->setProcessor(Config::instance()->cacheEnabled ? new LotteriesCacheProcessor() : new LotteriesDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function publish(Entity $lottery)
    {
        return $this->getProcessor()->publish($lottery);
    }

    public function getLastPublishedLottery()
    {
        return $this->getProcessor()->getLastPublishedLottery();
    }

    public function getPublishedLotteriesList($limit, $offset = 0)
    {
        return $this->getProcessor()->getPublishedLotteriesList($limit, $offset);
    }

    public function getPlayerPlayedLotteries($playerId, $limit = 0, $offset = 0)
    {
        return $this->getProcessor()->getPlayerPlayedLotteries($playerId, $limit, $offset);
    }

    public function getPlayerHistory($playerId, $limit = 0, $offset = 0)
    {
        return $this->getProcessor()->getPlayerHistory($playerId, $limit, $offset);   
    }

    public function getLotteryDetails($lotteryId)
    {
        return $this->getProcessor()->getLotteryDetails($lotteryId);
    }

    public function getAllLotteriesDetails()
    {
        return $this->getProcessor()->getAllLotteriesDetails();
    }

    public function getDependentLottery($lotteryId, $dependancy) 
    {
        return $this->getProcessor()->getDependentLottery($lotteryId, $dependancy);
    }

    public function getWinnersCount()
    {
        return $this->getProcessor()->getWinnersCount();   
    }

    public function getMoneyTotalWin()
    {
        return $this->getProcessor()->getMoneyTotalWin();      
    }
}