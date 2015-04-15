<?php
Application::import(PATH_APPLICATION . 'model/Entity.php');

class News extends Entity 
{
    private $_id    = '';
    private $_title = '';
    private $_text  = '';
    private $_lang  = '';
    private $_date  = '';
    
    public function init()
    {
        $this->setModelClass('NewsModel');
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

    public function setText($text)
    {
        $this->_text = $text;

        return $this;
    }

    public function getText()
    {
        return $this->_text;
    }

    public function setLang($lang) 
    {
        $this->_lang = $lang;

        return $this;
    }

    public function getLang()
    {
        return $this->_lang;
    }

    public function setTitle($title) {
        $this->_title = $title;

        return $this;
    }

    public function getTitle() 
    {
        return $this->_title;
    }

    public function setDate($date) 
    {
        $this->_date = $date;

        return $this;
    }

    public function getDate()
    {
        return $this->_date;
    }

    public function formatFrom($from, $data) 
    {
        if ($from == 'DB') {
            $this->setId($data['Id'])
                 ->setLang($data['Lang'])
                 ->setTitle($data['Title'])
                 ->setDate($data['Date'])
                 ->setText($data['Text']);
        }

        return $this;
    }

    public function validate($action)
    {
        switch ($action) {
            case 'create' :
            case 'update' :
                if (!$this->getTitle()) {
                    throw new EntityException("Title can not be empty", 400);
                }

                $this->setTitle(htmlspecialchars(strip_tags($this->getTitle())));

            ;

                if (!$this->getLang() || !\LanguagesModel::instance()->isLang($this->getLang())) {
                    throw new EntityException("Invalid lang", 400);      
                }
            break;
            
            case 'delete':
                if (!$this->getId()) {
                    throw new EntityException("Identifier can't be empty", 400);
                }                
            break;
            default:
                throw new EntityException('Object does not pass validation', 400);
            break;
        }
    }
}