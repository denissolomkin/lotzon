<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/processors/BannersDBProcessor.php');

class BannersModel extends Model
{
    public function init()
    {
        $this->setProcessor(new BannersDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function hitBanner($userId, $device, $location, $page, $title, $country)
    {
        return $this->getProcessor()->hitBanner($userId, $device, $location, $page, $title, $country);
    }
}