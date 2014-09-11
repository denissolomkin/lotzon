<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/BaseCacheProcessor.php');

class NewsCacheProcessor extends BaseCacheProcessor implements IProcessor
{
    const LIST_CACHE_KEY = "news::latest::%s";

    public function init()
    {
        $this->setBackendProcessor(new NewsDBProcessor());
    }

    public function create(Entity $object)
    {
        $text = $this->getBackendProcessor()->create($object);
        $this->cacheLatest();

        return $text;
    }

    public function update(Entity $object)
    {
        $text = $this->getBackendProcessor()->update($object);
        $this->cacheLatest();

        return $text;   
    }
    
    public function delete(Entity $object)
    {
        $text = $this->getBackendProcessor()->delete($object);
        $this->cacheLatest();
        
        return true;
    }   

    private function cacheLatest()
    {
        foreach (Config::instance()->langs as $lang) {
            $list = $this->getBackendProcessor()->getList($lang, Config::instance()->newsCacheCount);
            if (!Cache::init()->set(sprintf(self::LIST_CACHE_KEY, $lang) , $list)) {
                throw new ModelException("Unable to cache storage data", 500);
            }
        }
    }

    public function fetch(Entity $text)
    {
        
    }

    public function getList($lang, $limit = null, $offset = null)
    {
        if ($limit + $offset > Config::instance()->newsCacheCount) {
            return $this->getBackendProcessor()->getList($lang, $limit, $offset);
        }
        if (($list = Cache::init()->get(sprintf(self::LIST_CACHE_KEY, $lang))) !== false) {
            if (!is_null($limit) || !is_null($offset)) {
                $list = array_slice($list, $offset, $limit);
            }
        } else {
            $list = $this->getBackendProcessor()->getList($lang, $limit, $offset);
            $this->cacheLatest();
        }

        return $list;
    }

}