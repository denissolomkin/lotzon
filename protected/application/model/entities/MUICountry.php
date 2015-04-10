<?php

Application::import(PATH_APPLICATION . 'model/Entity.php');

class Country extends Entity
{
    private $_id          = 0;
    private $_code        = '';
    private $_lang        = '';
    private $_currency    = 0;
 
    public function init()
    {
        $this->setModelClass('CountriesModel');
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

    public function setId($int)
    {
        $this->_id = (int) $int;

        return $this;
    }

    public function getId()
    {
        return $this->_id;
    }


    public function setLang($char)
    {
        $this->_lang = $char;

        return $this;
    }

    public function getLang()
    {
        return $this->_lang;
    }

    public function setCurrency($array) 
    {
        $this->_currency = $array;

        return $this;
    }

    public function getCurrency()
    {
        return $this->_currency;
    }

    public function loadCurrency()
    {
        $currency = new Currency();
        try {
            $currency->setId($this->getCurrency())->fetch();
        } catch (EntityException $e) {
        }
        return $currency;
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
                $this->isValidCode();
                $this->isValidCurrency();
                $this->isValidLang();
            break;
            default:
                throw new EntityException("Object does not pass validation", 400);
            break;
        }

        return true;
    }

    private function isValidCode($throwException = true)
    {
        if ($this->getCode()){ // && preg_match('/^('.(implode('|',$this->_countries)).')$/i', $this->getCode())
            return true;
        }

        if ($throwException) {
            throw new EntityException('Invalid country code format', 400);
        }

        return false;
    }

    private function isValidCurrency($throwException = true)
    {
        if ($this->getCurrency()) {
            return true;
        }

        if ($throwException) {
            throw new EntityException('Empty currency', 400);
        }

        return false;
    }

    private function isValidLang($throwException = true) 
    {
        if ($this->getLang()){ // && in_array($this->getLang(), $this->_countries)
            return true;
        }

        if ($throwException) {
            throw new EntityException('Empty or invalid country lang setting', 400);
        }

        return false;   
    }


    public function formatFrom($from, $data) {
        switch ($from) {
            case 'DB' :
                $this->setId($data['Id'])
                     ->setCode($data['Code'])
                     ->setLang($data['Lang'])
                     ->setCurrency($data['Currency']);
            break;
        }

        return $this;
    }
}