<?php
Application::import(PATH_APPLICATION . 'model/Entity.php');

class StaticText extends Entity
{
    private $_id   = 0;
    private $_key   = '';
    private $_category = '';
    private $_lang = '';
    private $_text = '';
 
    public function init()
    {
        $this->setModelClass('StaticTextsModel');
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

    public function setKey($key)
    {
        $this->_key = $key;

        return $this;
    }

    public function getKey()
    {
        return $this->_key;
    }

    public function addText($lang, $text)
    {
        $this->_text[$lang] = $text;

        return $this;
    }

    public function setText($text)
    {
        $this->_text = $text;

        return $this;
    }

    public function getText($lang=null)
    {
        if(isset($lang)) {
            if(isset($this->_text[$lang]) && $this->_text[$lang] && $this->_text[$lang]!='') // get Lang
                $text = $this->_text[$lang];
            elseif($lang=\CountriesModel::instance()->defaultLang() && isset($this->_text[$lang]) && $this->_text[$lang] && $this->_text[$lang]!='') // default Lang
                $text = $this->_text[$lang];
            elseif(is_array($this->_text) && !empty($this->_text)) // any Lang
                $text = reset($this->_text);
            else
                $text = $this->getKey(); // identifier
        } else
            $text = $this->_text;

        return $text;
    }

    public function setCategory($cat)
    {
        $this->_category = $cat;

        return $this;
    }


    public function getCategory()
    {
        return $this->_category;
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

    public function validate($action)
    {
        switch ($action) {
            case 'create' :
            case 'update' :
                if (!$this->getKey()) {
                    throw new EntityException("Identifier can't be empty", 400);
                }

                if (!preg_match('/^[\p{L}0-9_-]+$/iu', $this->getKey())) {
                    throw new EntityException("Invalid identifier format", 400);   
                }

                if (!($text=$this->getText()) || empty($text)) {
                    throw new EntityException("Invalid lang", 400);
                }
            break;
            
            case 'delete':
                if (!$this->getKey()) {
                    throw new EntityException("Identifier can't be empty", 400);
                }                
            break;
            default:
                throw new EntityException('Object does not pass validation', 400);
            break;
        }
    }

    public function formatFrom($from, $data)
    {
        if ($from == 'DB') {
            $this->setId($data['Id'])
                ->setKey($data['Key'])
                ->setLang($data['Lang'])
                ->setCategory($data['Category'])
                ->setText(unserialize($data['Text'])?:(array($data['Lang']=>$data['Text'])));
        }

        return $this;
    }
}