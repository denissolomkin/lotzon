<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/BaseCacheProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/CountriesDBProcessor.php');

class CountriesCacheProcessor extends BaseCacheProcessor implements IProcessor
{

    const LIST_CACHE_KEY = "countries::list";
    const COUNTRIES_CACHE_KEY = "countries::countries";
    const LANGS_CACHE_KEY = "countries::langs";

    public function init()
    {
        $this->setBackendProcessor(new CountriesDBProcessor());
    }

    public function getList($recache=false)
    {
        if (($list = Cache::init()->get(self::LIST_CACHE_KEY)) === false OR $recache) {

            $list = $this->getBackendProcessor()->getList();

            if (!Cache::init()->set(self::LIST_CACHE_KEY , $list)) {
                throw new ModelException("Unable to cache storage data", 500);
            }

            $this->getCountries(true);
            $this->getLangs(true);

        }
        return $list;
    }

    public function getAvailabledCountries()
    {
        return $this->getBackendProcessor()->getAvailabledCountries();
    }

    public function getCountries($recache=false)
    {

        if (($countries = Cache::init()->get(self::COUNTRIES_CACHE_KEY)) === false || $recache) {

            $list = $this->getList();

            $countries = array();
            if(is_array($list) && !empty($list))
                foreach($list as $country)
                    $countries[]=$country->getCode();

            if (!Cache::init()->set(self::COUNTRIES_CACHE_KEY , $countries)) {
                throw new ModelException("Unable to cache storage data", 500);
            }
        }

        return $countries;
    }


    public function getLangs($recache=false)
    {

        if (($langs = Cache::init()->get(self::LANGS_CACHE_KEY)) === false || $recache) {

            $list = $this->getList();

            $langs = array();
            if(is_array($list) && !empty($list))
                foreach($list as $country)
                    $langs[]=$country->getLang();

            if (!Cache::init()->set(self::LANGS_CACHE_KEY , $langs)) {
                throw new ModelException("Unable to cache storage data", 500);
            }
        }

        return $langs;
    }


    public function create(Entity $country)
    {
        $country = $this->getBackendProcessor()->create($country);
        $this->getList(true);
        return $country;
    }

    public function update(Entity $player) {
    }

    public function fetch(Entity $player) {
    }

    public function delete(Entity $player) {
    }
}