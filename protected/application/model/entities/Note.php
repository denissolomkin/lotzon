<?php
Application::import(PATH_APPLICATION . 'model/Entity.php');

class Note extends Entity
{
    protected $_id    = '';
    protected $_playerId = '';
    protected $_adminId = '';
    protected $_adminName = '';
    protected $_text  = '';
    protected $_date  = '';
    
    public function init()
    {
        $this->setModelClass('NotesModel');
    }

    public function formatFrom($from, $data) 
    {
        if ($from == 'DB') {
            $this->setId($data['Id'])
                 ->setPlayerId($data['PlayerId'])
                 ->setAdminId($data['AdminId'])
                 ->setAdminName($data['AdminName'])
                 ->setDate($data['Date'])
                 ->setText($data['Text']);
        }

        return $this;
    }

    public function validate($action)
    {
        switch ($action) {
            case 'create' :
            case 'update' :
                if (!$this->getText()) {
                    throw new EntityException("Text can not be empty", 400);
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