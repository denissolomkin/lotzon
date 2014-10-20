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

    public function getList()
    {
        return $this->getProcessor()->getList();
    }
}