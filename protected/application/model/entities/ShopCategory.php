<?php
Application::import(PATH_APPLICATION . 'model/Entity.php');

class ShopCategory extends Entity 
{
    private $_id    =  0;
    private $_name  = '';
    private $_order  = 0;
    private $_items = array();

    public function init() 
    {
        $this->setModelClass('ShopModel');
    }

    public function setId($id)
    {
        $this->_id = $id;

        return $this;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function setOrder($order)
    {
        $this->_order = $order;

        return $this;
    }

    public function getOrder()
    {
        return $this->_order;
    }

    public function setName($name) 
    {
        $this->_name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function addItem(ShopItem $item)
    {
        $this->_items[$item->getId()] = $item;

        return $this;
    }

    public function getItem($id) {
        return $this->_items[$id];
    }

    public function getItems() 
    {
        return $this->_items;
    }

    public function create()
    {
        $this->validate('create');
        try {
            $model = $this->getModelClass();
            $model::instance()->createCategory($this);
        }  catch (ModelException $e) {
            throw new EntityException($e->getCode(), $e->getMessage());
        }

        return $this;
    }

    public function delete()
    {
        $this->validate('delete');
        try {
            $model = $this->getModelClass();
            $model::instance()->deleteCategory($this);
        }  catch (ModelException $e) {
            throw new EntityException($e->getCode(), $e->getMessage());
        }

        return $this;
    }

    public function update() 
    {
        $this->validate('update');
        try {
            $model = $this->getModelClass();
            $model::instance()->updateCategory($this);
        }  catch (ModelException $e) {
            throw new EntityException($e->getCode(), $e->getMessage());
        }

        return $this;
    }

    public function validate($action, $params = array()) 
    {
        switch($action) {
            case 'create' :
                if (!$this->getName()) {
                    throw new EntityException("Category name can't be empty", 400);
                }
                $this->setName(htmlspecialchars(strip_tags($this->getName())));
            break;
            case 'delete' :
                if (!$this->getId()) {
                    throw new EntityException("Category id required", 400);   
                }
            break;
            case 'update' :
                if (!$this->getId()) {
                    throw new EntityException("EMPTY_CATEGORY_ID", 400);
                }
                if ($this->getName()) {
                    $this->setName(htmlspecialchars(strip_tags($this->getName())));  
                }
            break;
            default :
                throw new EntityException("Object validation fails", 400);                
            break;
        }

        return $this;
    }

}