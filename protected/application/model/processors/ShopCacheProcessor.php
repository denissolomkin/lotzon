<?php

Application::import(PATH_APPLICATION . 'model/processors/BaseCacheProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/ShopDBProcessor.php');

class ShopCacheProcessor extends BaseCacheProcessor
{

    const CACHE_KEY = "shop::categories";

    public function init()
    {
        $this->setBackendProcessor(new ShopDBProcessor());
    }

    public function createCategory(ShopCategory $category) {
        $category = $this->getBackendProcessor()->createCategory($category);

        $this->recacheShop();
        return $category;
    }

    public function deleteCategory(ShopCategory $category) {
        $this->getBackendProcessor()->updateCategory($category);

        $this->recacheShop();
        return true;
    } 

    public function createItem(ShopItem $item) {
        $item = $this->getBackendProcessor()->createItem($item);

        $this->recacheShop();
        return $item;
    }

    public function deleteItem(ShopItem $item) {
        $this->getBackendProcessor()->deleteItem($item);

        $this->recacheShop();
        return true;
    } 
    
    public function updateItem(ShopItem $item) {
        $item = $this->getBackendProcessor()->updateItem($item);

        $this->recacheShop();
        return $item;
    } 


    public function recacheShop()
    {
        $shop = $this->getBackendProcessor()->loadShop();

        if (!Cache::init()->set(self::CACHE_KEY, $shop)) {
            throw new ModelException("Unable to cache storage data", 500);            
        }

        return $shop;    
    }

    public function loadShop()
    {
        if (($shop = Cache::init()->get(self::CACHE_KEY)) !== false) {
            return $shop;
        }

        return $this->recacheShop();
    }
}