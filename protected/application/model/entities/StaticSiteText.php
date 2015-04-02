<?php
Application::import(PATH_APPLICATION . 'model/Entity.php');

class StaticSiteText extends Entity 
{
    private $_id   = '';
    private $_text = '';
    private $_lang = '';
 
    public function init()
    {
        $this->setModelClass('StaticSiteTextsModel');
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

    public function formatFrom($from, $data) 
    {
        if ($from == 'DB') {
            $this->setId($data['Id'])
                 ->setLang(strtoupper($data['Lang']))
                 ->setText($data['Text']);
        }

        return $this;
    }

    public function validate($action)
    {
        switch ($action) {
            case 'create' :
            case 'update' :
                if (!$this->getId()) {
                    throw new EntityException("Identifier can't be empty", 400);
                }

                if (!preg_match('/^[\p{L}0-9_-]+$/iu', $this->getId())) {
                    throw new EntityException("Invalid identifier format", 400);   
                }

                if (!$this->getLang() || !\CountriesModel::instance()->isLang($this->getLang())) {
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