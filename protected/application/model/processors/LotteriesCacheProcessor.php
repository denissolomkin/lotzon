<?php

Application::import(PATH_APPLICATION . 'model/processors/BaseCacheProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/LotteriesDBProcessor.php');

class LotteriesCacheProcessor extends BaseCacheProcessor
{

    const LOTTERIES_LIST_KEY   = "lotteries::list";
    const LOTTERIES_WINNERS_KEY = "lotteries::winners";
    const LOTTERIES_MONEY_KEY   = "lotteries::money";

    public function init()
    {
        $this->setBackendProcessor(new LotteriesDBProcessor());
    }


    public function publish(Entity $lottery)
    {
        return $this->getBackendProcessor()->publish($lottery);
    }

    public function getLastPublishedLottery()
    {
        return current($this->getPublishedLotteriesList(1));
    }

    public function getPublishedLotteriesList($limit = null, $offset = 0)
    {
        if (($list = Cache::init()->get(self::LOTTERIES_LIST_KEY)) === false) {

            $list = $this->getBackendProcessor()->getPublishedLotteriesList(null, null);
            Cache::init()->set(self::LOTTERIES_LIST_KEY, $list);
        }

        return array_slice($list,$offset,$limit,true);
    }

    public function getPlayerPlayedLotteries($playerId, $limit = 0, $offset = 0)
    {
        return $this->getBackendProcessor()->getPlayerPlayedLotteries($playerId, $limit, $offset);
    }

    public function getPlayerHistory($playerId, $limit = 0, $offset = 0)
    {
        return $this->getBackendProcessor()->getPlayerHistory($playerId, $limit, $offset);
    }

    public function getLotteryDetails($lotteryId)
    {

        $list = $this->getPublishedLotteriesList();
        return isset($list[$lotteryId]) ? $list[$lotteryId] : array();

    }

    public function getDependentLottery($lotteryId, $dependancy)
    {

        $lottery = null;
        $list = $this->getPublishedLotteriesList();
        $max = reset($list)->getId();

        do {

            $dependancy == 'next' ? ++$lotteryId : --$lotteryId;

            if($lotteryId <= 0){
                $lottery = reset($list);
            } elseif($lotteryId >= $max) {
                $lottery = end($list);
            } elseif(isset($list[$lotteryId])) {
                $lottery = $list[$lotteryId];
            }

        } while(!$lottery);

        return $lottery;

    }

    public function getWinnersCount()
    {

        if (($winners = Cache::init()->get(self::LOTTERIES_WINNERS_KEY)) === false) {

            $winners = $this->getBackendProcessor()->getWinnersCount();
            Cache::init()->set(self::LOTTERIES_WINNERS_KEY, $winners);

        }

        return $winners;
    }

    public function getMoneyTotalWin()
    {

        if (($count = Cache::init()->get(self::LOTTERIES_MONEY_KEY)) === false) {

            $money = $this->getBackendProcessor()->getMoneyTotalWin();
            Cache::init()->set(self::LOTTERIES_MONEY_KEY, $money);

        }

        return $money;
    }


    public function recache()
    {
        $list = $this->getBackendProcessor()->getPublishedLotteriesList(null, null);
        Cache::init()->set(self::LOTTERIES_LIST_KEY, $list);

        $winners = $this->getBackendProcessor()->getWinnersCount();
        Cache::init()->set(self::LOTTERIES_WINNERS_KEY, $winners);

        $money = $this->getBackendProcessor()->getMoneyTotalWin();
        Cache::init()->set(self::LOTTERIES_MONEY_KEY, $money);

        return true;
    }

}