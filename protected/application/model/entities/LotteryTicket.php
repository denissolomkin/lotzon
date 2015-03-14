<?php

class LotteryTicket extends Entity
{
    private $_id = 0;

    private $_ticketNum   = 1;
    private $_playerId    = 0;
    private $_lotteryId   = 0;
    private $_combination = array();

    private $_dateCreated = null;

    private $_ticketWin = 0;
    private $_ticketWinCurrency = LotterySettings::CURRENCY_POINT;

    public function init()
    {
        $this->setModelClass('TicketsModel');
    }

    public function getTicketNum()
    {
        return $this->_ticketNum;
    }

    public function setTicketNum($num)
    {
        $this->_ticketNum = $num;

        return $this;
    }

    public function getTicketWin()
    {
        return $this->_ticketWin;
    }

    public function setTicketWin($Win)
    {
        $this->_ticketWin = $Win;

        return $this;
    }

    public function getTicketWinCurrency()
    {
        return $this->_ticketWinCurrency;
    }

    public function setTicketWinCurrency($currency)
    {
        $this->_ticketWinCurrency = $currency;

        return $this;
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

    public function setPlayerId($playerId)
    {
        $this->_playerId = $playerId;

        return $this;
    }

    public function getPlayerId()
    {
        return $this->_playerId;
    }

    public function setLotteryId($lotteryId)
    {
        $this->_lotteryId = $lotteryId;

        return $this;
    }

    public function getLotteryId()
    {
        return $this->_lotteryId;
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

    public function setDateCreated($dateCreated)
    {
        $this->_dateCreated = $dateCreated;

        return $this;
    }

    public function getDateCreated($format = null)
    {
        $date = $this->_dateCreated;

        if (!is_null($format)) {
            $date = date($format, $this->_dateCreated);
        }

        return $date;
    }

    public function validate($action, array $params = array())
    {
        switch ($action) {
            case 'create' :
                if (!$this->getPlayerId()) {
                    throw new EntityException("INVALID_PLAYER", 401);
                }

                if (!$this->getCombination() || !is_array($this->getCombination()) || count($this->getCombination()) != 6) {
                    throw new EntityException("INVALID_COMBINATION", 400);   
                }
                foreach ($this->getCombination() as $num) {
                    if (!is_numeric($num) || $num < 1 || $num > 49) {
                        throw new EntityException("INVALID_COMBINATION", 400);       
                    } 
                }
                $owner = new Player();
                $owner->setId($this->getPlayerId());
                $tiketsAlreadyFilled = TicketsModel::instance()->getPlayerUnplayedTickets($owner);
                if (count($tiketsAlreadyFilled) >= 5) {
                    throw new EntityException("LIMIT_EXCEEDED", 400);       
                }
                if (!empty($tiketsAlreadyFilled[$this->getTicketNum()])) {
                    throw new EntityException("ALREADY_FILLED", 400);          
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
                 ->setTicketNum($data['TicketNum'])
                 ->setTicketWin($data['TicketWin'])
                 ->setTicketWinCurrency($data['TicketWinCurrency'])
                 ->setPlayerId($data['PlayerId'])
                 ->setLotteryId($data['LotteryId'])
                 ->setDateCreated($data['DateCreated'])
                 ->setCombination(@unserialize($data['Combination']));
        }

        return $this;
    }
}