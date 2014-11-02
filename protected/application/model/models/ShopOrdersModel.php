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

    public function getOrdersToProcess()
    {
        return $this->getProcessor()->getOrdersToProcess();
    }
}