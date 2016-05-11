<?php

Application::import(PATH_APPLICATION . 'model/entities/Gift.php');
Application::import(PATH_APPLICATION . 'model/processors/GiftsDBProcessor.php');

class GiftsModel extends Model
{
    public function init()
    {
        $this->setProcessor(new GiftsDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function getList($playerId, $objectType = NULL, $objectId = NULL, $used = false)
    {
        return $this->getProcessor()->getList($playerId, $objectType, $objectId, $used);
    }

}
