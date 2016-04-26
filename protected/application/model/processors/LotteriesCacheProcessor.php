<?php

Application::import(PATH_APPLICATION . 'model/processors/BaseCacheProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/LotteriesDBProcessor.php');

class LotteriesCacheProcessor extends BaseCacheProcessor
{

    const LOTTERIES_LIST_KEY    = "lotteries::list";
    const LOTTERIES_WINNERS_KEY = "lotteries::winners";
    const LOTTERIES_MONEY_KEY   = "lotteries::money";

    public function init()
    {
        $this->setBackendProcessor(new LotteriesDBProcessor());
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

    public function getLotteryDetails($lotteryId)
    {

        $list = $this->getPublishedLotteriesList();
        return isset($list[$lotteryId]) ? $list[$lotteryId] : array();

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

        if (($money = Cache::init()->get(self::LOTTERIES_MONEY_KEY)) === false) {

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