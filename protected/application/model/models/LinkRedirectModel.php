<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/entities/LinkRedirect.php');
Application::import(PATH_APPLICATION . 'model/processors/LinkRedirectDBProcessor.php');


class LinkRedirectModel extends Model
{
    public function init()
    {
        $this->setProcessor(new LinkRedirectDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function getUin($link = '')
    {
        return $this->getProcessor()->getUin($link);
    }
}
