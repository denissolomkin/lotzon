<?php

class GameSettings
{
    const CURRENCY_POINT = 'POINT';
    const CURRENCY_MONEY = 'MONEY';

    private $_model = null;

    private $_countryPrizes = array();
    private $_countryTotals = array();
    private $_gameTimes = array(); 

    public function __construct() 
    {
        $this->_model = GameSettingsModel::instance();
    }


    public function setTotalWinSum($country, $sum, $jackpot = false)
    {
        $this->_countryTotals[$country]['sum'] = $sum;
        $this->_countryTotals[$country]['isJackpot'] = $jackpot;

        return $this;
    }

    public function setPrizes($country, array $prizes)
    {
        if (count($prizes)) {
            if (!isset($this->_countryPrizes[$country])) {
                $this->_countryPrizes[$country] = array();    
            }

            foreach ($prizes as $ballsCount => $prize) {
                if (!empty($prize['ballsCount'])) {
                    $ballsCount = $prize['ballsCount'];
                }
                $prize['currency'] = strtoupper($prize['currency']);

                if (!in_array($prize['currency'], array(self::CURRENCY_MONEY, self::CURRENCY_POINT))) {
                    throw new GameSettingsException("Invalid prize internal currency", 400);
                }

                $this->_countryPrizes[$country][$ballsCount] = array(
                    'sum' => $prize['sum'],
                    'currency' => $prize['currency'],
                );
            }
        }

        return $this;
    }

    public function getTotalWinSum($country = null)
    {   
        if (!empty($country)) {
            return !empty($this->_countryTotals[$country]) ? $this->_countryTotals[$country] : array();
        }

        return $this->_countryTotals;
    }

    public function getPrizes($country = null)
    {
        if (!empty($country)) {
            return $this->_countryPrizes[$country];
        }

        return $this->_countryPrizes;
    }

    public function addGameTime($time) 
    {
        if (!is_numeric($time)) {
            $time = strtotime($time, 0);
        }

        $this->_gameTimes[] = $time;
    }

    public function getGameTimes($nearest = false)
    {
        // sort dates asc
        sort($this->_gameTimes, SORT_NUMERIC);
        if ($nearest) {
            $now = strtotime(date('H:i'), 0);
            $nearest = 0;
            foreach ($this->_gameTimes as $time) {
                if ($time > $now) {
                    $nearest = $time;
                    break;
                }
            }
            if (!$nearest) {
                $nearest = array_shift($this->_gameTimes) + 86400;
            }
            return $nearest;
        }

        return $this->_gameTimes;
    }

    public function getNearestGame()
    {
        return $this->getGameTimes(true);
    }

    public function validate()
    {
        $times = $this->getGameTimes();
        if (!count($times)) {
            throw new GameSettingsException("At least one time point for lottery must be specified", 400);
        }
    }

    public function saveSettings()
    {
        $this->validate();
        try {
            $this->_model->saveSettings($this);
        } catch (ModelException $e) {
            throw new GameSettingsException($e->getMessage(), $e->getCode());
        }

        return $this;
    }
}

class GameSettingsException extends Exception 
{

}