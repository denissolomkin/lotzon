<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/processors/DebugDBProcessor.php');


class DebugModel extends Model
{
    public function init()
    {
        $this->setProcessor(new DebugDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function addLog($player, $log = array())
    {
        return $this->getProcessor()->addLog($player, $log);
    }

    public function getList($mode = null)
    {
        return $this->getProcessor()->getList($mode);
    }
}
