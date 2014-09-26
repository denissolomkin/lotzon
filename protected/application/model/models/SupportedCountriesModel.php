<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/entities/SupportedCountry.php');
Application::import(PATH_APPLICATION . 'model/processors/SupportedCountriesDBProcessor.php');

class SupportedCountriesModel extends Model
{
    public function init()
    {
        parent::init();

        $this->setProcessor(new SupportedCountriesDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function getEnabledCountriesList()
    {
        $countries = $this->getProcessor()->getEnabledCountriesList();

        return $countries;
    }
}
