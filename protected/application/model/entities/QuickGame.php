<?php 

class QuickGame extends Entity
{
    private $_id = '';
    private $_title = '';
    private $_banner = '';
    private $_description = '';
    private $_enabled = true;
    private $_prizes = array();
    private $_field= array();

    public function init()
    {
        $this->setModelClass('QuickGamesModel');
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

    public function setEnabled($enabled)
    {
        $this->_enabled = $enabled;
        return $this;
    }

    public function isEnabled()
    {
        return $this->_enabled;
    }

    public function setField($field)
    {
        $this->_field = $field;

        return $this;
    }

    public function getField()
    {
        return $this->_field;
    }

    public function setPrizes($prizes)
    {
        $this->_prizes = $prizes;

        return $this;
    }

    public function getPrizes()
    {
        return $this->_prizes;
    }

    public function setDescription($description)
    {
        $this->_description = $description;

        return $this;
    }

    public function getDescription()
    {
        return $this->_description;
    }

    public function setBanner($banner)
    {
        $this->_banner = $banner;

        return $this;
    }

    public function getBanner()
    {
        return $this->_banner;
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

    public function save()
    {
        try {
            $model = $this->getModelClass();
            $model::instance()->save($this);
        }  catch (ModelException $e) {
            throw new EntityException($e->getMessage(), $e->getCode());
        }

        return $this;
    }

    public function loadPrizes()
    {
        $prizes = array();
        if ($this->getPrizes()) {
            foreach ($this->getPrizes() as $prize) {
                if($prize['type']=='item')
                $prize = new ShopItem();
                try {
                    $prize->setId($prize)->fetch();
                    $prizes[$prize->getId()] = $prize;
                } catch (EntityException $e) {}
            }
        }

        return $prizes;
    }
}
