<?php

Application::import(PATH_APPLICATION . 'model/Entity.php');

class Player extends Entity
{
    private $_id         = 0;
    private $_email      = '';
    private $_password   = '';
    private $_salt       = '';
    
    private $_nicName    = '';
    private $_name       = '';
    private $_surname    = '';
    private $_secondName = '';
    private $_avatar     = '';
    
    private $_phone      = '';
    private $_birthday   = '';

    private $_dateRegistered = '';
    private $_dateLastLogin  = '';
    private $_country        = '';

    public function init()
    {
        $this->setModelClass('UsersModel');
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

    public function setEmail($email)
    {
        $this->_email = $email;

        return $this;
    }

    public function getEmail()
    {
        return $this->_email;
    }

    public function setPassword($password)
    {
        $this->_password = $password;

        return $this;
    }

    public function getPassword()
    {
        return $this->_password;
    }

    public function setSalt($salt)
    {
        $this->_salt = $salt;

        return $this;
    }

    public function getSalt()
    {
        return $this->_salt;
    }

    public function setNicName($nicName)
    {
        $this->_nicName = $nicName;

        return $this;
    }

    public function getNicName()
    {
        return $this->_nicName;
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

    public function setSurname($surname)
    {
        $this->_surname = $surname;

        return $this;
    }

    public function getSurname()
    {
        return $this->_surname;
    }

    public function setSecondName($secondName)
    {
        $this->_secondName = $secondName;

        return $this;
    }

    public function getSecondName()
    {
        return $this->_secondName;
    }

    public function setPhone($phone)
    {
        $this->_phone = $phone;

        return $this;
    }

    public function getPhone()
    {
        return $this->_phone;
    }    

    public function setBirthday($birthday)
    {
        $this->_birthday = $birthday;

        return $this;
    }

    public function getBirthday()
    {
        return $this->_birthday;
    }  

    public function setDateRegistered($dateRegistered)
    {
        $this->_dateRegistered = $dateRegistered;

        return $this;
    }

    public function getDateRegistered($format = null)
    {
        $date = $this->_dateRegistered;

        if (!is_null($format)) {
            $date = date($format, $this->_dateRegistered);
        }

        return $date;
    }  

    public function setDateLastLogin($dateLastLogin)
    {
        $this->_dateLastLogin = $dateLastLogin;

        return $this;
    }

    public function getDateLastLogin($format = null)
    {
        $date = $this->_dateLastLogin;

        if (!is_null($format)) {
            $date = date($format, $this->_dateLastLogin);
        }
        
        return $date;
    }  

    public function setCountry($country)
    {
        $this->_country = $country;

        return $this;
    }

    public function getCountry()
    {
        return $this->_country;
    }  

    public function setAvatar($avatar)
    {
        $this->_avatar = $avatar;

        return $this;
    }

    public function getAvatar()
    {
        return $this->_avatar;
    }  

}