<?php

Application::import(PATH_APPLICATION . 'model/processors/CommentsDBProcessor.php');

class CommentsModel extends Model
{
    public function init()
    {
        $this->setProcessor(new CommentsDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function getList($module, $objectId, $count, $beforeId = NULL, $afterId = NULL, $status = 1)
    {
        return $this->getProcessor()->getList($module, $objectId, $count, $beforeId, $afterId, $status);
    }
}
