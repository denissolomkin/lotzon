<?php

Application::import(PATH_APPLICATION . 'model/Entity.php');

class Currency extends Entity
{
    protected $_id          = 0;
    protected $_code        = '';
    protected $_title       = array();
    protected $_iso         = '';
    protected $_coefficient = 0;
    protected $_rate        = 0;

    public function init()
    {
        $this->setModelClass('CurrencyModel');
    }

    public function getSettings()
    {
        return ($this->_title + array(
                'coefficient' => $this->getCoefficient(),
                'rate'        => $this->getRate(),
                'iso'         => $this->getIso(),
                'code'        => $this->getCode()
            ));
    }

    public function validate($event, $params = array())
    {
        switch ($event) {

            case 'update' :
            case 'create' :
                $this->isValidTitle();
                $this->isValidRate();
                $this->isValidCoefficient();
                break;

            case 'delete':
                break;

            case 'fetch' :
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

    public function formatFrom($from, $data)
    {
        switch ($from) {
            case 'DB':
                $this->setId($data['Id'])
                    ->setCode($data['Code'])
                    ->setTitle(unserialize($data['Title']))
                    ->setRate($data['Rate'])
                    ->setIso($data['Iso'])
                    ->setCoefficient($data['Coefficient']);
                break;

            case 'CLASS':
                $this->setId($data->getId())
                    ->setCode($data->getCode())
                    ->setTitle($data->getTitle())
                    ->setRate($data->getRate())
                    ->setIso($data->getIso())
                    ->setCoefficient($data->getCoefficient());
                break;
        }

        return $this;
    }
}