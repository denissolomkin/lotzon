<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/processors/CaptchaDBProcessor.php');


class CaptchaModel extends Model
{
    public function init()
    {
        $this->setProcessor(new CaptchaDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function getList()
    {
        return $this->getProcessor()->getList();
    }

    public function getTimes()
    {
        return $this->getProcessor()->getTimes();
    }

    public function getStat()
    {
        return $this->getProcessor()->getStat();
    }
}
