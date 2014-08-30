<?php
Application::import(PATH_INTERFACES . 'IProcessor.php');
Application::import(PATH_APPLICATION . 'model/Entity.php');

abstract class Model
{
    /**
     * Model class instance
     * 
     * @var Model
     */
    protected static $_instance = null;

    /**
     * Storage processor
     * 
     * @var IProcessor
     */
    private $_processor = null;

    protected function __construct() {}

    public static function instance() 
    {
        if (is_null(self::$_instance)) {
            $className = static::myClassName();
            self::$_instance = new $className;
            self::$_instance->init();
        }

        return self::$_instance;
    }

    protected static function myClassName() 
    {
        throw new ModelException("myClassName must be overrided by Model class childs", 500);
    }

    public function init()
    {

    }

    public function setProcessor(IProcessor $processor) 
    {
        $this->_processor = $processor;
    }

    public function getProcessor() {
        if ($this->processorAvailable()) {
            return $this->_processor;    
        }
        
        throw new ModelException("Processor instance not specified", 500);
        
    }

    protected function processorAvailable() 
    {
        if (!is_null($this->_processor)) {
            return true;
        }

        return false;
    }

    public function create(Entity $object) 
    {
        return $this->getProcessor()->create($object);
    }

    public function update(Entity $object) 
    {
        return $this->getProcessor()->update($object);
    }

    public function delete(Entity $object)
    {
        return $this->getProcessor()->delete($object);
    }

    public function fetch(Entity $object)
    {
        return $this->getProcessor()->fetch($object);
    }
}

class ModelException extends ApplicationException
{

}


