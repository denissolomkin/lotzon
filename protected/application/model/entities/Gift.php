<?php

class Gift extends Entity
{
    protected $_id           = 0;
    protected $_playerId     = 0;
    protected $_giftPlayerId = 0;
    protected $_objectType   = '';
    protected $_objectId     = '';
    protected $_currency     = '';
    protected $_sum          = 0;
    protected $_equivalent   = 0;
    protected $_date         = 0;
    protected $_expiryDate   = 0;
    protected $_used         = false;

    public function init()
    {
        $this->setModelClass('GiftsModel');
    }

    public function formatFrom($from, $data)
    {
        if ($from == 'DB') {
            foreach ($data as $key=>$value) {
                $method = 'set'.$key;
                $this->$method($value);
            }
        }
        return $this;
    }

    public function exportTo($to)
    {
        return false;
    }

    public function validate($action)
    {
        switch ($action) {
            case 'create' :
            case 'update' :
                break;
            case 'fetch' :
            case 'delete':
                if (!$this->getId()) {
                    throw new EntityException("Identifier can't be empty", 400);
                }
                break;
            default:
                throw new EntityException('Object does not pass validation', 400);
                break;
        }
        return true;
    }
}
