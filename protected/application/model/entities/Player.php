<?php

Application::import(PATH_APPLICATION . 'model/Entity.php');

class Player extends Entity
{
    const IDENTITY = "player_session";

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

    private $_favoriteCombination = array();
    private $_visible             = false;

    private $_dateRegistered = '';
    private $_dateLastLogin  = '';
    private $_country        = '';

    private $_generatedPassword = '';

    public function init()
    {
        $this->setModelClass('PlayersModel');
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

    public function getBirthday($format = null)
    {
        $date = $this->_birthday;
        
        if (!is_null($format)) {
            $date = date($format, $this->_birthday);
        }

        return $date;
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

    public function setVisibility($v)
    {
        $this->_visible = $v;

        return $this;
    }

    public function getVisibility()
    {
        return  $this->_visible;
    }

    public function setFavoriteCombination(array $combination)
    {
        $this->_favoriteCombination = $combination;

        return $this;
    }

    public function getFavoriteCombination()
    {
        return $this->_favoriteCombination;
    }

    public function generatePassword()
    {
        $an = array(
            0 => "abcdefghijklmnopqrstuwxyz",
            1 => "ABCDEFGHIJKLMNOPQRSTUWXYZ",
            2 => "0123456789",
        );
        $pass = substr(str_shuffle($an[0]), 0, 6);
        $pass .= substr(str_shuffle($an[1]), 0, 6);
        $pass .= substr(str_shuffle($an[2]), 0, 4);

        $this->_generatedPassword = str_shuffle($pass);
        return $this->_generatedPassword;
    }  

    public function compilePassword($password)
    {
        if (!$this->getSalt()) 
        {
            $this->setSalt(uniqid());
        }
        
        return md5($this->getSalt() . sha1($password));
    }

    public function validate($action, $params = array())
    {
        switch ($action) {
            case 'create':
                $this->validEmail();
                try {
                    $this->fetch();
                    throw new EntityException('REG_LOGIN_EXISTS', 500);
                } catch (EntityException $e) {
                    if ($e->getCode() != 404) {
                        throw new EntityException($e->getMessage(), 500);
                    }
                }
            break;
            case 'fetch' :
                $this->validEmail();
            break;
            case 'login' :
                $this->validEmail();
                if (empty($params['password'])) {
                    throw new EntityException('EMPTY_PASSWORD', 400);
                }
            break;
            case 'update' :
                $this->validEmail();

                $this->setNicName(trim(htmlspecialchars(strip_tags($this->getNicName()))));
                $this->setName(trim(htmlspecialchars(strip_tags($this->getName()))));
                $this->setSurname(trim(htmlspecialchars(strip_tags($this->getSurname()))));
                $this->setSecondName(trim(htmlspecialchars(strip_tags($this->getSecondName()))));

                if ($this->getPhone() && !preg_match('/^[+0-9\- ()]*$/', $this->getPhone())) {
                    throw new EntityException("INVALID_PHONE_FORMAT", 400);
                }
            break;
            
            default:
                # code...
            break;
        }

        return true;
    }

    protected function validEmail($throwException = true)
    {

        if (!filter_var($this->getEmail(), FILTER_VALIDATE_EMAIL)) {
            if ($throwException) {
                throw new EntityException('INVALID_EMAIL', 500);
            }
        } 

        return true;
    }

    public function create()
    {
        $this->setPassword($this->compilePassword($this->generatePassword()));

        parent::create();

        Common::sendEmail($this->getEmail(), 'Регистрация на www.lotzone.com', 'player_registration', array(
            'login' => $this->getEmail(),
            'password'  => $this->_generatedPassword,
        ));
        $this->login($this->_generatedPassword);

        return $this;

    }

    public function login($password)
    {
        $this->validate('login', array(
            'password' => $password,
        ));

        try {
            $this->fetch();
        } catch (EntityException $e) {
            if ($e->getCode() == 404) {
                throw new EntityException("PLAYER_NOT_FOUND", 404);
            } else {
                throw new EntityException("INTERNAL_ERROR", 500);
            }
        }
        if ($this->getPassword() !== $this->compilePassword($password)) {
            throw new EntityException("INVALID_PASSWORD", 403);
        }

        Session::connect()->set(Player::IDENTITY, $this);

        $this->setDateLastLogin(time());
        try {
            $this->update();    
        } catch (Exception $e) {}
        
        return $this;
    }

    public function formatFrom($from, $data) 
    {
        if ($from == 'DB') {
            $this->setId($data['Id'])
                 ->setEmail($data['Email'])
                 ->setPassword($data['Password'])
                 ->setSalt($data['Salt'])
                 ->setNicName($data['Nicname'])
                 ->setName($data['Name'])
                 ->setSurname($data['Surname'])
                 ->setSecondName($data['SecondName'])
                 ->setPhone($data['Phone'])
                 ->setBirthday($data['Birthday'])
                 ->setDateRegistered($data['DateRegistered'])
                 ->setDateLastLogin($data['DateLogined'])
                 ->setCountry($data['Country'])
                 ->setAvatar($data['Avatar'])
                 ->setVisibility((boolean)$data['Visible'])
                 ->setFavoriteCombination(!empty($data['Favorite']) ? @unserialize($data['Favorite']) : array());
        }

        return $this;
    }

}