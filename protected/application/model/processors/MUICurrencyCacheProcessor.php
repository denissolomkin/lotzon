<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/BaseCacheProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/MUICurrencyDBProcessor.php');

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
        $this->recache();
        return $currency;
    }

    public function getList()
    {
        if (($list = Cache::init()->get(self::LIST_CACHE_KEY)) === false) {
            $list = $this->recache();
        }

        /*
         * todo delete after first use
         */

        if(!(current($list)->getId())){
            $list = $this->recache();
        }

        return $list;
    }

    public function update(Entity $currency) {
    }

    public function fetch(Entity $currency) {

        $list = $this->getList();
        if(isset($list[$currency->getId()]) && $currency->formatFrom('CLASS',$list[$currency->getId()])) {
            return $currency;
        } elseif(is_array($list) && !empty($list) && $currency->formatFrom('CLASS',reset($list))) {
            return $currency;
        } else
            throw new ModelException("Currency not found", 404);
    }

    public function delete(Entity $currency) {
    }

    public function recache() {

        $list = $this->getBackendProcessor()->getList();

        if (!Cache::init()->set(self::LIST_CACHE_KEY , $list)) {
            throw new ModelException("Unable to cache storage data", 500);
        }

        return $list;
    }
}