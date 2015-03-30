<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/entities/ShopCategory.php');
Application::import(PATH_APPLICATION . 'model/processors/ShopDBProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/ShopCacheProcessor.php');


class ShopModel extends Model
{
    public function init()
    {
        $this->setProcessor(Config::instance()->cacheEnabled ? new ShopCacheProcessor() : new ShopDBProcessor());
        // $this->setProcessor(new ShopDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function createCategory($category)
    {
        return $this->getProcessor()->createCategory($category);
    }

    public function updateCategory($category) {
        return $this->getProcessor()->updateCategory($category);   
    }

    public function deleteCategory($category)
    {
        return $this->getProcessor()->deleteCategory($category);
    }

    public function createItem($item)
    {
        return $this->getProcessor()->createItem($item);
    }

    public function deleteItem($item)
    {
        return $this->getProcessor()->deleteItem($item);
    }

    public function updateItem($item) 
    {
        return $this->getProcessor()->updateItem($item);   
    }

    public function fetchItem($item)
    {
        return $this->getProcessor()->fetchItem($item);
    }

    public function loadShop()
    {
        return $this->getProcessor()->loadShop();
    }

    public function create(Entity $object) 
    {
        throw new ModelException("Direct manupilation disabled", 500);        
    }

    public function fetch(Entity $object)
    {
        throw new ModelException("Direct manupilation disabled", 500);
    }   

    public function update(Entity $object)
    {
        throw new ModelException("Direct manupilation disabled", 500);
    }

    public function delete(Entity $object)
    {
        throw new ModelException("Direct manupilation disabled", 500);
    }

    public function getAllItems($excludeQuantibleItems = true)
    {
        return $this->getProcessor()->getAllItems($excludeQuantibleItems);
    }


}