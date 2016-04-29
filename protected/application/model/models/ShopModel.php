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
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function create(Entity $item)
    {
        return $this->getProcessor()->create($item);
    }

    public function delete(Entity $item)
    {
        return $this->getProcessor()->delete($item);
    }

    public function update(Entity $item)
    {
        return $this->getProcessor()->update($item);
    }

    public function fetch(Entity $item)
    {
        return $this->getProcessor()->fetch($item);
    }

    public function createCategory($category)
    {
        return $this->getProcessor()->createCategory($category);
    }

    public function updateCategory($category)
    {
        return $this->getProcessor()->updateCategory($category);   
    }

    public function deleteCategory($category)
    {
        return $this->getProcessor()->deleteCategory($category);
    }

    public function loadShop()
    {
        return $this->getProcessor()->loadShop();
    }

    public function getAllItems($excludeQuantibleItems = true)
    {
        return $this->getProcessor()->getAllItems($excludeQuantibleItems);
    }


}