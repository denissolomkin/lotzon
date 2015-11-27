<?php
Application::import(PATH_APPLICATION . 'model/Entity.php');

class LinkRedirect extends Entity
{
    protected $_uin  = '';
    protected $_link = '';

    public function init()
    {
        $this->setModelClass('LinkRedirectModel');
    }

    public function formatFrom($from, $data)
    {
        if ($from == 'DB') {
            $this->setUin($data['Uin'])
                 ->setLink($data['Link']);
        }
        return $this;
    }

    public function validate($action)
    {
        switch ($action) {
            case 'create' :
                if (substr($this->getLink(),0,4)!='http') {
                    throw new EntityException("Link must be start from http......", 400);
                }
                break;
            case 'update' :
                break;
            case 'fetch':
                break;
            case 'delete':
                break;
            default:
                throw new EntityException('Object does not pass validation', 400);
                break;
        }
    }

}
