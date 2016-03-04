<?php

class Lottery extends Entity
{
    private $_id = 0;
    private $_combination = array();
    private $_date        = null;

    private $_winnersCount = 0;
    private $_moneyTotal   = 0;
    private $_pointsTotal   = 0;
    private $_ballsTotal   = array();

    protected $_playersCount     = 0;
    protected $_playersCountIncr = 0;
    protected $_winnersCountIncr = 0;
    protected $_ballsTotalIncr   = array();

    protected $_prizes           = array();
    protected $_prizesGold       = array();

    private $_ready = false;

    public $playerPlayed = false;


    public function init()
    {
        $this->setModelClass('LotteriesModel');
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


    public function setCombination($combination)
    {
        $this->_combination = $combination;

        return $this;
    }

    public function getCombination()
    {
        return $this->_combination;
    }


    public function setBallsTotal($ballsTotal)
    {
        $this->_ballsTotal = $ballsTotal;

        return $this;
    }

    public function getBallsTotal()
    {
        return $this->_ballsTotal;
    }

    public function setDate($dateCreated)
    {
        $this->_date = $dateCreated;

        return $this;
    }

    public function getDate($format = null)
    {
        $date = $this->_date;

        if (!is_null($format)) {
            $date = date($format, $this->_date);
        }

        return $date;
    }

    public function setWinnersCount($winnersCount)
    {
        $this->_winnersCount = $winnersCount;

        return $this;
    }

    public function getWinnersCount()
    {
        return $this->_winnersCount;
    }

    public function setMoneyTotal($moneyTotal)
    {
        $this->_moneyTotal = $moneyTotal;

        return $this;
    }

    public function getMoneyTotal()
    {
        return $this->_moneyTotal;
    }

    public function setReady($ready)
    {
        $this->_ready = $ready;

        return $this;
    }

    public function getReady()
    {
        return $this->_ready;
    }

    public function setPointsTotal($pointsTotal)
    {
        $this->_pointsTotal = $pointsTotal;

        return $this;
    }

    public function getPointsTotal()
    {
        return $this->_pointsTotal;
    }

    public function validate($action, array $params = array())
    {
        switch ($action) {
            case 'create' :
                if (!$this->getCombination() || !is_array($this->getCombination()) || count($this->getCombination()) != 6) {
                    throw new EntityException("INVALID_COMBINATION", 400);
                }
                foreach ($this->getCombination() as $num) {
                    if (!is_numeric($num) || $num < 1 || $num > 49) {
                        throw new EntityException("INVALID_COMBINATION", 400);
                    }
                }
            break;
            case 'update' :
                if (!$this->getId()) {
                    throw new EntityException("EMPTY_LOTTERY_ID", 400);
                }
                if (!$this->getCombination() || !is_array($this->getCombination()) || count($this->getCombination()) != 6) {
                    throw new EntityException("INVALID_COMBINATION", 400);
                }
                foreach ($this->getCombination() as $num) {
                    if (!is_numeric($num) || $num < 1 || $num > 49) {
                        throw new EntityException("INVALID_COMBINATION", 400);
                    }
                }
                break;
            case 'publish' :
                if (!$this->getId()) {
                    throw new EntityException("EMPTY_LOTTERY_ID", 400);
                }
            break;
            default :
                throw new EntityException("Object validation failed", 500);
            break;
        }

        return true;
    }

    public function formatFrom($from, $data) {
        if ($from == 'DB') {
            $this->setId($data['Id'])
                 ->setDate($data['Date'])
                 ->setCombination(@unserialize($data['Combination']))
                 ->setReady($data['Ready'])
                 ->setPlayersCount($data['PlayersCount'])
                 ->setPlayersCountIncr($data['PlayersCountIncr'])
                 ->setWinnersCount($data['WinnersCount'])
                 ->setWinnersCountIncr($data['WinnersCountIncr'])
                 ->setMoneyTotal($data['MoneyTotal'])
                 ->setPointsTotal($data['PointsTotal'])
                 ->setBallsTotal(@unserialize($data['BallsTotal']))
                 ->setBallsTotalIncr(@unserialize($data['BallsTotalIncr']))
                 ->setPrizes(@unserialize($data['Prizes']))
                 ->setPrizesGold(@unserialize($data['PrizesGold']));
        }

        return $this;
    }

    public function exportTo($to)
    {
        switch ($to) {
            case 'list':
                $ret = array(
                    'id'           => $this->getId(),
                    'date'         => $this->getDate(),
                    'combination'  => $this->getCombination(),
                );
                break;
        }

        return $ret;
    }

    public function publish()
    {
        $this->setReady(true);
        $this->validate('publish');
        $model = $this->getModelClass();

        $model::instance()->publish($this);

        return $this;
    }
}