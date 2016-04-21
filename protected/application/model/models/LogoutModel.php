<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/processors/LogoutMemoryProcessor.php');


class LogoutModel extends Model
{
    public function init()
    {
        $this->setProcessor(new LogoutMemoryProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

}
