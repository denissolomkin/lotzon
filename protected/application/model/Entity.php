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

    public function __construct()
    {
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
     * @return $this|null  $this при set*(), (bool)value|null при is*(), value|null при get*()
     *                     array[prop]|null при get*(prop)
     * @throws Exception   Если get обращение к несуществующему и не обрабатываемому свойству
     */
    public function __call($method, $params = null)
    {

        $methodPrefix = substr($method, 0, 3);
        $key          = lcfirst(substr($method, 3));
        $property     = '_' . $key;

        if (property_exists($this, $property)) {
            if (($methodPrefix == 'set') && (count($params) == 1)) {
                $value           = $params[0];
                $this->$property = $value;

                return $this;
            } elseif ($methodPrefix == 'get') {
                if (isset($this->$property)) {

                    if ((count($params) == 1 && ($value = $params[0]) && is_array($this->$property))) {
                        $property = $this->$property;

                        return isset($property[$value]) ? $property[$value] : null;
                    }

                    return $this->$property;
                } else {
                    return NULL;
                }
            }
        }

        $methodPrefix = substr($method, 0, 2);
        $key          = lcfirst(substr($method, 2));
        $property     = '_' . $key;

        if (property_exists($this, $property)) {
            if ($methodPrefix == 'is') {
                if (isset($this->$property)) {
                    return (bool)$this->$property;
                } else {
                    return NULL;
                }
            }
        }

        throw new Exception("Method $method is not defined in " . get_class($this) . "!");
    }

    public function setModelClass($modelClassName)
    {
        if (class_exists($modelClassName) && is_subclass_of($modelClassName, 'Model')) {
            $this->_modelClass = $modelClassName;

            return $this;
        }

        throw new EntityException("Invalid model class specified", 500);
    }

    public function getTitle()
    {
        $key = func_num_args() ? func_get_arg(0) : null;
        if ($key && is_array($this->_title)) {
            if (isset($this->_title[$key]) && $this->_title[$key] && $this->_title[$key] != '')
                return nl2br($this->_title[$key]);
            else
                return nl2br(reset($this->_title));
        }

        return $this->_title;
    }

    public function getDescription()
    {
        $key = func_num_args() ? func_get_arg(0) : null;
        if ($key && is_array($this->_description)) {
            if (isset($this->_description[$key]) && $this->_description[$key] && $this->_description[$key] != '')
                return nl2br($this->_description[$key]);
            else
                return nl2br(reset($this->_description));
        }

        return $this->_description;
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