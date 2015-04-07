<?php

Application::import(PATH_APPLICATION . 'model/Entity.php');

class Language extends Entity
{
    private $_id          = 0;
    private $_code        = '';
    private $_title       = '';

 
    public function init()
    {
        $this->setModelClass('LanguagesModel');
    }

    public function setId($int)
    {
        if($int)
            $this->_id = (int) $int;

        return $this;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function setCode($char)
    {
        $this->_code = $char;
        return $this;
    }

    public function getCode()
    {
        return $this->_code;
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

    public function validate($event, $params = array())
    {
        switch ($event) {
            case 'update' :

            break;

            case 'delete' :

            break;

            case 'fetch' :

            break;

            case 'create' :
                $this->isValidTitle();
                $this->isValidCode();
            break;
            default:
                throw new EntityException("Object does not pass validation", 400);
            break;
        }

        return true;
    }

    private function isValidCode($throwException = true)
    {
        if ($this->getCode()){
            return true;
        }

        if ($throwException) {
            throw new EntityException('Empty Code', 400);
        }

        return false;
    }

    private function isValidTitle($throwException = true)
    {
        if ($this->getTitle()){
            return true;
        }

        if ($throwException) {
            throw new EntityException('Empty Title', 400);
        }

        return false;
    }

    public function formatFrom($from, $data) {
        switch ($from) {
            case 'DB' :
                $this->setId($data['Id'])
                     ->setCode($data['Code'])
                     ->setTitle($data['Title']);
            break;
            case 'CLASS' :
                $this->setId($data->getId())
                    ->setCode($data->getCode())
                    ->setTitle($data->getTitle());
                break;
        }

        return $this;
    }
}