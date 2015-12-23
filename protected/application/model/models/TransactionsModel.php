<?php

Application::import(PATH_APPLICATION . 'model/processors/TransactionsDBProcessor.php');

class TransactionsModel extends Model
{
    public function init()
    {
        $this->setProcessor(new TransactionsDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function playerPointsHistory($playerId, $limit = 0, $offset = 0, $dateFrom = null, $dateTo = null)
    {
        return $this->getProcessor()->playerPointsHistory($playerId, $limit, $offset, $dateFrom, $dateTo);
    }


    public function playerMoneyHistory($playerId, $limit = 0, $offset = 0, $dateFrom = null, $dateTo = null)
    {
        return $this->getProcessor()->playerMoneyHistory($playerId, $limit, $offset, $dateFrom, $dateTo);
    }
}