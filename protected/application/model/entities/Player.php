<?php

use Symfony\Component\HttpFoundation\Session\Session;

Application::import(PATH_APPLICATION . 'model/Entity.php');
Application::import(PATH_APPLICATION . 'model/entities/Transaction.php');

class Player extends Entity
{
    const IDENTITY = "player_session";
    const AUTOLOGIN_COOKIE = "autologin";
    const AUTOLOGIN_HASH_COOKIE   = "autologinHash";
    const PLAYERID_COOKIE   = "playerId";
    // 3 months
    const AUTOLOGIN_COOKIE_TTL = 7776000;


    const AVATAR_WIDTH  = 160;
    const AVATAR_HEIGHT = 160;

    const REFERAL_INVITE_COST = 20;
    const SOCIAL_POST_COST = 20;
    const SOCIAL_PROFILE_COST = 40;

    private $_id         = 0;
    private $_email      = '';
    private $_password   = '';
    private $_salt       = '';

    private $_socialid     = 0;
    private $_socialemail  = '';
    private $_socialname   = '';
    private $_socialenable = 1;

    private $_nicName    = '';
    private $_name       = '';
    private $_surname    = '';
    private $_secondName = '';
    private $_avatar     = '';
    
    private $_phone      = '';
    private $_birthday   = '';

    private $_favoriteCombination = array();
    private $_visible             = false;
    private $_ban             = false;

    private $_dateRegistered = '';
    private $_dateLastLogin  = '';
    private $_dateLastNotice = '';
    private $_dateLastChance = '';
    private $_dateAdBlocked  = '';
    private $_country        = '';

    private $_generatedPassword = '';

    private $_points      = 0;
    private $_money       = 0;
    private $_gamesPlayed = 0;

    private $_invitesCount = 0;
    private $_socialPostsCount = 0;

    private $_online     = 0;
    private $_onlineTime = 0;
    private $_adBlock    = 0;
    private $_webSocket  = 0;

    private $_valid = 0;
    private $_hash = '';
    private $_ip = '';
    private $_lastip = '';
    private $_cookieId = 0;

    private $_referalId = 0;
    private $_referalPaid = 0;

    private $_additionalData = array();
    // filled only when list of players fetched
    private $_isTicketsFilled = array();
    private $_counters = array();

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

    public function setBan($status)
    {
        $this->_ban = $status;

        return $this;
    }

    public function getBan()
    {
        return $this->_ban;
    }

    public function setSocialEnable($socialenable)
    {
        $this->_socialenable = $socialenable;

        return $this;
    }

    public function getSocialEnable()
    {
        return $this->_socialenable;
    }

    public function setSocialId($socialid)
    {
        $this->_socialid = $socialid;

        return $this;
    }

    public function getSocialId()
    {
        return $this->_socialid;
    }

    public function setSocialName($socialname)
    {
        $this->_socialname = $socialname;

        return $this;
    }

    public function getSocialName()
    {
        return $this->_socialname;
    }

    public function setSocialEmail($socialemail)
    {
        $this->_socialemail = $socialemail;

        return $this;
    }

