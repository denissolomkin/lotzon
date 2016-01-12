<?php

Application::import(PATH_APPLICATION . 'model/Entity.php');

class Currency extends Entity
{
    private $_id          = 0;
    private $_code        = '';
    private $_title       = array();
    private $_coefficient = 0;
    private $_rate        = 0;


    public function init()
    {
        $this->setModelClass('CurrencyModel');
    }

    public function setId($int)
    {
        if($int)
            $this->_id = (int) $int;

        return $this;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function setCode($char)
    {
        $this->_code = $char;
        return $this;
    }

    public function getCode()
    {
        return $this->_code;
    }

    public function setTitle($array)
    {
        $this->_title = $array;

        return $this;
    }

    public function getTitle($format=null)
    {

        if(isset($format)) {
            if(isset($this->_title[$format]) && $this->_title[$format] && $this->_title[$format]!='')
                $currency = $this->_title[$format];
            elseif(is_array($this->_title) && !empty($this->_title))
                $currency = reset($this->_title);
        } else
            $currency = $this->_title;

        return $currency;
    }

    public function setCoefficient($float)
    {
        $this->_coefficient = $float;

        return $this;
    }

    public function getCoefficient()
    {
        return $this->_coefficient;
    }

    public function setRate($int)
    {
        $this->_rate = (int) $int;

        return $this;
    }

    public function getRate()
    {
        return $this->_rate;
    }

    public function getSettings()
    {
        return ($this->_title + array(
                'coefficient' => $this->getCoefficient(),
                'rate' => $this->getRate(),
                'code' => $this->getCode()
            ));
    }


    public function validate($event, $params = array())
    {
        switch ($event) {
            case 'update' :

            break;

            case 'delete' :

            break;

            case 'fetch' :

            break;

            case 'create' :
                $this->isValidTitle();
                $this->isValidRate();
                $this->isValidCoefficient();
            break;
            default:
                throw new EntityException("Object does not pass validation", 400);
            break;
        }

        return true;
    }

    private function isValidTitle($throwException = true)
    {
        if (is_array($currencies=$this->getTitle()) && !empty($currencies) && isset($currencies['iso']) && !empty($currencies['iso'])) {
            foreach($currencies as &$currency)
                $currency=htmlspecialchars(strip_tags($currency));
            $this->setTitle($currencies);
            return true;
        }

        if ($throwException) {
            throw new EntityException('Empty currency', 400);
        }

        return false;
    }

    private function isValidCoefficient($throwException = true)
    {
        if ($this->getCoefficient() && is_numeric($this->getCoefficient())) {
            return true;
        }

        if ($throwException) {
            throw new EntityException('Empty or invalid coefficient setting', 400);
        }

        return false;
    }

    private function isValidRate($throwException = true)
    {
        if ($this->getRate() && is_numeric($this->getRate())) {
            return true;
        }

        if ($throwException) {
            throw new EntityException('Empty or invalid coefficient setting', 400);
        }

        return false;
    }

    public function formatFrom($from, $data) {
        switch ($from) {
            case 'DB' :
                $this->setId($data['Id'])
                     ->setCode($data['Code'])
                     ->setTitle(unserialize($data['Title']))
                     ->setRate($data['Rate'])
                     ->setCoefficient($data['Coefficient']);
            break;
            case 'CLASS' :
                $this->setId($data->getId())
                    ->setCode($data->getCode())
                    ->setTitle($data->getTitle())
                    ->setRate($data->getRate())
                    ->setCoefficient($data->getCoefficient());
                break;
        }

        return $this;
    }
}