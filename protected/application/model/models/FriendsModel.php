<?php

Application::import(PATH_APPLICATION . 'model/processors/FriendsDBProcessor.php');

class FriendsModel extends Model
{
    public function init()
    {
        $this->setProcessor(new FriendsDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

}
