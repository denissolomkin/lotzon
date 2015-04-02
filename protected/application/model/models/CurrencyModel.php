<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/entities/Currency.php');
Application::import(PATH_APPLICATION . 'model/processors/CurrencyDBProcessor.php');

class CurrencyModel extends Model
{
    public function init()
    {
        parent::init();

        $this->setProcessor(new CurrencyDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function getList()
    {
        $currency = $this->getProcessor()->getList();

        return $currency;
    }

    public function getCurrency($code)
    {
        $currency = $this->getList();

        if(isset($currency[$code])) {
            return $currency[$code];
        } elseif(is_array($currency) && !empty($currency)) {
            return reset($currency);
        } else {$default = new Currency();}

        return $default;
    }

}
