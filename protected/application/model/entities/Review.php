<?php
Application::import(PATH_APPLICATION . 'model/Entity.php');

class Review extends Entity
{
    const IMAGE_WIDTH = 960;//480;
    const IMAGE_HEIGHT = 600;//150;

    protected $_id            = 0;
    protected $_reviewId      = null;
    protected $_status        = '';
    protected $_image         = '';
    protected $_text          = '';
    protected $_playerName    = '';
    protected $_complain    = '';
    protected $_moderatorId   = 0;
    protected $_moderatorName = '';
    protected $_playerAvatar  = '';
    protected $_playerId      = '';
    protected $_playerEmail   = '';
    protected $_userId        = 0;
    protected $_promo         = 0;
    protected $_userName      = '';
    protected $_date          = '';
    protected $_toPlayerId    = null;
    protected $_module        = 'comments';
    protected $_objectId      = 0;

    public function init()
    {
        $this->setModelClass('ReviewsModel');
    }   

    public function getText()
    {
        return htmlspecialchars_decode($this->_text);
    }

    public function getDate($format = null)
    {
        $date = $this->_date;

        if (!is_null($format)) {
            $date = date($format, $this->_date);
        }

        return $date;
    }

    public function formatFrom($from, $data) 
    {

        if ($from == 'DB') {
            $this->setId($data['Id'])
                 ->setReviewId($data['ParentId'])
                 ->setStatus($data['Status'])
                 ->setPromo($data['IsPromo'])
                 ->setPlayerId($data['PlayerId'])
                 ->setPlayerEmail($data['PlayerEmail'])
                 ->setPlayerAvatar($data['PlayerAvatar'])
                 ->setPlayerName($data['PlayerName'])
                 ->setUserId($data['UserId'])
                 ->setUserName($data['UserName'])
                 ->setModeratorId($data['ModeratorId'])
                 ->setModeratorName($data['ModeratorName'])
                 ->setComplain($data['Complain'])
                 ->setDate($data['Date'])
                 ->setImage($data['Image'])
                 ->setText($data['Text'])
                 ->setModule($data['Module'])
                 ->setObjectId($data['ObjectId']);
        }

        return $this;
    }

    public function validate($action)
    {
        switch ($action) {
            case 'create' :
            case 'update' :
                $this->setText(trim(htmlspecialchars(strip_tags($this->getText()))));
                if (!$this->getText() || $this->getText() == '') {
                    throw new EntityException("Text can not be empty", 400);
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
    }
}