<?php

use Symfony\Component\HttpFoundation\Session\Session;

Application::import(PATH_APPLICATION . 'model/Entity.php');
Application::import(PATH_APPLICATION . 'model/entities/Transaction.php');

class Player extends Entity
{
    const IDENTITY = "player_session";
    const AUTOLOGIN_COOKIE = "autologin";
    const AUTOLOGIN_HASH_COOKIE   = "autologinHash";
    const PLAYERID_COOKIE   = "_ga_ca";
    // 3 months
    const AUTOLOGIN_COOKIE_TTL = 7776000;

    const AVATAR_WIDTH  = 160;
    const AVATAR_HEIGHT = 160;

    static $MASK = array(
        'dates'=>array('Moment','QuickGame','ChanceGame','AdBlockLast','AdBlocked','WSocket','TeaserClick','Ping','Logined'),
        'counters'=>array('WhoMore','SeaBattle','Notice','Note','AdBlock','Log','Ip','MyReferal','Referal','MyInviter','Inviter','Order','Review','CookieId'));

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
    private $_agent     = '';
    private $_referer     = '';

    private $_phone      = null;
    private $_yandexMoney = null;
    private $_qiwi       = null;
    private $_webMoney   = null;
    private $_birthday   = '';

    private $_favoriteCombination = array();
    private $_visible             = false;
    private $_ban             = false;

    private $_dates = array();
    private $_dateRegistered = '';
    private $_dateLastLogin  = '';
    private $_dateLastNotice = '';
    private $_dateLastChance = '';
    private $_dateLastQuickGame = '';
    private $_dateAdBlocked  = '';
    private $_country        = '';
    private $_lang        = '';

    private $_generatedPassword = '';

    private $_points      = 0;
    private $_money       = 0;
    private $_gamesPlayed = 0;

    private $_invitesCount = 0;

    /**
     * @var array Счётчик оставшихся оплачиваемых реф.ссылок в соц.сетях [имя соц.сети]=>[количество]
     */
    private $_socialPostsCount = array();

    //private $_online     = 0;
    private $_onlineTime = 0;
    private $_adBlock    = 0;
    private $_webSocket  = 0;

    private $_valid = 0;
    private $_hash = '';
    private $_ip = '';
    private $_lastip = '';
    private $_cookieId = 0;

    private $_inviterId = 0;
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

    public function setReferer($referer)
    {
        $this->_referer = $referer;

        return $this;
    }

    public function getReferer()
    {
        return $this->_referer;
    }

    public function setWebmoney($val)
    {
        $this->_webMoney = $val;

        return $this;
    }

    public function getWebMoney()
    {
        return $this->_webMoney;
    }

    public function setQiwi($val)
    {
        $this->_qiwi = $val;

        return $this;
    }

    public function getQiwi()
    {
        return $this->_qiwi;
    }

    public function setYandexMoney($val)
    {
        $this->_yandexMoney = $val;

        return $this;
    }

    public function getYandexMoney()
    {
        return $this->_yandexMoney;
    }

    public function setAgent($agent)
    {
        $this->_agent = $agent;

        return $this;
    }

