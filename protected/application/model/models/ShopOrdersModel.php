<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/entities/ShopItemOrder.php');
Application::import(PATH_APPLICATION . 'model/processors/ShopOrdersDBProcessor.php');


class ShopOrdersModel extends Model
{
    public function init()
    {
        $this->setProcessor(new ShopOrdersDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function getOrdersToProcess($limit = 0, $offset = 0, $playerid=null,$status=null)
    {
        return $this->getProcessor()->getOrdersToProcess($limit,$offset,$playerid,$status);
    }

    public function getOrdersToProcessCount($playerid=null,$status=null)
    {
        return $this->getProcessor()->getOrdersToProcessCount($playerid,$status);
    }
}