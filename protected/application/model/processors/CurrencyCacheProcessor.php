<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/BaseCacheProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/CurrencyDBProcessor.php');

class CurrencyCacheProcessor extends BaseCacheProcessor implements IProcessor
{

    const LIST_CACHE_KEY = "currency::list";

    public function init()
    {
        $this->setBackendProcessor(new CurrencyDBProcessor());
    }

    public function create(Entity $currency)
    {
        $currency = $this->getBackendProcessor()->create($currency);
        $this->getList(true);
        return $currency;
    }

    public function getList($recache=false)
    {
        if (($list = Cache::init()->get(self::LIST_CACHE_KEY)) === false OR $recache) {
            $list = $this->getBackendProcessor()->getList();

            if (!Cache::init()->set(self::LIST_CACHE_KEY , $list)) {
                throw new ModelException("Unable to cache storage data", 500);
            }
        }
        return $list;
    }


    public function update(Entity $currency) {
    }

    public function fetch(Entity $currency) {

        $list = $this->getList();
        if(isset($list[$currency->getId]) && $currency->formatFrom('CLASS',$list[$currency->getId])) {
            return $currency;
        } elseif(is_array($list) && !empty($list) && $currency->formatFrom('CLASS',reset($list))) {
            return $currency;
        } else
            throw new ModelException("Currency not found", 404);
    }

    public function delete(Entity $currency) {
    }
}