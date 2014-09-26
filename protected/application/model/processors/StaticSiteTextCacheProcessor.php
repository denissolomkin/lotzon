<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/BaseCacheProcessor.php');

class StaticSiteTextCacheProcessor extends BaseCacheProcessor implements IProcessor
{
    const LIST_CACHE_KEY = "static::texts";

    public function init()
    {
        $this->setBackendProcessor(new StaticSiteTextDBProcessor());
    }

    public function create(Entity $object)
    {
        $text = $this->getBackendProcessor()->create($object);
        $this->recacheList();

        return $text;
    }

    public function update(Entity $object)
    {
        
    }
    
    public function delete(Entity $object)
    {
        $text = $this->getBackendProcessor()->delete($object);
        $this->recacheList();
        
        return true;
    }   

    private function recacheList()
    {
        $list = $this->getBackendProcessor()->getList();
        if (!Cache::init()->set(self::LIST_CACHE_KEY , $list)) {
            throw new ModelException("Unable to cache storage data", 500);
        }

        return $list;
    }

    public function fetch(Entity $text)
    {
        
    }

    public function getList()
    {
        if (($list = Cache::init()->get(self::LIST_CACHE_KEY)) !== false) {
            return $list;
        }
        
        return $this->recacheList();
    }

}