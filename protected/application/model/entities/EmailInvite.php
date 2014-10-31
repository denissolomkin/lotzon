<?php

class EmailInvite extends Entity
{
    const INVITE_COST = 10;

    private $_id    = 0;
    private $_email = 0;
    private $_date  = 0;
    private $_inviter = null;
    private $_hash = '';
    private $_valid  = '';

    public function init()
    {
        $this->setModelClass('EmailInvites');
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

    public function setDate($date) 
    {
        $this->_date = $date;

        return $this;
    }

    public function getDate() 
    {
        return $this->_date;
    }

    public function setInviter($inviter) 
    {
        $this->_inviter = $inviter;

        return $this;
    }

    public function getInviter() 
    {
        return $this->_inviter;
    }

    public function setHash($hash) 
    {
        $this->_hash = $hash;

        return $this;
    }

    public function getHash() 
    {
        return $this->_hash;
    }

    public function setValid($valid) 
    {
        $this->_valid = $valid;

        return $this;
    }

    public function getValid() 
    {
        return $this->_valid;
    }

    public function validate($action, $params = array()) 
    {
        switch ($action) {
            case 'create':
                if (!$this->getInviter()->getId()) {
                    throw new EntityException("FRAUD", 400);
                }
                if (!$this->getEmail()) {
                    throw new EntityException("EMPTY_EMAIL", 400);   
                }
                $this->validEmail();

                if (!$this->getInviter()->getInvitesCount()) {
                    throw new EntityException("NO_MORE_INVITES", 403);                    
                }
                // check for player already exists
                $test = new Player();
                $test->setEmail($this->getEmail());
                try {
                    $test->fetch();
                    if ($test->getId()) {
                        throw new EntityException("EMAIL_ALREADY_REGISTERED", 403);    
                    }
                } catch (EntityException $e) {
                    if ($e->getCode() != 404) {
                        throw new EntityException($e->getMessage(), $e->getCode());
                    }
                }
                $this->setHash(md5(uniqid()));
                $this->setValid(false);
                try {
                    EmailInvites::instance()->getInvite($this->getEmail());
                    
                    throw new EntityException("ALREADY_INVITED", 403);
                } catch (ModelException $e) {
                    if ($e->getCode() != 404) {
                        throw new EntityException($e->getMessage(), $e->getCode());
                    }
                }

            break;
            
            default:
                # code...
                break;
        }
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
}