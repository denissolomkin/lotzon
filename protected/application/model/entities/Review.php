<?php
Application::import(PATH_APPLICATION . 'model/Entity.php');

class Review extends Entity
{
    const IMAGE_WIDTH = 960;//480;
    const IMAGE_HEIGHT = 600;//150;

    private $_id            = 0;
    private $_reviewId      = null;
    private $_status        = '';
    private $_image         = '';
    private $_text          = '';
    private $_playerName    = '';
    private $_playerAvatar  = '';
    private $_playerId      = '';
    private $_playerEmail   = '';
    private $_userId        = 0;
    private $_isPromo        = 0;
    private $_userName      = '';
    private $_date          = '';
    
    public function init()
    {
        $this->setModelClass('ReviewsModel');
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

    public function setReviewId($id)
    {
        $this->_reviewId = $id;

        return $this;
    }

    public function getReviewId()
    {
        return $this->_reviewId;
    }

    public function setPromo($bool)
    {
        $this->_isPromo = $bool;

        return $this;
    }

    public function isPromo()
    {
        return $this->_isPromo;
    }

    public function setUserId($id)
    {
        $this->_userId = $id;

        return $this;
    }

    public function getUserId()
    {
        return $this->_userId;
    }

    public function setUserName($name)
    {
        $this->_userName = $name;

        return $this;
    }

    public function getUserName()
    {
        return $this->_userName;
    }

    public function setPlayerId($playerId)
    {
        $this->_playerId = $playerId;

        return $this;
    }

    public function getPlayerId()
    {
        return $this->_playerId;
    }

    public function setPlayerEmail($playerEmail)
    {
        $this->_playerEmail = $playerEmail;

        return $this;
    }

    public function getPlayerEmail()
    {
        return $this->_playerEmail;
    }

    public function setPlayerAvatar($playerAvatar)
    {
        $this->_playerAvatar = $playerAvatar;

        return $this;
    }

    public function getPlayerAvatar()
    {
        return $this->_playerAvatar;
    }

    public function setPlayerName($playerName)
    {
        $this->_playerName = $playerName;

        return $this;
    }

    public function getPlayerName()
    {
        return $this->_playerName;
    }

    public function setText($text)
    {
        $this->_text = $text;

        return $this;
    }

    public function getText()
    {
        return htmlspecialchars_decode($this->_text);
    }

    public function setStatus($status)
    {
        $this->_status = $status;

        return $this;
    }

    public function getStatus()
    {
        return $this->_status;
    }

    public function setImage($image) {
        $this->_image = $image;

        return $this;
    }

    public function getImage()
    {
        return $this->_image;
    }

    public function setDate($date) 
    {
        $this->_date = $date;

        return $this;
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
                 ->setReviewId($data['ReviewId'])
                 ->setStatus($data['Status'])
                 ->setPromo($data['IsPromo'])
                 ->setPlayerId($data['PlayerId'])
                 ->setPlayerEmail($data['PlayerEmail'])
                 ->setUserId($data['UserId'])
                 ->setUserName($data['UserName'])
                 ->setPlayerAvatar($data['PlayerAvatar'])
                 ->setPlayerName($data['PlayerName'])
                 ->setDate($data['Date'])
                 ->setImage($data['Image'])
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
                $this->setText(htmlspecialchars(strip_tags($this->getText())));

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