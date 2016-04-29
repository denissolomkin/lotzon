<?php
Application::import(PATH_APPLICATION . 'model/Entity.php');

class ShopItem extends Entity 
{
    const IMAGE_WIDTH = 300;
    const IMAGE_HEIGHT = 300;

    protected $_id       = 0;
    protected $_category = null;
    protected $_title    = '';
    protected $_price    = 0;
    protected $_quantity = null;
    protected $_countries = array();
    protected $_visible  = true;
    protected $_image    = '';

    public function init() 
    {
        $this->setModelClass('ShopModel');
    }

    public function setQuantity($quantity)
    {

        $this->_quantity = is_numeric($quantity) ? $quantity : null;

        return $this;
    }

    public function exportTo($to)
    {
        switch ($to) {
            case 'list':
                $ret = array(
                    'id'       => $this->getId(),
                    'category' => $this->getCategory()->getId(),
                    'title'    => $this->getTitle(),
                    'price'    => $this->getPrice(),
                    'quantity' => $this->getQuantity(),
                    'type'     => 'item',
                    'img'      => $this->getImage()
                );

                return $ret;
                break;
        }
        return false;
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