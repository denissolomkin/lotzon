<?php

Application::import(PATH_APPLICATION . 'model/processors/BaseCacheProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/ShopDBProcessor.php');

class ShopCacheProcessor extends BaseCacheProcessor
{

    const CACHE_KEY = "shop::categories";
    const ITEMS_KEY = "shop::items";

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
        $this->getBackendProcessor()->deleteCategory($category);

        $this->recacheShop();
        return true;
    }

    public function updateCategory(ShopCategory $category) {
        $this->getBackendProcessor()->updateCategory($category);

        $this->recacheShop();
        return true;
    }

    public function fetch(Entity $item)
    {
        $itemData = $this->getAllItems()[$item->getId()];

        $item->setTitle($itemData->getTitle())
            ->setPrice($itemData->getPrice())
            ->setQuantity($itemData->getQuantity())
            ->setCountries($itemData->getCountries())
            ->setImage($itemData->getImage())
            ->setCategory($itemData->getCategory());

        return $item;
    }

    public function create(Entity $item) {
        $item = $this->getBackendProcessor()->create($item);

        $this->recacheShop();
        return $item;
    }

    public function delete(Entity $item) {
        $this->getBackendProcessor()->delete($item);

        $this->recacheShop();
        return true;
    } 
    
    public function update(Entity $item) {
        $item = $this->getBackendProcessor()->update($item);

        $this->recacheShop();
        return $item;
    } 


    public function recacheShop()
    {
        $shop = $this->getBackendProcessor()->loadShop();

        if (!Cache::init()->set(self::CACHE_KEY, $shop)) {
            throw new ModelException("Unable to cache storage data", 500);            
        }

        $this->recacheItems();

        return $shop;    
    }

    public function recacheItems()
    {
        $items = array();
        $shop = $this->loadShop();

        foreach ($shop as $category) {
            foreach ($category->getItems() as $item) {
                $items[(int)$item->getId()] = $item;
            }
        }

        if (!Cache::init()->set(self::ITEMS_KEY, $items)) {
            throw new ModelException("Unable to cache storage data", 500);
        }

        return $items;
    }

    public function getAllItems($excludeQuantibleItems = true)
    {
        $this->recacheShop();
        
        $items = Cache::init()->get(self::ITEMS_KEY) ? : $this->recacheItems();
        return $items;
    }

    public function loadShop()
    {
        if (($shop = Cache::init()->get(self::CACHE_KEY)) !== false) {
            return $shop;
        }

        return $this->recacheShop();
    }
}