<?php

class Blog extends Entity
{
    protected $_id          = 0;
    protected $_title       = '';
    protected $_img         = '';
    protected $_text        = '';
    protected $_lang        = '';
    protected $_dateCreated = 0;
    protected $_dateModify  = 0;
    protected $_enable      = false;

    public function init()
    {
        $this->setModelClass('BlogsModel');
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
        switch ($to) {
            case 'list':
                $ret = array(
                    'id'    => $this->getId(),
                    'img'   => $this->getImg(),
                    'title' => $this->getTitle(),
                    'date'  => $this->getDateCreated(),
                    'stat'  => array(
                        'comments' => CommentsModel::instance()->getCount('blog', $this->getId())
                    )
                );
                return $ret;
                break;
            case 'item':
                $ret = array(
                    'id'    => $this->getId(),
                    'img'   => $this->getImg(),
                    'title' => $this->getTitle(),
                    'text'  => $this->getText(),
                    'date'  => $this->getDateCreated(),
                    'stat'  => array(
                        'comments' => CommentsModel::instance()->getCount('blog', $this->getId())
                    )
                );
                return $ret;
                break;
            case 'similar':
                $ret = array(
                    'id'    => $this->getId(),
                    'img'   => $this->getImg(),
                    'title' => $this->getTitle()
                );
                return $ret;
                break;
        }

        return false;
    }

    public function validate($action)
    {
        switch ($action) {
            case 'create' :
            case 'update' :
                $this->setTitle(htmlspecialchars(strip_tags($this->getTitle())));
                if ((!$this->getText())or(!$this->getTitle())){
                    throw new EntityException("Text/title can not be empty", 400);
                }
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
