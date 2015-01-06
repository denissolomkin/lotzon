<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/BaseCacheProcessor.php');

class ReviewsCacheProcessor extends BaseCacheProcessor implements IProcessor
{
    const LIST_CACHE_KEY = "reviews::latest::%s";

    public function init()
    {
        $this->setBackendProcessor(new ReviewsDBProcessor());
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
            $list = $this->getBackendProcessor()->getList(1, Config::instance()->reviewsCacheCount);
            if (!Cache::init()->set(sprintf(self::LIST_CACHE_KEY, 1) , $list)) {
                throw new ModelException("Unable to cache storage data", 500);
            }

    }

    public function fetch(Entity $text)
    {
        
    }

    public function getList($status = 1, $limit = null, $offset = null)
    {
        if ($limit + $offset > Config::instance()->reviewsCacheCount) {
            return $this->getBackendProcessor()->getList($status, $limit, $offset);
        }
        if (($list = Cache::init()->get(sprintf(self::LIST_CACHE_KEY, $status))) !== false) {
            if (!is_null($limit) || !is_null($offset)) {
                $list = array_slice($list, $offset, $limit);
            }
        } else {
            $list = $this->getBackendProcessor()->getList($status, $limit, $offset);
            $this->cacheLatest();
        }

        return $list;
    }

    public function getCount($lang) {
        return $this->getBackendProcessor()->getCount($lang);
    } 

}