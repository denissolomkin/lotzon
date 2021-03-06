<?php
Application::import(PATH_APPLICATION . 'model/Entity.php');

class ShopItem extends Entity 
{
    const IMAGE_WIDTH = 300;
    const IMAGE_HEIGHT = 300;

    private $_id       = 0;
    private $_category = null;
    private $_title    = '';
    private $_price    = 0;
    private $_quantity = null;
    private $_countries = array();
    private $_visible  = true;
    private $_image    = '';

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

    public function setTitle($title) 
    {
        $this->_title = $title;

        return $this;
    }

    public function getTitle()
    {
        return $this->_title;
    }

    public function setPrice($price)
    {   
        $this->_price = (int)$price;

        return $this;
    }

    public function getPrice()
    {
        return $this->_price;
    }

    public function setQuantity($quantity)
    {

        $this->_quantity = is_numeric($quantity) ? $quantity : null;

        return $this;
    }

    public function getQuantity()
    {
        return $this->_quantity;
    }

    public function setCountries($countries)
    {
        $this->_countries = $countries;

        return $this;
    }

    public function getCountries()
    {
        return $this->_countries;
    }

    public function setCategory(ShopCategory $category) 
    {
        $this->_category = $category;

        return $this;
    }

    public function getCategory()
    {
        return $this->_category;
    }

    public function setVisibility($visible)
    {
        $this->_visible = $visible;

        return $this;
    }

    public function isVisible()
    {
        return (boolean)$this->_visible;
    }

    public function setImage($image) 
    {
        $this->_image = $image;

        return $this;
    }

    public function getImage()
    {
        return $this->_image;
    }

    public function create()
    {
        $this->validate('create');
        try {
            $model = $this->getModelClass();
            $model::instance()->createItem($this);
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
            $model::instance()->updateItem($this);
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
            $model::instance()->deleteItem($this);
        }  catch (ModelException $e) {
            throw new EntityException($e->getCode(), $e->getMessage());
        }

        return true;
    }

    public function fetch()
    {
        $this->validate('fetch');
        try {
            $model = $this->getModelClass();
            $model::instance()->fetchItem($this);
        }  catch (ModelException $e) {
            throw new EntityException($e->getMessage(), $e->getCode());
        }
    
        return $this;        
    }

    public function validate($action, $params = array()) 
    {
        switch($action) {
            case 'create' :
            case 'update' :
                if (!$this->getTitle()) {
                    throw new EntityException("Item title can't be empty", 400);
                }
                $this->setTitle(htmlspecialchars(strip_tags($this->getTitle())));

                if (!$this->getPrice()) {
                    throw new EntityException("Price can't be empty", 1);
                }
            break;
            case 'delete' :
                if (!$this->getId()) {
                    throw new EntityException("Category id required", 400);   
                }
            break;
            case 'fetch' :
                if (!$this->getId()) {
                    throw new EntityException("EMPTY_ITEM_ID", 400);
                }
            break;
            default :
                throw new EntityException("Object validation fails", 400);                
            break;
        }

        return $this;
    }

}