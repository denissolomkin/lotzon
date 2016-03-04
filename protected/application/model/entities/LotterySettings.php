<?php

class LotterySettings
{
    const CURRENCY_POINT = 'POINT';
    const CURRENCY_MONEY = 'MONEY';
    const TOTAL_TICKETS  = 8;
    const TOTAL_BALLS    = 49;
    const REQUIRED_BALLS = 6;

    private $_model = null;

    private   $_countryPrizes     = array();
    protected $_countryGoldPrizes = array();
    private   $_total             = 0;
    private   $_jackpot           = false;
    private   $_gameTimes         = array();
    private   $_gameSettings      = array();
    protected $_gameIncrements    = array();

    public function __construct()
    {
        $this->_model = LotterySettingsModel::instance();
    }

    public function setGameIncrements($inc)
    {
        $this->_gameIncrements = $inc;

        return $this;
    }

    public function getGameIncrements()
    {
        return $this->_gameIncrements;
    }

    public function setTotalWinSum($sum)
    {
        $this->_total = $sum;

        return $this;
    }

    public function getTotalWinSum()
    {
        return $this->_total;
    }

    public function setJackpot($jackpot)
    {
        $this->_jackpot = $jackpot;
    }

    public function getJackpot()
    {
        return $this->_jackpot;
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
                    throw new LotterySettingsException("Invalid prize internal currency", 400);
                }

                $this->_countryPrizes[$country][$ballsCount] = array(
                    'sum'      => $prize['sum'],
                    'currency' => $prize['currency'],
                );
            }
        }

        return $this;
    }

    public function setGoldPrizes($country, array $prizes)
    {
        if (count($prizes)) {
            if (!isset($this->_countryGoldPrizes[$country])) {
                $this->_countryGoldPrizes[$country] = array();
            }

            foreach ($prizes as $ballsCount => $prize) {
                if (!empty($prize['ballsCount'])) {
                    $ballsCount = $prize['ballsCount'];
                }
                $prize['currency'] = strtoupper($prize['currency']);

                if (!in_array($prize['currency'], array(self::CURRENCY_MONEY, self::CURRENCY_POINT))) {
                    throw new LotterySettingsException("Invalid prize internal currency", 400);
                }

                $this->_countryGoldPrizes[$country][$ballsCount] = array(
                    'sum'      => $prize['sum'],
                    'currency' => $prize['currency'],
                );
            }
        }

        return $this;
    }

    public function getPrizes($country = null)
    {
        if (!empty($country)) {
            return $this->_countryPrizes[$country];
        }

        return $this->_countryPrizes;
    }

    public function getGoldPrizes($country = null)
    {
        if (!empty($country)) {
            return $this->_countryGoldPrizes[$country];
        }

        return $this->_countryGoldPrizes;
    }

    public function addLotterySettings($settings)
    {
        if (isset($settings['StartTime']) && !is_numeric($settings['StartTime'])) {
            $settings['StartTime'] = strtotime($settings['StartTime'], 0);
        }

        $this->_gameSettings[] = $settings;
    }

    public function getLotterySettings()
    {
        return $this->_gameSettings;
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
            $now     = strtotime(date('H:i'), 0);
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
        $times = $this->getLotterySettings();
        if (!count($times)) {
            throw new LotterySettingsException("At least one time point for lottery must be specified", 400);
        }
    }

    public function saveSettings()
    {
        $this->validate();
        try {
            $this->_model->saveSettings($this);
        } catch (ModelException $e) {
            throw new LotterySettingsException($e->getMessage(), $e->getCode());
        }

        return $this;
    }
}

class LotterySettingsException extends Exception
{

}