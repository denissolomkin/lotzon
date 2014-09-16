<?php
Application::import(PATH_APPLICATION . 'model/Entity.php');

class ShopItem extends Entity 
{
    const IMAGE_WIDTH = 231;
    const IMAGE_HEIGHT = 231;

    private $_id       = 0;
    private $_category = null;
    private $_title    = '';
    private $_price    = 0;
    private $_quantity = 0;
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
        $this->_quantity = (int)$quantity;

        return $this;
    }

    public function getQuantity()
    {
        return $this->_quantity;
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
        $this->validate('udpate');
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
                if (!$this->getImage()) {
                    $this->setVisibility(false);
                }
                if (!$this->getCategory()->getId()) {
                    throw new EntityException("invalid category link", 400);
                }
            break;
            case 'delete' :
                if (!$this->getId()) {
                    throw new EntityException("Category id required", 400);   
                }
            break;
            default :
                throw new EntityException("Object validation fails", 400);                
            break;
        }

        return $this;
    }

}