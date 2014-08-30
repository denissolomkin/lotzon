<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/processors/AdminDBProcessor.php');

class AdminModel extends Model
{
    public function init()
    {
        parent::init();

        $this->setProcessor(new AdminDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }
}
