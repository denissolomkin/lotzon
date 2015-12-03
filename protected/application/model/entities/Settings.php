<?php

Application::import(PATH_APPLICATION . 'model/Entity.php');

class Settings extends Entity
{
    private $_key = '';
    private $_value = array();

    public function init()
    {
        $this->setModelClass('SettingsModel');
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

    public function setValue($array)
    {
        $this->_value = $array;
        return $this;
    }

    public function getValue($value = null)
    {
        if ($value) {

            if (isset($this->_value[$value])) {
                $value = $this->_value[$value];
            } else {
                $value = null;
            }

        } else {
            $value = $this->_value;
        }

        return $value;
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
                $this->isValidKey();
                break;
            default:
                throw new EntityException("Object does not pass validation", 400);
                break;
        }

        return true;
    }

    private function isValidKey($throwException = true)
    {
        if ($this->getKey()) {
            return true;
        }

        if ($throwException) {
            throw new EntityException('Empty or invalid Key setting', 400);
        }

        return false;
    }

    public function formatFrom($from, $data)
    {
        switch ($from) {
            case 'DB' :
                $this->setKey($data['Key'])
                    ->setValue(@unserialize($data['Value']));
                break;
            case 'CLASS' :
                $this->setKey($data->getKey())
                    ->setValue($data->getValue());
                break;
        }

        return $this;
    }
}