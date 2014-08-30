<?php
Application::import(PATH_APPLICATION . 'model/Entity.php');

class Admin extends Entity
{
    const SESSION_VAR = '540236854b650';

    public function init()
    {
        $this->setModelClass('AdminModel');
    }

    private $_login = '';
    private $_password = '';
    private $_salt = '';
    private $_lastLogin = 0;
    private $_lastLoginIp = 0;    


    public function setLogin($login)
    {
        $this->_login = $login;

        return $this;
    }

    public function getLogin()
    {
        return $this->_login;
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

    public function setLastLogin($lastLogin)
    {
        $this->_lastLogin = $lastLogin;

        return $this;
    }

    public function getLastLogin()
    {
        return $this->_lastLogin;
    }

    public function setLastLoginIp($lastLoginIp)
    {
        $this->_lastLoginIp = $lastLoginIp;

        return $this;
    }

    public function getLastLoginIp()
    {
        return $this->_lastLoginIp;
    }

    public function formatFrom($from, $data) 
    {
        if ($from == 'DB') {
            $this->setLogin($data['Login'])
                 ->setPassword($data['Password'])
                 ->setSalt($data['Salt'])
                 ->setLastLogin($data['LastLogin'])
                 ->setLastLoginIp($data['LastLoginIP']);
        }
        return $this;
    }

    public function validate($event, $params = array()) 
    {
        switch ($event) {
            case 'login' :
                $this->isValidLogin();
                if (!$params['password']) {
                    throw new EntityException("Empty password", 400);   
                }
            break;
            case 'fetch' :
                $this->isValidLogin();
            break;
            default:
                throw new EntityException("Object does not pass validation", 400);
            break;
        }

        return true;
    }

    protected function isValidLogin($throwException = true)
    {
        if (!$this->getLogin() && preg_match('/^[a-z0-9-_]{6,}$/', $this->getLogin())) {
            if ($throwException) {
                throw new EntityException("Invalid or empty login", 400);    
            }
            return false;
        }

        return true;
    }

    public function login($password) {
        $this->fetch();
        $this->validate('login', array('password' => $password));

        if ($this->saltPassword($password) !== $this->getPassword()) {
            throw new EntityException("Invalid password", 401);
        } 

        Session::connect()->set(self::SESSION_VAR, $this);
        return true;
    }

    public function logout()
    {
        Session::connect()->delete(self::SESSION_VAR);

        return true;
    }

    protected function saltPassword($password)
    {
        return md5(sha1($password) . $this->getSalt());
    }

    public function __destruct()
    {
        if ($this->getLastLogin()) {
            try {
                $this->update();    
            } catch (EntityException $e) {}            
        }
    }
}