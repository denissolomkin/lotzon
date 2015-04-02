<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/entities/Country.php');
Application::import(PATH_APPLICATION . 'model/processors/CountriesDBProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/CountriesCacheProcessor.php');

class CountriesModel extends Model
{
    public function init()
    {
        parent::init();

        $this->setProcessor(Config::instance()->cacheEnabled ? new CountriesCacheProcessor() : new CountriesDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function getList()
    {
        return $this->getProcessor()->getList();
    }

    public function isCountry($code=null)
    {
        return in_array($code, $this->getCountries());
    }

    public function isLang($code=null)
    {
        return in_array($code, $this->getLangs());
    }

    public function getCountry($code=null)
    {
        $countries = $this->getList();
        if(isset($countries[$code])) {
            $country = $countries[$code];
        } elseif(is_array($countries) && !empty($countries)) {
            $country = reset($countries);
        } else {
            $country = $this->getDefault();
        }

        return $country;
    }

    public function defaultLang()
    {
        if(is_array($countries=$this->getList()) && !empty($countries)) {
            if ($default = (reset($countries)))
                return $default->getLang();
        } else {
            return $this->getDefault()->getLang();
        }
    }

    public function defaultCountry()
    {

        if(is_array($countries=$this->getList()) && !empty($countries)) {
            if ($default = (reset($countries)))
                return $default->getCode();
        } else {
            return $this->getDefault()->getCode();
        }
    }

    public function getAvailabledCountries()
    {
        return $this->getProcessor()->getAvailabledCountries();
    }

    public function getCountries()
    {

        $countries = $this->getProcessor()->getCountries();
        if(empty($countries)) {
            $country = $this->getDefault();
            $countries[] = $country->getCode();
        }

        return $countries;
    }

    public function getLangs()
    {
        $langs = $this->getProcessor()->getLangs();
        if(empty($langs)) {
            $country = $this->getDefault();
            $langs[] = $country->getLang();
        }

        return $langs;

    }

    public function getDefault(){

        $default = new Country;
        $default->setId(1)
            ->setLang('RU')
            ->setCode('RU')
            ->setCurrency(1);
        return $default;
    }

}
