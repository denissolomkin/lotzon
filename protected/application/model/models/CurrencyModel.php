<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/entities/Currency.php');
Application::import(PATH_APPLICATION . 'model/processors/CurrencyDBProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/CurrencyCacheProcessor.php');

class CurrencyModel extends Model
{
    public function init()
    {
        parent::init();

        $this->setProcessor(Config::instance()->cacheEnabled ? new CurrencyCacheProcessor() : new CurrencyDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function getList()
    {
        return $this->getProcessor()->getList();
    }

    public function fetch(Entity $currency) {

        try {
            $currency = $this->getProcessor()->fetch($currency);
        } catch (ModelException $e) {

            if($e->getCode()=='404')
                $currency->setId(1)
                    ->setCoefficient(34)
                    ->setRate(3)
                    ->setCode('RUB')
                    ->setTitle(array('many'=>'рублей','iso'=>'руб'));
            else
                throw new EntityException("Model Error", 500);
        }

        return $currency;
    }

}
