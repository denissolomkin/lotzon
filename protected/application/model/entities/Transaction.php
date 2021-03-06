<?php

class Transaction extends Entity 
{
    protected $_id = 0;
    protected $_playerId = 0;
    protected $_currency = LotterySettings::CURRENCY_POINT;
    protected $_sum = 0;
    protected $_balance = 0;
    protected $_objectId = null;
    protected $_objectType = null;
    protected $_description = '';
    protected $_date = 0;

    public function init()
    {
        $this->setModelClass('TransactionsModel');
    }

    public function validate($action, $params = array())
    {
        return true;
    }
}