    public function getAgent()
    {
        return $this->_agent;
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

    public function setDateLastMoment($date)
    {
        $this->_dateLastMoment = $date;

        return $this;
    }

    public function getDateLastMoment($format = null)
    {
        $date = $this->_dateLastMoment;

        if (!is_null($format)) {
            $date = date($format, $this->_dateLastMoment);
        }

        return $date;
    }

    public function setDateLastQuickGame($date)
    {
        $this->_dateLastQuickGame = $date;

        return $this;
    }

    public function getDateLastQuickGame($format = null)
    {
        $date = $this->_dateLastQuickGame;

        if (!is_null($format)) {
            $date = date($format, $this->_dateLastQuickGame);
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

    public function setLang($lang)
    {

        $this->_lang =
            \LanguagesModel::instance()->isLang($lang)
                ? $lang
                : \CountriesModel::instance()->defaultLang();

        return $this;
    }

    public function getLang()
    {
        return $this->_lang;
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

    /**
     * Возвращает счётчик остатка оплачиваемых постов для соц.сети $provider
     *
     * @author subsan <subsan@online.ua>
     *
     * @param  string|null     $provider Имя социальной сети | Весь массив счётчиков
     * @return int|array|false           Количество оставшихся постов | Весь массив | не найден счётчик для соц.сети $provider
     */
    public function getSocialPostsCount($provider = null)
    {
        if ($provider === null) {
            return $this->_socialPostsCount;
        }
        if (isset($this->_socialPostsCount[$provider])) {
            return $this->_socialPostsCount[$provider];
        } else {
            return false;
        }
    }

    /**
     * Устанавливает счётчик остатка оплачиваемых постов для соц.сетей
     *
     * @author subsan <subsan@online.ua>
     *
     * @param  mixed[] $sp Массив [имя счётчика => количество оставшихся постов]
     * @return object      this
     */
    public function setSocialPostsCount($sp)
    {
        if (is_array($sp)) {
            foreach ($sp as $key => $value) {
                $this->_socialPostsCount[$key] = $value;
            }
        }
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
/*
    public function setOnline($online)
    {
        $this->_online = $online;

        return $this;
    }

    public function isOnline()
    {
        return $this->_online;
    }
*/
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

    public function checkDate($key)
    {
        $model = $this->getModelClass();
        $check = false;

        try {
            $check = $model::instance()->checkDate($key, $this);
        } catch (ModelException $e) {
            throw new EntityException('INTERNAL_ERROR', 500);
        }

        if($check)
            $this->setDates($key, time());

        return $check;
    }

    public function checkLastGame($key)
    {
        $model = $this->getModelClass();

        try {
            return $model::instance()->checkLastGame($key, $this);
        } catch (ModelException $e) {
            throw new EntityException('INTERNAL_ERROR', 500);
        }

        return false;
    }

    /**
     * Декремент счётчика оплачиваемых реф.ссылок в соц.сети $provider
     *
     * @author subsan <subsan@online.ua>
     *
     * @param  string $provider Имя социальной сети
     * @return object           this
     */
    public function decrementSocialPostsCount($provider)
    {
        $this->setSocialPostsCount(array($provider => ($this->getSocialPostsCount($provider) - 1)));
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
        if(!$this->getIP()){
            $this->setIp($ip);
        } elseif($ip!=$this->getIP()){
            $this->_lastip = $ip;
        }
        return $this;
    }

    public function getLastIP()
    {
        return $this->_lastip;
    }

    public function setInviterId($inviterId)
    {
        $this->_inviterId = $inviterId;

        return $this;
    }

    public function getInviterId()
    {
        return $this->_inviterId;
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

    public function updateInvite()
    {
        $model = $this->getModelClass();

        try {
            $model::instance()->getProcessor()->updateInvite($this);
        } catch (ModelException $e) {
            throw new EntityException('INTERNAL_ERROR', 500);
        }

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

    public function writeLogin()
    {
        $model = $this->getModelClass();

        try {
            $model::instance()->getProcessor()->writeLogin($this);
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

                if ($this->getPhone()){
                    if(!preg_match('/^[+0-9\- ()]*$/', $this->getPhone()))
                        throw new EntityException("INVALID_PHONE_FORMAT", 400);
                    $this->checkPhone();
                }

                if ($this->getYandexMoney()){
                    if(!preg_match('/^41001[0-9]{7,10}$/', $this->getYandexMoney()))
                        throw new EntityException("INVALID_YANDEXMONEY_FORMAT", 400);
                    $this->checkYandexMoney();
                }

                if ($this->getWebMoney()){
                    if(!preg_match('/^[RZUB][0-9]{12}$/', $this->getWebMoney()))
                        throw new EntityException("INVALID_WEBMONEY_FORMAT", 400);
                    $this->checkWebMoney();
                }

                if ($this->getQiwi()){
                    if(!preg_match('/^[+0-9\- ()]*$/', $this->getQiwi()))
                        throw new EntityException("INVALID_QIWI_FORMAT", 400);
                    $this->checkQiwi();
                }

                $this->setName(trim(htmlspecialchars(strip_tags($this->getName()))));
                $this->setSurname(trim(htmlspecialchars(strip_tags($this->getSurname()))));
                $this->setSecondName(trim(htmlspecialchars(strip_tags($this->getSecondName()))));

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

    public function initCounters($data=null)
    {

        if(!$data) {
            $model = $this->getModelClass();

            try {
                $data = $model::instance()->initCounters($this);
            } catch (ModelException $e) {
                throw new EntityException($e->getMessage(), $e->getCode());

            }
        }

        $counters = array();
        foreach(self::$MASK['counters'] as $key)
            if(isset($data['Counter'.$key]))
                $counters[$key] = $data['Counter'.$key];
            elseif(isset($data[$key]))
                $counters[$key] = $data[$key];

        $this->setCounters($counters);

        return $this;
    }

    public function getCounters($key=null)
    {
        if($key){
            if(isset($this->_counters[$key])){
                return $this->_counters[$key];
            } else {
                return false;
            }
        } else
            return $this->_counters;
    }

    public function setCounters($counters, $update=false)
    {
        if($update){
            $this->_counters[$counters] = $update;
        } else {
            $this->_counters = $counters;
        }

        return $this;
    }

    public function initDates($data=null)
    {

        if(!$data) {
            $model = $this->getModelClass();

            try {
                $data = $model::instance()->initDates($this);

            } catch (ModelException $e) {
                throw new EntityException($e->getMessage(), $e->getCode());
            }
        }

        $dates = array();
        foreach(self::$MASK['dates'] as $key)
            if(isset($data[$key]))
                $dates[$key] = $data[$key];

        $this->setDates($dates)
            ->setDateLastMoment($data['Moment'])
            ->setDateLastQuickGame($data['QuickGame'])
            ->setDateLastChance($data['ChanceGame'])
            ->setOnlineTime($data['Ping'])
            ->setAdBlock($data['AdBlockLast'])
            ->setDateAdBlocked($data['AdBlocked'])
            ->setWebSocket($data['WSocket']);

        return $this;
    }

    public function setDates($dates, $update=false)
    {
        if($update){
            $this->_dates[$dates] = $update;
        } else {
            $this->_dates = $dates;
        }

        return $this;
    }

    public function getDates($key = null, $format = null)
    {
        if($key){

            if(isset($this->_dates[$key])){
                if (!is_null($format)) {
                    return date($format, $this->_dates[$key]);
                } else {
                    return $this->_dates[$key];
                }
            } else {
                return false;
            }

        } else
            return $this->_dates;

    }

    public function updateCookieId($cookie)
    {
        $model = $this->getModelClass();

        try {
            $model::instance()->updateCookieId($this,$cookie);
        } catch (ModelException $e) {
            throw new EntityException($e->getMessage(), $e->getCode());

        }

        return $this;
    }

    public function updateIp($ip)
    {

        if(!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
            return $this;

        $ip=sprintf("%u", ip2long($ip));
        $model = $this->getModelClass();

        try {
            if(is_numeric($ip)){
                $model::instance()->updateIp($this,$ip);}
        } catch (ModelException $e) {
            throw new EntityException($e->getMessage(), $e->getCode());

        }

        return $this;
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

    protected function checkPhone()
    {
        $model = $this->getModelClass();

        try {
            $model::instance()->checkPhone($this);
        } catch (ModelException $e) {
            if ($e->getCode() == 403) {
                throw new EntityException("PHONE_BUSY", 400);
            }
            throw new EntityException($e->getMessage(), $e->getCode());

        }

        return true;
    }

    protected function checkQiwi()
    {
        $model = $this->getModelClass();

        try {
            $model::instance()->checkQiwi($this);
        } catch (ModelException $e) {
            if ($e->getCode() == 403) {
                throw new EntityException("QIWI_BUSY", 400);
            }
            throw new EntityException($e->getMessage(), $e->getCode());

        }

        return true;
    }

    protected function checkWebMoney()
    {
        $model = $this->getModelClass();

        try {
            $model::instance()->checkWebMoney($this);
        } catch (ModelException $e) {
            if ($e->getCode() == 403) {
                throw new EntityException("WEBMONEY_BUSY", 400);
            }
            throw new EntityException($e->getMessage(), $e->getCode());

        }

        return true;
    }

    protected function checkYandexMoney()
    {
        $model = $this->getModelClass();

        try {
            $model::instance()->checkYandexMoney($this);
        } catch (ModelException $e) {
            if ($e->getCode() == 403) {
                throw new EntityException("YANDEXMONEY_BUSY", 400);
            }
            throw new EntityException($e->getMessage(), $e->getCode());

        }

        return true;
    }

    protected function validIp()
    {

        $blockedIps=SettingsModel::instance()->getSettings('blockedIps')->getValue();
        if ((in_array($this->getIp(), $blockedIps) || $this->getLastIp() && in_array($this->getLastIp(), $blockedIps))) {
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
        if (in_array($emailDomain, SettingsModel::instance()->getSettings('blockedEmails')->getValue())) {
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

    public function getBalance($forUpdate = false)
    {
        $model = $this->getModelClass();

        try {
            return $model::instance()->getBalance($this, $forUpdate);
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
        $session = new Session();
        $psw=$this->generatePassword();
        $this->setPassword($this->compilePassword($psw))
            ->setAgent($_SERVER['HTTP_USER_AGENT'])
            ->setReferer($session->get('REFERER'));

        parent::create();

        $this->updateIp(Common::getUserIp())
            ->writeLog(array('action'=>'PLAYER_CREATED', 'desc'=>$this->hidePassword($psw), 'status'=>'success'));

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
                    ->setCurrency(LotterySettings::CURRENCY_MONEY)
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
                    ->setCurrency(LotterySettings::CURRENCY_POINT)
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

        if(!$_COOKIE[self::PLAYERID_COOKIE] OR $_COOKIE[self::PLAYERID_COOKIE] != $this->getId() OR $_COOKIE[self::PLAYERID_COOKIE] != $this->getCookieId() OR !$this->getCookieId())
            $this->updateCookieId($_COOKIE[self::PLAYERID_COOKIE]?:$this->getId());

        if(!$_COOKIE[self::PLAYERID_COOKIE])
            setcookie(self::PLAYERID_COOKIE, $this->getId(), time() + self::AUTOLOGIN_COOKIE_TTL, '/');

        $session = new Session();
        $session->set('QuickGameLastDate',($this->getDateLastLogin() < strtotime(date("Y-m-d"))? $this->getDateLastLogin() : time() ));

        $this->setDateLastLogin(time())
            ->setCookieId(($_COOKIE[self::PLAYERID_COOKIE]?:$this->getId()))
            ->setLastIp(Common::getUserIp())
            ->payReferal()
            ->updateIp(Common::getUserIp())
            ->setAgent($_SERVER['HTTP_USER_AGENT'])
            ->update()
            ->writeLogin();

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
                $this->setReferalPaid(1);
            /*
                try {
                    $this->markReferalPaid();
                } catch (EntityException $e) {}
            */
            // add bonuses to inviter and delete invite
            try {
                if(!$this->getReferer() && SettingsModel::instance()->getSettings('bonuses')->getValue('bonus_email_invite')){
                    $invite->getInviter()->addPoints(
                        SettingsModel::instance()->getSettings('bonuses')->getValue('bonus_email_invite'),
                        StaticTextsModel::instance()->setLang($this->getLang())->getText('bonus_email_invite'). $this->getEmail());
                }
                $invite->delete();
            } catch (EntityException $e) {}

            try {
                $this->setInviterId($invite->getInviter()->getId())->updateInvite();
            } catch (EntityException $e) {}

        }

        return $this;
    }

    public function payReferal()
    {

        // add referal points on first login
        if ($this->getReferalId() && !$this->isReferalPaid()) {
            try {
                if(!$this->getReferer() && SettingsModel::instance()->getSettings('bonuses')->getValue('bonus_referal_invite')){
                    $refPlayer = new Player();
                    $refPlayer->setId($this->getReferalId())->fetch();
                    $refPlayer->addPoints(
                        SettingsModel::instance()->getSettings('bonuses')->getValue('bonus_referal_invite'),
                        StaticTextsModel::instance()->setLang($this->getLang())->getText('bonus_referal_invite') .$this->getId());
                }
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
        $this//->setOnline(true)
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
                 ->setQiwi($data['Qiwi'])
                 ->setYandexMoney($data['YandexMoney'])
                 ->setWebMoney($data['WebMoney'])
                 ->setBirthday($data['Birthday'])
                 ->setDateRegistered($data['DateRegistered'])
                 ->setDateLastLogin($data['DateLogined'])
                 ->setDateLastNotice($data['DateNoticed'])
                 //->setDateLastChance($data['ChanceGame'])
                 //->setDateLastMoment($data['Moment'])
                 //->setOnline($data['Online'])
                 //->setOnlineTime($data['OnlineTime'])
                 //->setAdBlock($data['AdBlock'])
                 //->setDateAdBlocked($data['DateAdBlocked'])
                 //->setWebSocket($data['WebSocket'])
                 ->setCountry($data['Country'])
                 ->setLang($data['Lang'])
                 ->setAvatar($data['Avatar'])
                 ->setAgent($data['Agent'])
                 ->setReferer($data['Referer'])
                 ->setVisibility((boolean)$data['Visible'])
                 ->setFavoriteCombination(!empty($data['Favorite']) ? @unserialize($data['Favorite']) : array())
                 ->setPoints($data['Points'])
                 ->setMoney($data['Money'])
                 ->setGamesPlayed($data['GamesPlayed'])
                 ->setInvitesCount($data['InvitesCount'])
                 ->setSocialPostsCount(!empty($data['SocialPostsCount']) ? @unserialize($data['SocialPostsCount']) : array())
                 ->setCookieId($data['CookieId'])
                 ->setIp($data['Ip'])
                 ->setLastIp($data['LastIp'])
                 ->setHash($data['Hash'])
                 ->setValid($data['Valid'])
                 ->setInviterId($data['InviterId'])
                 ->setReferalId($data['ReferalId'])
                 ->setReferalPaid($data['ReferalPaid'])
                 ->setAdditionalData(!empty($data['AdditionalData']) ? @unserialize($data['AdditionalData']) : null);

            if ($data['TicketsFilled']) {
                $this->_isTicketsFilled = $data['TicketsFilled'];
            }

            if (isset($data['Ping'])) {
                $this->initDates($data);
            }

            if (isset($data['MyReferal'])) {
                $this->initCounters($data);
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
