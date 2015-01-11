<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/entities/MoneyOrder.php');
Application::import(PATH_APPLICATION . 'model/processors/MoneyOrdersDBProcessor.php');


class MoneyOrderModel extends Model
{
    public function init()
    {
        $this->setProcessor(new MoneyOrdersDBProcessor());
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