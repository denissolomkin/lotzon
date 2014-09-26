<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/models/*');

abstract class Entity 
{

    /**
     * Entity model class name
     * @var string
     */
    private $_modelClass = '';

    public function __construct() {
        $this->init();
    }

    public function init()
    {

    }

    public function setModelClass($modelClassName)
    {
        if (class_exists($modelClassName) && is_subclass_of($modelClassName, 'Model')) {
            $this->_modelClass = $modelClassName;

            return $this;
        } 

        throw new EntityException("Invalid model class specified", 500);
    }

    public function getModelClass()
    {
        if (!empty($this->_modelClass)) {
            return $this->_modelClass;
        }

        throw new EntityException("Model class specified", 500);
    }

    public function create()
    {
        $this->validate('create');
        try {
            $model = $this->getModelClass();
            $model::instance()->create($this);
        }  catch (ModelException $e) {
            throw new EntityException($e->getMessage(), $e->getCode());
        }

        return $this;
    }

    public function update()
    {   
        $this->validate('update');
        try {
            $model = $this->getModelClass();
            $model::instance()->update($this);
        }  catch (ModelException $e) {
            throw new EntityException($e->getMessage(), $e->getCode());
        }

        return $this;
    }

    public function delete()
    {
        $this->validate('delete');
        try {
            $model = $this->getModelClass();
            $model::instance()->delete($this);
        }  catch (ModelException $e) {
            throw new EntityException($e->getMessage(), $e->getCode());
        }

        return null;        
    }

    public function fetch()
    {
        $this->validate('fetch');
        try {
            $model = $this->getModelClass();
            $model::instance()->fetch($this);
            
        }  catch (ModelException $e) {
            throw new EntityException($e->getMessage(), $e->getCode());
        }

        return $this;
    }


    public function serialize()
    {

    }

    public function unserialize()
    {

    }
}

class EntityException extends ApplicationException 
{

}