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

    public function setBackendProcessor($backendProcessor) 
    {
        $this->_backendProcessor = $backendProcessor;

        return $this;
    }

    public function getBackendProcessor()
    {
        return $this->_backendProcessor;
    }
}