    public function getSocialEmail()
    {
        return $this->_socialemail;
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

    public function setCookieId($id)
    {
        $this->_cookieId = $id;

        return $this;
    }

    public function getCookieId()
    {
        return $this->_cookieId;
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

    public function setDateLastChance($dateLastChance)
    {
        $this->_dateLastChance = $dateLastChance;

        return $this;
    }

    public function getDateLastChance($format = null)
    {
        $date = $this->_dateLastChance;

        if (!is_null($format)) {
            $date = date($format, $this->_dateLastChance);
        }

        return $date;
    }

    public function setDateLastNotice($dateLastNotice)
    {
        $this->_dateLastNotice = $dateLastNotice;

        return $this;
    }

    public function getDateLastNotice($format = null)
    {
        $date = $this->_dateLastNotice;

        if (!is_null($format)) {
            $date = date($format, $this->_dateLastNotice);
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
        if (!is_array($this->_favoriteCombination)) {
            return array();
        }
        return $this->_favoriteCombination;
    }

    public function setPoints($points)
    {
        $this->_points = $points;

        return $this;
    }

    public function getPoints()
    {
        return $this->_points;
    }  

    public function setMoney($money)
    {
        $this->_money = $money;

        return $this;
    }

    public function getMoney()
    {
        return $this->_money;
    }

    public function setGamesPlayed($gamesPlayed)
    {
        $this->_gamesPlayed = $gamesPlayed;

        return $this;
    }

    public function getGamesPlayed()
    {
        return $this->_gamesPlayed;
    }  

    public function getInvitesCount()
    {
        return $this->_invitesCount;
    }

    public function setInvitesCount($ic)
    {
        $this->_invitesCount = $ic;

        return $this;
    }

    public function getSocialPostsCount()
    {
        return $this->_socialPostsCount;
    }

    public function setSocialPostsCount($sp)
    {
        $this->_socialPostsCount = $sp;

        return $this;
    }

    public function setOnlineTime($time) 
    {
        $this->_onlineTime  = $time;
        return $this;
    }

    public function getOnlineTime($format = null)
    {
        $date = $this->_onlineTime;

        if (!is_null($format)) {
            $date = date($format, $this->_onlineTime);
        }

        return $date;
    }

    public function setOnline($online)
    {
        $this->_online = $online;

        return $this;
    }

    public function isOnline()
    {
        return $this->_online;
    }

    public function decrementInvitesCount()
    {
        $this->setInvitesCount($this->getInvitesCount() - 1);
        $model = $this->getModelClass();

        try {
            $model::instance()->decrementInvitesCount($this);
        } catch (ModelException $e) {
            throw new EntityException('INTERNAL_ERROR', 500);
        }
        
        return $this;
    }


    public function updateLastChanced()
    {
        $model = $this->getModelClass();
        $check=false;

        try {
            if($model::instance()->updateLastChanced($this)) {
                $this->setDateLastChance(time());
                $check=true;
            }
        } catch (ModelException $e) {
            throw new EntityException('INTERNAL_ERROR', 500);
        }

        return $check;

    }


    public function decrementSocialPostsCount()
    {
        $this->setSocialPostsCount($this->getSocialPostsCount() - 1);
        $model = $this->getModelClass();

        try {
            $model::instance()->decrementSocialPostsCount($this);
        } catch (ModelException $e) {
            throw new EntityException('INTERNAL_ERROR', 500);
        }
        
        return $this;
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

    public function setAdBlock($check)
    {
        $this->_adBlock = $check;

        return $this;
    }

    public function getAdBlock()
    {
        return $this->_adBlock;
    }

    public function setDateAdBlocked($date)
    {
        if($date)
            $this->_dateAdBlocked = $date;

        return $this;
    }

    public function getDateAdBlocked()
    {
        return $this->_dateAdBlocked;
    }

    public function setWebSocket($check)
    {
        $this->_webSocket = $check;

        return $this;
    }

    public function getWebSocket()
    {
        return $this->_webSocket;
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

    public function setIP($ip) 
    {
        $this->_ip = $ip;

        return $this;
    }

    public function getIP()
    {
        return $this->_ip;
    }

    public function setLastIP($ip)
    {
        if(!$this->getIP())
            $this->setIP($ip);
        elseif($ip!=$this->getIP())
            $this->_lastip = $ip;

        return $this;
    }

    public function getLastIP()
    {
        return $this->_lastip;
    }

    public function setReferalId($referalId) 
    {
        $this->_referalId = $referalId;

        return $this;
    }

    public function getReferalId()
    {
        return $this->_referalId;
    }

    public function isReferalPaid()
    {
        return $this->_referalPaid;
    }

    public function setReferalPaid($status)
    {
        $this->_referalPaid = $status;

        return $this;
    }

    public function markReferalPaid()
    {
        $model = $this->getModelClass();

        try {
            $model::instance()->getProcessor()->markReferalPaid($this);
        } catch (ModelException $e) {
            throw new EntityException('INTERNAL_ERROR', 500);
        }
        
        return $this;   
    }

    public function writeLog($options=array())
    {
        $model = $this->getModelClass();

        try {
            $model::instance()->getProcessor()->writeLog($this, $options);
        } catch (ModelException $e) {
            throw new EntityException('INTERNAL_ERROR', 500);
        }

        return $this;
    }

    public function reportTrouble($trouble)
    {
        $model = $this->getModelClass();

        try {
            $model::instance()->getProcessor()->reportTrouble($this, $trouble);
        } catch (ModelException $e) {
            throw new EntityException('INTERNAL_ERROR', 500);
        }

        return $this;
    }

    public function setAdditionalData($additionalData=null)
    {

        if(is_array($additionalData)){
            $additionalData=array_merge($this->getAdditionalData(),$additionalData);
            foreach($additionalData as $provider => $info)
                $this->_additionalData[$provider] = $info;
        }

        return $this;
    }

    public function getAdditionalData()
    {
        return $this->_additionalData;
    }

    public function isTicketsFilled()
    {
        return $this->_isTicketsFilled;
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

    public function hidePassword($pass)
    {
        if(strlen($pass)>4){
            $pass=substr($pass,0, 2).str_pad('', strlen($pass)-4,"*"). substr($pass,strlen($pass)-2, 2);
        }
        return $pass;
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
                $this->validIp();
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
                $this->getSocialId() || $this->getId() || $this->validEmail() || $this->validIp();
            break;
            case 'login' :
                $this->validEmail();
                $this->validIp();
                if (empty($params['password'])) {
                    throw new EntityException('EMPTY_PASSWORD', 400);
                }
                $this->fetch();
                if (!$this->getValid()) {
                    $this->writeLog(array('action'=>'LOGIN_DENIED', 'desc'=>'EMAIL_NOT_VALIDATED', 'status'=>'danger'));
                    throw new EntityException("EMAIL_NOT_VALIDATED", 400);
                }
            break;
            case 'update' :
                $this->validEmail();

                $this->setNicName(trim(htmlspecialchars(strip_tags($this->getNicName()))));
                $this->checkNickname();
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

    public function uploadAvatar($photoURL=null)
    {

        try {
            if($photoURL)
                $image = WideImage::load($photoURL);
            else
                $image = WideImage::loadFromUpload('image');

            $image = $image->resize(Player::AVATAR_WIDTH, Player::AVATAR_WIDTH);
            $image = $image->crop("center", "center", Player::AVATAR_WIDTH, Player::AVATAR_WIDTH);

            $imageName = uniqid() . ".jpg";
            $saveFolder = PATH_FILESTORAGE . 'avatars/' . (ceil($this->getId() / 100)) . '/';

            if (!is_dir($saveFolder)) {
                mkdir($saveFolder, 0777);
            }

            $image->saveToFile($saveFolder . $imageName, 100);
            // remove old one
            if ($this->getAvatar()) {
                @unlink($saveFolder . $this->getAvatar());
            };

            $this->setAvatar($imageName)->saveAvatar();

        } catch (EntityException $e) {
            // throw new EntityException($e->getMessage(), $e->getCode());
        }

        if(!$photoURL)
            return $imageName;
        else
            return $this;

    }

    public function saveAvatar() 
    {
        $model = $this->getModelClass();

        try {
            $model::instance()->saveAvatar($this);
        } catch (ModelException $e) {
            throw new EntityException('INTERNAL_ERROR', 500);
        }
        
        return $this;   
    }

    public function getCounters()
    {
        return $this->_counters;
    }

    public function setCounters($counters)
    {
        return $this->_counters=$counters;
    }

    public function updateCounters()
    {
        $model = $this->getModelClass();

        try {
            $counters=$model::instance()->updateCounters($this);
            $this->setCounters($counters);
            return $counters;
        } catch (ModelException $e) {
            throw new EntityException($e->getMessage(), $e->getCode());

        }

    }

    protected function checkNickname()
    {
        $model = $this->getModelClass();

        try {
            $model::instance()->checkNickname($this);
        } catch (ModelException $e) {
            if ($e->getCode() == 403) {
                throw new EntityException("NICKNAME_BUSY", 400);    
            }
            throw new EntityException($e->getMessage(), $e->getCode());
            
        }

        return true;
    }

    protected function validIp()
    {

        if (is_array(Config::instance()->blockedIps) && (in_array($this->getIp(), Config::instance()->blockedIps) || $this->getLastIp() && in_array($this->getLastIp(), Config::instance()->blockedIps))) {
            throw new EntityException('BLOCKED_IP', 400);
        }

        return true;
    }

    protected function validEmail($throwException = true)
    {

        if (!filter_var($this->getEmail(), FILTER_VALIDATE_EMAIL)) {
            if ($throwException) {
                throw new EntityException('INVALID_EMAIL', 400);
            }
        } 

        $emailDomain = substr(strrchr($this->getEmail(), "@"), 1);
        if (in_array($emailDomain, Config::instance()->blockedEmails)) {
            throw new EntityException('BLOCKED_EMAIL_DOMAIN', 400);
        }

        return true;
    }

    public function updateSocial()
    {
        $model = $this->getModelClass();

        try {
            $model::instance()->updateSocial($this);
        } catch (ModelException $e) {
            throw new EntityException('INTERNAL_ERROR', 500);
        }

        return $this;
    }

    public function updateBalance($currency, $quantity=0)
    {
        $model = $this->getModelClass();

        try {
            $model::instance()->updateBalance($this, $currency, $quantity);
        } catch (ModelException $e) {
            throw new EntityException('INTERNAL_ERROR', 500);
        }

        return $this;
    }

    public function getBalance()
    {
        $model = $this->getModelClass();

        try {
            return $model::instance()->getBalance($this);
        } catch (ModelException $e) {
            throw new EntityException('INTERNAL_ERROR', 500);
        }

    }

    public function disableSocial()
    {
        $model = $this->getModelClass();

        try {
            $socialData=$this->getAdditionalData()[$this->getSocialName()];
            $socialData['enabled']=0;
            $this->setAdditionalData(array($this->getSocialName()=>$socialData))->update();
            $model::instance()->disableSocial($this);
        } catch (ModelException $e) {
            throw new EntityException('INTERNAL_ERROR', 500);
        }

        return $this;
    }

    public function isSocialUsed()
    {
        $model = $this->getModelClass();
        return $model::instance()->isSocialUsed($this);
    }

    public function create()
    {
        $psw=$this->generatePassword();
        $this->setPassword($this->compilePassword($psw));

        parent::create();

        $this->writeLog(array('action'=>'PLAYER_CREATED', 'desc'=>$this->hidePassword($psw), 'status'=>'success'));

        Common::sendEmail($this->getEmail(), 'Регистрация на www.lotzon.com', 'player_registration', array(
            'login' => $this->getEmail(),
            'password'  => $this->_generatedPassword,
            'hash'  => $this->getHash(),
        ));
        //$this->login($this->_generatedPassword);

        return $this;
    }

    public function addMoney($quantity, $description = '', $inplaceUpdate = true) {

        $this->setMoney($this->getBalance()['Money'] + $quantity);

        if ($inplaceUpdate) {
            $this->updateBalance('Money', $quantity);
        }
    
        $transaction = new Transaction();
        $transaction->setPlayerId($this->getId())
                    ->setSum($quantity)
                    ->setBalance($this->getMoney())
                    ->setCurrency(GameSettings::CURRENCY_MONEY)
                    ->setDescription($description);
        $transaction->create();
        
        return $this;
    }

    public function addPoints($quantity, $description = '', $inplaceUpdate = true) {
        //@TODO process transaction

        $this->setPoints($this->getBalance()['Points'] + $quantity);

        if ($inplaceUpdate) {
            $this->updateBalance('Points', $quantity);
        }

        $transaction = new Transaction();
        $transaction->setPlayerId($this->getId())
                    ->setBalance($this->getPoints())
                    ->setSum($quantity)
                    ->setCurrency(GameSettings::CURRENCY_POINT)
                    ->setDescription($description);
        $transaction->create();

        return $this;
    }

    public function login($password)
    {
        $password=trim($password);
        $this->validate('login', array(
            'password' => $password,
        ));

        try {
            $this->fetch();
        } catch (EntityException $e) {
            if ($e->getCode() == 404) {
                throw new EntityException("PLAYER_NOT_FOUND", 404);
            } else {
                $this->writeLog(array('action'=>'LOGIN_DENIED', 'desc'=>'INTERNAL_ERROR', 'status'=>'danger'));
                throw new EntityException("INTERNAL_ERROR", 500);
            }
        }
        if ($this->getBan()) {
            throw new EntityException("ACCESS_DENIED", 403);
        }

        if ($this->getPassword() !== $this->compilePassword($password)) {
            $this->writeLog(array('action'=>'INVALID_PASSWORD', 'desc'=>$this->hidePassword($password), 'status'=>'danger'));
            throw new EntityException("INVALID_PASSWORD", 403);
        }

        if(!$_COOKIE[self::PLAYERID_COOKIE])
            setcookie(self::PLAYERID_COOKIE, $this->getId(), time() + self::AUTOLOGIN_COOKIE_TTL, '/');

        $this->setDateLastLogin(time())
            ->setCookieId(($_COOKIE[self::PLAYERID_COOKIE]?:$this->getId()))
            ->setLastIp(Common::getUserIp())
            ->payReferal()
            ->update();

        $session = new Session();
        $session->set(Player::IDENTITY, $this);

        return $this;
    }


    public function payInvite()
    {

        // check invites
        $invite = false;
        try {
            $invite = EmailInvites::instance()->getInvite($this->getEmail());
        } catch (ModelException $e) {}

        if ($invite && $invite->getValid()) {

            // mark referal unpaid for preverse of double points
            if ($this->getReferalId() && !$this->isReferalPaid())
                try {
                    $this->markReferalPaid();
                } catch (EntityException $e) {}

            // add bonuses to inviter and delete invite
            try {
                $invite->getInviter()->addPoints(EmailInvite::INVITE_COST, 'Приглашение друга ' . $this->getEmail());
                $invite->delete();
            } catch (EntityException $e) {}
        }

        return $this;
    }

    public function payReferal()
    {

        // add referal points on first login
        if ($this->getReferalId() && !$this->isReferalPaid()) {
            try {
                $refPlayer = new Player();
                $refPlayer->setId($this->getReferalId())->fetch();
                $refPlayer->addPoints(Player::REFERAL_INVITE_COST, 'Регистрация по вашей ссылке #'.$this->getId());
                $this->markReferalPaid();
            } catch (EntityException $e) {}
        }

        return $this;
    }

    public function changePassword($password) 
    {
        $this->setSalt("");
        $this->setPassword($this->compilePassword($password));

        $model = $this->getModelClass();

        try {
            $model::instance()->changePassword($this);
        } catch (ModelException $e) {
            throw new EntityException('INTERNAL_ERROR', 500);
        }
        
        return $this;   
    }

    public function updateLastNotice()
    {
        $this->setDateLastNotice(time());
        $model = $this->getModelClass();

        try {
            $model::instance()->updateLastNotice($this);
        } catch (ModelException $e) {
            throw new EntityException('INTERNAL_ERROR', 500);
        }
    }

    public function markOnline()
    {
        $this->setOnline(true)
             ->setOnlineTime(time());

        $model = $this->getModelClass();

        try {
            $model::instance()->markOnline($this);
        } catch (ModelException $e) {
            throw new EntityException('INTERNAL_ERROR', 500);
        }
    }

    public function formatFrom($from, $data) 
    {
        if ($from == 'DB') {
            $this->setId($data['Id'])
                 ->setBan($data['Ban'])
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
                 ->setDateLastNotice($data['DateNoticed'])
                 ->setDateLastChance($data['DateChanced'])
                 ->setCountry($data['Country'])
                 ->setAvatar($data['Avatar'])
                 ->setVisibility((boolean)$data['Visible'])
                 ->setFavoriteCombination(!empty($data['Favorite']) ? @unserialize($data['Favorite']) : array())
                 ->setPoints($data['Points'])
                 ->setMoney($data['Money'])
                 ->setGamesPlayed($data['GamesPlayed'])
                 ->setInvitesCount($data['InvitesCount'])
                 ->setSocialPostsCount($data['SocialPostsCount'])
                 ->setOnline($data['Online'])
                 ->setOnlineTime($data['OnlineTime'])
                 ->setAdBlock($data['AdBlock'])
                 ->setDateAdBlocked($data['DateAdBlocked'])
                 ->setWebSocket($data['WebSocket'])
                 ->setCookieId($data['CookieId'])
                 ->setIp($data['Ip'])
                 ->setLastIp($data['LastIp'])
                 ->setHash($data['Hash'])
                 ->setValid($data['Valid'])
                 ->setReferalId($data['ReferalId'])
                 ->setReferalPaid($data['ReferalPaid'])
                 ->setAdditionalData(!empty($data['AdditionalData']) ? @unserialize($data['AdditionalData']) : null);

            if ($data['TicketsFilled']) {
                $this->_isTicketsFilled = $data['TicketsFilled'];
            }

            if (isset($data['CountIp'])) {
                $this->setCounters(array(
                    'Notice' => $data['CountNotice'],
                    'Note' => $data['CountNote'],
                    'AdBlock' => $data['CountAdBlock'],
                    'Log' => $data['CountLog'],
                    'Ip' => $data['CountIp'],
                    'MyReferal' => $data['CountMyReferal'],
                    'Referal' => $data['CountReferal'],
                    'Order' => ($data['CountMoneyOrder']+$data['CountShopOrder']),
                    'Review' => $data['CountReview'],
                    'CookieId' => $data['CountCookieId'],
                ));
            }
        }

        return $this;
    }

    public function generateAutologinHash()
    {
        return md5($this->getEmail() . $this->getIp() . $this->getSalt());
    }

    public function enableAutologin()
    {
        setcookie(self::AUTOLOGIN_COOKIE, $this->getEmail(), time() + self::AUTOLOGIN_COOKIE_TTL, '/', false, true);
        setcookie(self::AUTOLOGIN_HASH_COOKIE, $this->generateAutologinHash(), time() + self::AUTOLOGIN_COOKIE_TTL, '/', false, true);

        return $this;
    }

    public function disableAutologin()
    {
        setcookie(self::AUTOLOGIN_COOKIE, "", -1, '/');
        setcookie(self::AUTOLOGIN_HASH_COOKIE, "", -1, '/');
    }

}