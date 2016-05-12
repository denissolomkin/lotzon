<?php

class Transaction extends Entity 
{
    protected $_id = 0;
    protected $_playerId = 0;
    protected $_currency = LotterySettings::CURRENCY_POINT;
    protected $_currencyId = 0;
    protected $_equivalent = 0;
    protected $_sum = 0;
    protected $_balance = 0;
    protected $_objectId = null;
    protected $_objectUid = null;
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

    public function export($to)
    {
        switch ($to) {
            case 'list':
                $ret = array(
                    'id'          => $this->getId(),
                    'date'        => $this->getDate(),
                    'description' => $this->getDescription(),
                    'sum'         => $this->getSum(),
                    'balance'     => $this->getBalance(),
                    'currencyId'  => $this->getCurrencyId(),
                );
                break;
        }

        return $ret;
    }

    public function formatFrom($from, $data)
    {
        switch ($from) {
            case 'DB':
                $this->setId($data['Id'])
                    ->setPlayerId($data['PlayerId'])
                    ->setSum($data['Sum'])
                    ->setBalance($data['Balance'])
                    ->setDescription($data['Description'])
                    ->setCurrency($data['Currency'])
                    ->setCurrencyId($data['CurrencyId'])
                    ->setDate($data['Date']);
                break;
            default:
                break;
        }
    }
}