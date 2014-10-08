<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/entities/Lottery.php');
Application::import(PATH_APPLICATION . 'model/processors/LotteriesModelDBProcessor.php');


class LotteriesModel extends Model
{
    public function init()
    {
        $this->setProcessor(new LotteriesModelDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function publish(Entity $lottery)
    {
        return $this->getProcessor()->publish($lottery);
    }

    public function getPublishedLotteriesList($limit, $offset = 0)
    {
        return $this->getProcessor()->getPublishedLotteriesList($limit, $offset);
    }

    public function getLastPlayedLottery()
    {
        return $this->getProcessor()->getLastPlayedLottery();
    }

    public function getPlayerPlayedLotteries($playerId, $limit = 0, $offset = 0)
    {
        return $this->getProcessor()->getPlayerPlayedLotteries($playerId, $limit, $offset);
    }

    public function getPlayerHistory($playerId, $limit = 0, $offset = 0)
    {
        return $this->getProcessor()->getPlayerHistory($playerId, $limit, $offset);   
    }

}