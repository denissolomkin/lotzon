<?php

abstract class BaseCacheProcessor
{
    private $_backendProcessor = null;

    public function __construct()
    {
        $this->init();
    }

    public function init()
    {

    }

    public function __call($method, $params = null)
    {
        if(method_exists($this->getBackendProcessor(), $method))
            return call_user_func_array(array($this->getBackendProcessor(), $method), $params);

        throw new Exception("Method $method is not defined in " . get_class($this) . "!");
    }

    public function setBackendProcessor($backendProcessor) 
    {
        $this->_backendProcessor = $backendProcessor;

        return $this;
    }

    public function getBackendProcessor()
    {
        return $this->_backendProcessor;
    }

    public function fetch(Entity $entity)
    {
        return $this->getBackendProcessor()->fetch($entity);
    }

    public function update(Entity $entity)
    {
        return $this->getBackendProcessor()->update($entity);
    }

    public function create(Entity $entity)
    {
        return $this->getBackendProcessor()->create($entity);
    }

    public function delete(Entity $entity)
    {
        return $this->getBackendProcessor()->delete($entity);
    }
}