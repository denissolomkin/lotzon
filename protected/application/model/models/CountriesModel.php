<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/entities/Country.php');
Application::import(PATH_APPLICATION . 'model/processors/CountriesDBProcessor.php');

class CountriesModel extends Model
{
    public function init()
    {
        parent::init();

        $this->setProcessor(new CountriesDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function getList()
    {
        $countries = $this->getProcessor()->getList();

        return $countries;
    }

    public function isCountry($code=null)
    {
        $countries = $this->getList();
        return isset($countries[$code]);
    }

    public function isLang($code=null)
    {
        return in_array($code, $this->getLangs());
    }

    public function getCountry($code=null)
    {
        $countries = $this->getList();

        if(isset($countries[$code])) {
            return $countries[$code];
        } elseif(is_array($countries) && !empty($countries)) {
            return reset($countries);
        } else {
            $default = new Country;
        }

        return $default;
    }

    public function defaultLang()
    {
        if(is_array($countries=$this->getList()) && !empty($countries)) {
            if ($default = (reset($countries)))
                return $default->getLang();
        } else {
            $default = new Country;
        }
        return $default->getLang();


    }
    public function defaultCountry()
    {

        if(is_array($countries=$this->getList()) && !empty($countries)) {
            if ($default = (reset($countries)))
                return $default->getCode();
        } else {
            $default = new Country;
        }
        return $default->getCode();

    }

    public function getCountries()
    {
        $countries = $this->getProcessor()->getCountries();

        return $countries;
    }

    public function getAvailabledCountries()
    {
        $countries = $this->getProcessor()->getAvailabledCountries();

        return $countries;
    }

    public function getLangs()
    {
        $countries = $this->getProcessor()->getLangs();

        return $countries;
    }
}
