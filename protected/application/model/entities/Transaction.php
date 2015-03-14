<?php

class Transaction extends Entity 
{
    private $_id = 0;
    private $_playerId = 0;
    private $_currency = LotterySettings::CURRENCY_POINT;
    private $_sum = 0;
    private $_balance = 0;
    private $_description = '';
    private $_date = 0;

    public function init()
    {
        $this->setModelClass('TransactionsModel');
    }   

    public function setId($id)
    {
        $this->_id = $id;

        return $this;
    }

    public function getId()
    {
        return $this->_id;
    }


    public function setBalance($balance)
    {
        $this->_balance = $balance;

        return $this;
    }

    public function getBalance()
    {
        return $this->_balance;
    }

    public function setPlayerId($playerId)
    {
        $this->_playerId = $playerId;

        return $this;
    }

    public function getPlayerId()
    {
        return $this->_playerId;
    }

    public function setCurrency($currency)
    {
        $this->_currency = $currency;

        return $this;
    }

    public function getCurrency()
    {
        return $this->_currency;
    }

    public function setSum($sum)
    {
        $this->_sum = $sum;

        return $this;
    }

    public function getSum()
    {
        return $this->_sum;
    }

    public function setDescription($description)
    {
        $this->_description = $description;

        return $this;
    }

    public function getDescription()
    {
        return $this->_description;
    }

    public function setDate($date)
    {
        $this->_date = $date;

        return $this;
    }

    public function getDate()
    {
        return $this->_date;
    }

    public function validate($action, $params = array())
    {
        return true;
    }
}