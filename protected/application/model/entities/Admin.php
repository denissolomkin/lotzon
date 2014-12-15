<?php
Application::import(PATH_APPLICATION . 'model/Entity.php');

class Admin extends Entity
{
    const SESSION_VAR  = '540236854b650';
    
    const ROLE_ADMIN   = 'ADMIN';
    const ROLE_MANAGER = 'MANAGER';

    public function init()
    {
        $this->setModelClass('AdminModel');
    }

    private $_login = '';
    private $_password = '';
    private $_salt = '';
    private $_lastLogin = 0;
    private $_lastLoginIp = 0;    
    private $_role = '';


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

    public function setRole($role) 
    {
        $this->_role = $role;

        return $this;
    }

    public function getRole()
    {
        return $this->_role;
    }

    public function formatFrom($from, $data) 
    {
        if ($from == 'DB') {
            $this->setLogin($data['Login'])
                 ->setPassword($data['Password'])
                 ->setSalt($data['Salt'])
                 ->setRole($data['Role'])
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
            case 'update' :
                $this->isValidLogin();
                
                $check = new Admin();
                $check->setLogin($this->getLogin())->fetch();
                
                if ($this->getPassword() && $this->getPassword() != $check->getPassword()) {
                    $this->isValidPassword();

                    $this->setSalt(uniqid());
                    $this->setPassword($this->saltPassword($this->getPassword()));
                }

                if ($this->getRole()) {
                    $this->isValidRole();    
                }
            break;
            case 'create':
                $this->isValidLogin();
                $this->isValidPassword();
                $this->isValidRole();

                // check exist
                $check = new Admin();
                $check->setLogin($this->getLogin());
                $exists = true;
                try {
                    $check->fetch();
                } catch (EntityException $e) {
                    if ($e->getCode() == 404) {
                        $exists = false;
                    }
                }
                if ($exists) {
                    throw new EntityException("login '" . $this->getLogin() . "' already exists");
                }

                $this->setSalt(uniqid());
                $this->setPassword($this->saltPassword($this->getPassword()));
            break;
            case 'delete':
                $this->isValidLogin();
                $this->fetch();
            break;
            default:
                throw new EntityException("Object does not pass validation", 400);
            break;
        }

        return true;
    }

    protected function isValidLogin($throwException = true)
    {
        if (!$this->getLogin() || !preg_match('/^[a-z0-9-_]{3,}$/', $this->getLogin())) {
            if ($throwException) {
                throw new EntityException("Invalid or empty login", 400);    
            }
            return false;
        }

        return true;
    }

    protected function isValidPassword($throwException = true)
    {
        if (!$this->getPassword()) {
            if ($throwException) {
                throw new EntityException("Password can not be empty", 400);
            }
            return false;
            
        }

        if (strlen($this->getPassword()) < 8) {
            if ($throwException) {
                throw new EntityException("Password length must be grater or equal 8", 400);       
            }
            return false;
        }

        return true;
    }

    protected function isValidRole($throwException = true)
    {   
        if (!in_array($this->getRole(), array(self::ROLE_ADMIN, self::ROLE_MANAGER))) {
            if ($throwException) {
                throw new EntityException("Invalid admin role", 400);           
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

        Session2::connect()->set(self::SESSION_VAR, $this);
        return true;
    }

    public function logout()
    {
        Session2::connect()->delete(self::SESSION_VAR);

        return true;
    }

    protected function saltPassword($password)
    {
        return md5(sha1($password) . $this->getSalt());
    }
}