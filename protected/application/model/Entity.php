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

    /**
     * Обработчик вызовов к несуществующим методам get*(), set*()
     * При обращении, например, к getParentId вернёт значение свойства $_parentId
     * При обращении, например, к setParentId($id) запишет в свойство $_parentId значение $id
     *
     * @param      $method
     * @param null $params
     *
     * @return $this|null  $this при set*(), value|null при get*()
     * @throws Exception   Если get обращение к несуществующему и не обрабатываемому свойству
     */
    public function __call($method, $params = null)
    {
        $methodPrefix = substr($method, 0, 3);
        $key          = lcfirst(substr($method, 3));
        if (($methodPrefix == 'set') && (count($params) == 1)) {
            $value           = $params[0];
            $property        = '_'.$key;
            $this->$property = $value;
            return $this;
        } elseif ($methodPrefix == 'get') {
            $property = '_'.$key;
            if (isset($this->$property)) {
                return $this->$property;
            } else {
                return NULL;
            }
        }
        throw new Exception("Method $method is not defined!");
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